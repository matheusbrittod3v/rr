@extends('emails.layout')

@section('content')
    <h1>Novo Serviço Confirmado! 🚀</h1>
    <p>Olá,</p>
    <p>Um cliente realizou o pagamento e uma nova sala de chat foi criada para iniciar o atendimento.</p>
    
    <p><strong>Detalhes do Serviço:</strong></p>
    <div class="attributes">
        <div class="attributes_content">
             @if($orcamento->service)
                <p><strong>Marca:</strong> {{ $orcamento->service->marca ? $orcamento->service->marca->nome : 'N/A' }}</p>
                <p><strong>Serviço:</strong> {{ $orcamento->service->nome_servico }}</p>
                
                @if(!empty($orcamento->informacoes_adicionais) && is_array($orcamento->informacoes_adicionais))
                    <hr style="border: 0; border-top: 1px solid #e7eaec; margin: 10px 0;">
                    @foreach($orcamento->informacoes_adicionais as $key => $value)
                         @if(is_string($value) || is_numeric($value))
                            <p><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</p>
                         @endif
                    @endforeach
                @endif
             @else
                <p>Detalhes do serviço não disponíveis.</p>
             @endif
        </div>
    </div>

    <p><strong>Dados de Acesso ao Chat:</strong></p>
    <div class="attributes">
        <div class="attributes_content">
            <p><strong>Senha de Acesso:</strong> {{ $chatRoom->provider_password }}</p>
        </div>
    </div>
    
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <a href="{{ route('chat.login', $chatRoom->room_code) }}" class="button button--green" target="_blank">Acessar Sala de Chat</a>
            </td>
        </tr>
    </table>
    
    <p>Por favor, acesse o chat o mais breve possível para alinhar os detalhes com o cliente.</p>
@endsection
