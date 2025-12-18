@extends('emails.layout')

@section('content')
    <h1>Novo Orçamento Disponível! 📋</h1>
    <p>Olá,</p>
    <p>Um novo orçamento foi atribuído a você e aguarda sua análise.</p>
    
    <div class="attributes">
        <div class="attributes_content">
            <p><strong>ID do Orçamento:</strong> {{ $uniqueId }}</p>
            
            @if($orcamento->service)
                <p><strong>Serviço:</strong> {{ $orcamento->service->nome_servico }}</p>
                <p><strong>Marca:</strong> {{ $orcamento->service->marca ? $orcamento->service->marca->nome : 'N/A' }}</p>
            @endif

            @if($informacoesAdicionais && !empty($informacoesAdicionais))
                <hr style="border: 0; border-top: 1px solid #e7eaec; margin: 10px 0;">
                <p><strong>Informações Adicionais:</strong></p>
                @foreach($informacoesAdicionais as $key => $value)
                    @php
                        $formattedKey = ucfirst(str_replace('_', ' ', $key));
                        $displayValue = is_array($value) ? implode(', ', $value) : $value;
                    @endphp
                    <p><strong>{{ $formattedKey }}:</strong> {{ $displayValue }}</p>
                @endforeach
            @endif
        </div>
    </div>
    
    <p style="text-align: center; color: #777;">Responda no grupo ou acesse o painel para mais detalhes.</p>
@endsection
