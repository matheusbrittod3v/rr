<div class="p-6" 
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
            
            try {
                const response = await fetch('/whatsapp/qr-code', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']')?.getAttribute('content') || '{{ csrf_token() }}'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.qrCode = data.qrCode;
                    this.startPolling();
                } else {
                    this.error = data.message || 'Falha ao obter QR Code';
                }
            } catch (err) {
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
                
                if (data.success) {
                    this.connectionStatus = data.status;
                    
                    if (data.status === 'connected') {
                        this.stopPolling();
                        window.location.reload();
                    }
                }
            } catch (err) {
                console.log('💥 Erro ao verificar status:', err);
            }
        },
        
        startPolling() {
            this.pollingInterval = setInterval(() => {
                this.checkConnectionStatus();
            }, 3000);
        },
        
        stopPolling() {
            if (this.pollingInterval) {
                clearInterval(this.pollingInterval);
                this.pollingInterval = null;
            }
        }
     }"
     x-init="fetchQrCode()"
     x-on:unload.window="stopPolling()"
>
    <!-- Header -->
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-green-400 to-green-600 rounded-full mb-4">
            <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
            </svg>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-2">Connect WhatsApp</h3>
        <p class="text-gray-600">Scan the QR code to link your WhatsApp</p>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="flex flex-col items-center justify-center py-12">
        <div class="relative">
            <div class="w-16 h-16 border-4 border-green-200 border-t-green-600 rounded-full animate-spin"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-8 h-8 bg-green-100 rounded-full"></div>
            </div>
        </div>
        <p class="mt-4 text-gray-600 font-medium">Loading QR Code...</p>
        <p class="text-sm text-gray-500 mt-1">This may take a few seconds</p>
    </div>

    <!-- Error State -->
    <div x-show="!loading && error" class="py-8">
        <div class="bg-gradient-to-br from-red-50 to-red-100 border-2 border-red-300 rounded-2xl p-6 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-red-200 rounded-full mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <p class="text-red-800 font-bold text-lg mb-2">Unable to Load QR Code</p>
            <p class="text-red-700 mb-4" x-text="error"></p>
            <button @click="fetchQrCode()" class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Try Again
            </button>
        </div>
    </div>

    <!-- QR Code Display -->
    <div x-show="!loading && !error && qrCode">
        <!-- Instructions Card -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-5 mb-6">
            <h4 class="font-bold text-gray-800 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                How to Connect
            </h4>
            <ol class="space-y-2 text-sm text-gray-700">
                <li class="flex items-start">
                    <span class="flex-shrink-0 w-6 h-6 bg-indigo-600 text-white rounded-full flex items-center justify-center text-xs font-bold mr-3 mt-0.5">1</span>
                    <span>Open <strong>WhatsApp</strong> on your phone</span>
                </li>
                <li class="flex items-start">
                    <span class="flex-shrink-0 w-6 h-6 bg-indigo-600 text-white rounded-full flex items-center justify-center text-xs font-bold mr-3 mt-0.5">2</span>
                    <span>Tap <strong>Menu</strong> → <strong>Linked Devices</strong></span>
                </li>
                <li class="flex items-start">
                    <span class="flex-shrink-0 w-6 h-6 bg-indigo-600 text-white rounded-full flex items-center justify-center text-xs font-bold mr-3 mt-0.5">3</span>
                    <span>Tap <strong>Link a Device</strong></span>
                </li>
                <li class="flex items-start">
                    <span class="flex-shrink-0 w-6 h-6 bg-indigo-600 text-white rounded-full flex items-center justify-center text-xs font-bold mr-3 mt-0.5">4</span>
                    <span>Point your phone camera at this screen</span>
                </li>
            </ol>
        </div>

        <!-- QR Code -->
        <div class="flex justify-center mb-6">
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-br from-green-400 to-green-600 rounded-3xl blur-xl opacity-30"></div>
                <div class="relative bg-white p-6 rounded-3xl shadow-2xl">
                    <img 
                        :src="`https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(qrCode)}`"
                        alt="WhatsApp QR Code"
                        class="rounded-2xl w-72 h-72"
                    >
                </div>
            </div>
        </div>
        
        <!-- Connection Status -->
        <div x-show="connectionStatus" class="p-4 rounded-xl text-center" :class="{
            'bg-green-100 border-2 border-green-300': connectionStatus === 'connected',
            'bg-yellow-100 border-2 border-yellow-300': connectionStatus === 'qr_code',
            'bg-red-100 border-2 border-red-300': connectionStatus === 'disconnected'
        }">
            <p class="font-semibold flex items-center justify-center">
                <span class="mr-2">Status:</span>
                <span x-text="connectionStatus" :class="{
                    'text-green-700': connectionStatus === 'connected',
                    'text-yellow-700': connectionStatus === 'qr_code',
                    'text-red-700': connectionStatus === 'disconnected'
                }"></span>
            </p>
        </div>
    </div>
</div>