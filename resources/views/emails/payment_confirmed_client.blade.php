@extends('emails.layout')

@section('content')
    <h1>Pagamento Aprovado! ✅</h1>
    <p>Olá,</p>
    <p>Temos ótimas notícias! Seu pagamento no valor de <strong>R$ {{ number_format($payment->valor, 2, ',', '.') }}</strong> foi aprovado com sucesso.</p>
    <p>Uma sala de chat exclusiva foi criada para você se comunicar diretamente com o técnico.</p>
    
    <div class="attributes">
        <div class="attributes_content">
            <p><strong>Link de Acesso:</strong> <a href="{{ route('chat.login', $chatRoom->room_code) }}">Clique aqui para acessar</a></p>
            <p><strong>Senha:</strong> {{ $chatRoom->client_password }}</p>
        </div>
    </div>

    <p>Você pode clicar no botão abaixo para ir direto para o chat:</p>
    
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <a href="{{ route('chat.login', $chatRoom->room_code) }}" class="button button--green" target="_blank">Acessar Chat</a>
            </td>
        </tr>
    </table>
    
    <p>Obrigado por escolher nossos serviços!</p>
@endsection
