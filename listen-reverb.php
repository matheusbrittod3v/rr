#!/usr/bin/env php
<?php

/**
 * Script para testar recepção de eventos do Reverb
 * 
 * Uso:
 *   php listen-reverb.php [canal] [app_id] [app_key] [app_secret] [host] [port] [scheme]
 * 
 * Exemplos:
 *   php listen-reverb.php
 *   php listen-reverb.php chat-room.test
 *   php listen-reverb.php chat-room.test 123456 your-app-key your-app-secret localhost 8080 http
 */

require __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Reverb\Certificate;
use Laravel\Reverb\Client;
use Laravel\Reverb\Contracts\Logger;
use Laravel\Reverb\Loggers\NullLogger;
use Laravel\Reverb\Protocols\Pusher\Contracts\ChannelManager;
use Laravel\Reverb\Protocols\Pusher\EventDispatcher;
use Laravel\Reverb\Protocols\Pusher\Managers\ArrayChannelManager;
use Laravel\Reverb\ServerProvider;
use React\EventLoop\Loop;
use React\Socket\SocketServer;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;

$appId = $argv[2] ?? $_ENV['REVERB_APP_ID'] ?? 'reverb';
$appKey = $argv[3] ?? $_ENV['REVERB_APP_KEY'] ?? 'reverb';
$appSecret = $argv[4] ?? $_ENV['REVERB_APP_SECRET'] ?? 'reverb';
$host = $argv[5] ?? $_ENV['REVERB_HOST'] ?? '127.0.0.1';
$port = $argv[6] ?? $_ENV['REVERB_PORT'] ?? '8080';
$scheme = $argv[7] ?? $_ENV['REVERB_SCHEME'] ?? 'http';
$channel = $argv[1] ?? 'chat-room.test';

$output = new ConsoleOutput;
$logger = new NullLogger;

$output->writeln(sprintf('> <fg=yellow>Listening for events on the `%s` channel...</>', $channel));
$output->writeln('');

$loop = Loop::get();

$server = new SocketServer("$host:$port", [
    'tcp' => ['backlog' => 2048],
], $loop);

$serverProvider = new class($appId, $appKey, $appSecret, $scheme) extends ServerProvider
{
    protected $appId;
    protected $appKey;
    protected $appSecret;
    protected $scheme;

    public function __construct($appId, $appKey, $appSecret, $scheme)
    {
        $this->appId = $appId;
        $this->appKey = $appKey;
        $this->appSecret = $appSecret;
        $this->scheme = $scheme;
    }

    public function id(): string
    {
        return $this->appId;
    }

    public function secret(): string
    {
        return $this->appSecret;
    }

    public function key(): string
    {
        return $this->appKey;
    }

    public function scheme(): string
    {
        return $this->scheme;
    }
};

$client = new Client(
    $serverProvider,
    new ArrayChannelManager,
    new EventDispatcher,
    $logger,
    $loop
);

$client->handle($server);

$client->channelManager()->for($appId)->subscribe($channel)->watch(function ($event) use ($output, $channel) {
    $output->writeln(sprintf(
        '[%s] Received %s event on the `%s` channel.',
        date('Y-m-d H:i:s'),
        $event['event'],
        $channel
    ));

    if (isset($event['data'])) {
        $data = json_decode($event['data'], true);

        if (json_last_error() === JSON_ERROR_NONE) {
            $output->writeln(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            $output->writeln($event['data']);
        }
    }

    $output->writeln('');
});

$loop->run();