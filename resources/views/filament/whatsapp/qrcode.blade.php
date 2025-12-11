<div class="p-4 text-center" 
     x-data="{
        qrCode: null,
        loading: false,
        error: null,
        connectionStatus: null,
        pollingInterval: null,
        
        async fetchQrCode() {
            console.log('🔍 Iniciando fetchQrCode para instância única');
            
            this.loading = true;
            this.error = null;
            this.qrCode = null;
            
            console.log('📡 Buscando QR Code para instância única');
            
            try {
                const response = await fetch('/whatsapp/qr-code', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']')?.getAttribute('content') || '{{ csrf_token() }}'
                    }
                });
                
                const data = await response.json();
                console.log('📥 Resposta recebida:', data);
                
                if (data.success) {
                    this.qrCode = data.qrCode;
                    console.log('✅ QR Code obtido com sucesso');
                    // Inicia o polling para verificar o status da conexão
                    this.startPolling();
                } else {
                    this.error = data.message || 'Falha ao obter QR Code';
                    console.log('❌ Erro ao obter QR Code:', this.error);
                }
            } catch (err) {
                console.log('💥 Erro na requisição:', err);
                this.error = 'Erro ao conectar com o servidor: ' + err.message;
            } finally {
                this.loading = false;
            }
        },
        
        async checkConnectionStatus() {
            try {
                const response = await fetch('/whatsapp/connection-status', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']')?.getAttribute('content') || '{{ csrf_token() }}'
                    }
                });
                
                const data = await response.json();
                console.log('🔄 Status da conexão:', data);
                
                if (data.success) {
                    this.connectionStatus = data.status;
                    
                    // Se estiver conectado, pare o polling e mostre mensagem
                    if (data.status === 'connected') {
                        this.stopPolling();
                        // Recarrega a página para atualizar o status
                        window.location.reload();
                    }
                } else {
                    console.log('❌ Erro ao verificar status:', data.message);
                }
            } catch (err) {
                console.log('💥 Erro ao verificar status:', err);
            }
        },
        
        startPolling() {
            console.log('⏱️ Iniciando polling de status');
            // Verifica o status a cada 3 segundos
            this.pollingInterval = setInterval(() => {
                this.checkConnectionStatus();
            }, 3000);
        },
        
        stopPolling() {
            console.log('⏹️ Parando polling de status');
            if (this.pollingInterval) {
                clearInterval(this.pollingInterval);
                this.pollingInterval = null;
            }
        }
     }"
     x-init="fetchQrCode()"
     x-on:unload.window="stopPolling()"
>
    <p class="text-lg mb-3">Conecte seu WhatsApp</p>

    <div x-show="loading" class="flex justify-center py-8">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900"></div>
        <p class="ml-3 self-center">Carregando QR Code...</p>
    </div>

    <div x-show="!loading && error" class="text-center py-4">
        <p class="text-red-500 font-bold mb-2">Erro ao carregar QR Code</p>
        <p class="text-gray-700 mb-4" x-text="error"></p>
        <button @click="fetchQrCode()" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
            Tentar Novamente
        </button>
    </div>

    <div x-show="!loading && !error && qrCode">
        <div class="mb-4 text-gray-700">
            <p class="mb-2">Escaneie o QR Code abaixo com o seu WhatsApp:</p>
        </div>
        
        <div class="flex justify-center py-4">
            <img 
                :src="`https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(qrCode)}`"
                alt="QR Code para WhatsApp"
                class="border-4 border-gray-200 rounded-lg shadow-lg"
                style="max-width: 300px; height: auto;"
                onload="console.log('✅ Imagem QR Code carregada com sucesso')"
                onerror="console.log('❌ Falha ao carregar imagem QR Code')"
            >
        </div>
        
        <div class="mt-4 text-sm text-gray-600">
            <p><strong>Instruções:</strong></p>
            <ol class="list-decimal list-inside text-left mt-2 space-y-1">
                <li>Abra o WhatsApp no seu celular</li>
                <li>Toque em "Dispositivos conectados" ou "Linked Devices"</li>
                <li>Selecione "Conectar um dispositivo"</li>
                <li>Aponte a câmera do seu celular para o QR Code acima</li>
            </ol>
        </div>
        
        <div class="mt-4 p-3 bg-blue-50 rounded-lg" x-show="connectionStatus">
            <p class="font-medium">Status da conexão: 
                <span x-text="connectionStatus" 
                      :class="{
                          'text-green-600': connectionStatus === 'connected',
                          'text-yellow-600': connectionStatus === 'qr_code',
                          'text-red-600': connectionStatus === 'disconnected'
                      }">
                </span>
            </p>
        </div>
    </div>

    <div x-show="!loading && !error && !qrCode">
        <p class="text-gray-500">Nenhum QR disponível no momento.</p>
        
        <div class="mt-4 text-sm text-gray-600">
            <p><strong>Instruções:</strong></p>
            <ol class="list-decimal list-inside text-left mt-2 space-y-1">
                <li>Verifique se a API do WhatsApp está configurada corretamente</li>
                <li>Tente gerar o QR Code novamente</li>
            </ol>
        </div>
    </div>
</div>