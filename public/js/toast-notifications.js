/**
 * Sistema de Notificações Toast
 * Substitui alerts nativos por notificações elegantes e não intrusivas
 */

class ToastNotification {
    constructor() {
        this.container = null;
        this.init();
    }

    init() {
        // Criar container de notificações se não existir
        if (!document.getElementById('toast-container')) {
            this.container = document.createElement('div');
            this.container.id = 'toast-container';
            this.container.className = 'fixed top-4 right-4 z-[9999] flex flex-col gap-2 max-w-sm w-full';
            this.container.setAttribute('aria-live', 'polite');
            this.container.setAttribute('aria-label', 'Notificações');
            document.body.appendChild(this.container);
        } else {
            this.container = document.getElementById('toast-container');
        }
    }

    /**
     * Mostrar notificação
     * @param {string} message - Mensagem a ser exibida
     * @param {string} type - Tipo: 'success', 'error', 'warning', 'info'
     * @param {number} duration - Duração em ms (0 = não fecha automaticamente)
     */
    show(message, type = 'info', duration = 5000) {
        const toast = document.createElement('div');
        const id = `toast-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
        toast.id = id;
        
        // Configurações por tipo
        const config = {
            success: {
                bg: 'bg-green-500',
                icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>`,
                title: 'Sucesso'
            },
            error: {
                bg: 'bg-red-500',
                icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>`,
                title: 'Erro'
            },
            warning: {
                bg: 'bg-yellow-500',
                icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>`,
                title: 'Aviso'
            },
            info: {
                bg: 'bg-blue-500',
                icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>`,
                title: 'Informação'
            }
        };

        const toastConfig = config[type] || config.info;

        toast.className = `${toastConfig.bg} text-white rounded-lg shadow-2xl p-4 flex items-start gap-3 animate-slide-in-right transform transition-all duration-300 ease-out`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        
        toast.innerHTML = `
            <div class="flex-shrink-0 mt-0.5">
                ${toastConfig.icon}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium">${toastConfig.title}</p>
                <p class="text-sm mt-1 opacity-90">${this.escapeHtml(message)}</p>
            </div>
            <button class="flex-shrink-0 ml-2 text-white/80 hover:text-white transition-colors focus:outline-none focus:ring-2 focus:ring-white/50 rounded" 
                    onclick="window.toastNotification.close('${id}')" 
                    aria-label="Fechar notificação">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        `;

        this.container.appendChild(toast);

        // Auto-fechar após duração especificada
        if (duration > 0) {
            setTimeout(() => {
                this.close(id);
            }, duration);
        }

        return id;
    }

    /**
     * Fechar notificação
     */
    close(id) {
        const toast = document.getElementById(id);
        if (toast) {
            toast.classList.add('animate-slide-out-right', 'opacity-0');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }
    }

    /**
     * Fechar todas as notificações
     */
    closeAll() {
        const toasts = this.container.querySelectorAll('[id^="toast-"]');
        toasts.forEach(toast => {
            this.close(toast.id);
        });
    }

    /**
     * Escapar HTML para prevenir XSS
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Métodos de conveniência
    success(message, duration = 5000) {
        return this.show(message, 'success', duration);
    }

    error(message, duration = 7000) {
        return this.show(message, 'error', duration);
    }

    warning(message, duration = 6000) {
        return this.show(message, 'warning', duration);
    }

    info(message, duration = 5000) {
        return this.show(message, 'info', duration);
    }
}

// Inicializar e disponibilizar globalmente
window.toastNotification = new ToastNotification();

// Adicionar estilos CSS para animações
const style = document.createElement('style');
style.textContent = `
    @keyframes slide-in-right {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slide-out-right {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .animate-slide-in-right {
        animation: slide-in-right 0.3s ease-out;
    }
    
    .animate-slide-out-right {
        animation: slide-out-right 0.3s ease-out;
    }
    
    @media (max-width: 640px) {
        #toast-container {
            top: 1rem;
            right: 1rem;
            left: 1rem;
            max-width: none;
        }
    }
`;
document.head.appendChild(style);

