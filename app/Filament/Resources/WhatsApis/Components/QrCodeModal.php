<?php

namespace App\Filament\Resources\WhatsApis\Components;

use Filament\Forms\Components\Field;
use Illuminate\Support\HtmlString;

class QrCodeModal extends Field
{
    protected string $view = 'filament.forms.components.qr-code-modal';

    protected ?string $qrCodeBase64 = null;

    protected ?string $instanceName = null;

    public function qrCodeBase64(?string $qrCodeBase64): static
    {
        $this->qrCodeBase64 = $qrCodeBase64;

        return $this;
    }

    public function getInstanceName(): ?string
    {
        return $this->instanceName;
    }

    public function instanceName(?string $instanceName): static
    {
        $this->instanceName = $instanceName;

        return $this;
    }

    public function getQrCodeBase64(): ?string
    {
        return $this->qrCodeBase64;
    }

    public function getQrCodeImage(): ?HtmlString
    {
        if (!$this->qrCodeBase64) {
            return null;
        }

        $imageHtml = "<img src='data:image/png;base64,{$this->qrCodeBase64}' alt='QR Code' style='max-width: 200px; height: auto;' />";
        return new HtmlString($imageHtml);
    }
}