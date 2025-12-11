<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Histórico de pagamentos e ferramentas alugadas">
    <meta name="keywords" content="histórico, pagamentos, ferramentas, aluguel, renttool">
    
    <title>renttool - Histórico de Pagamentos</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#10B981',
                        dark: '#1F2937',
                        light: '#F9FAFB'
                    }
                }
            }
        }
    </script>
    <style>
        .tool-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .gradient-bg {
            background: linear-gradient(135deg, #3B82F6 0%, #10B981 100%);
        }
        .tool-icon {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }
        .tool-logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
            border-radius: 12px;
        }
        
        /* Dark Mode Styles */
        .dark-mode {
            background-color: #1a202c;
            color: #e2e8f0;
        }
        .dark-mode .bg-white {
            background-color: #2d3748;
        }
        .dark-mode .text-gray-900 {
            color: #e2e8f0;
        }
        .dark-mode .text-gray-600 {
            color: #a0aec0;
        }
        .dark-mode .text-gray-500 {
            color: #718096;
        }
        .dark-mode .text-gray-400 {
            color: #a0aec0;
        }
        .dark-mode .border-gray-100 {
            border-color: #4a5568;
        }
        .dark-mode .border-gray-200 {
            border-color: #4a5568;
        }
        .dark-mode .border-gray-300 {
            border-color: #4a5568;
        }
        .dark-mode .border-gray-800 {
            border-color: #2d3748;
        }
        .dark-mode .bg-gray-50 {
            background-color: #1a202c;
        }
        .dark-mode input, 
        .dark-mode textarea {
            background-color: #4a5568;
            border-color: #4a5568;
            color: #e2e8f0;
        }
        .dark-mode input:focus {
            border-color: #3B82F6;
        }
        .dark-mode .bg-yellow-50 {
            background-color: #2d3748;
        }
        .dark-mode .bg-blue-50 {
            background-color: #2d3748;
        }
        .dark-mode .bg-blue-100 {
            background-color: #2a4365;
        }
        .dark-mode .bg-green-100 {
            background-color: #22543d;
        }
        .dark-mode .bg-purple-100 {
            background-color: #553c9a;
        }
        .dark-mode .bg-yellow-100 {
            background-color: #634d1f;
        }
        .dark-mode .bg-dark {
            background-color: #1a202c;
        }
        .dark-mode .gradient-bg {
            background: linear-gradient(135deg, #2b6cb0 0%, #059669 100%);
        }
        
        /* Theme Toggle Button */
        .theme-toggle {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .theme-toggle:hover {
            background-color: #edf2f7;
        }
        .dark-mode .theme-toggle:hover {
            background-color: #4a5568;
        }
        
        /* Responsive table */
        @media (max-width: 768px) {
            .table-header {
                display: none;
            }
        }
        
        /* Status badge styling */
        .status-badge {
            display: inline-flex;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            align-items: center;
        }
        
        /* Response modal */
        .response-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        
        .response-modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 0;
            border-radius: 12px;
            width: 95%;
            max-width: 800px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            max-height: 80vh;
            overflow: hidden;
        }
        
        .dark-mode .response-modal-content {
            background-color: #2d3748;
        }
        
        .response-modal-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .dark-mode .response-modal-header {
            border-color: #4a5568;
        }
        
        .response-modal-body {
            padding: 1.5rem;
            max-height: 60vh;
            overflow-y: auto;
        }
        
        .response-modal-close {
            color: #aaa;
            font-size: 1.5rem;
            font-weight: bold;
            cursor: pointer;
        }
        
        .response-modal-close:hover {
            color: #000;
        }
        
        .dark-mode .response-modal-close {
            color: #a0aec0;
        }
        
        .dark-mode .response-modal-close:hover {
            color: #e2e8f0;
        }
        
        /* History table styling */
        .history-table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .history-table th {
            background-color: #f9fafb;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.75rem;
        }
        
        .dark-mode .history-table th {
            background-color: #2d3748;
        }
        
        .history-table tr {
            transition: background-color 0.2s ease;
        }
        
        .history-table tr:hover {
            background-color: #f3f4f6;
        }
        
        .dark-mode .history-table tr:hover {
            background-color: #4a5568;
        }
        
        .history-table td, .history-table th {
            padding: 1rem;
        }
        
        .history-table tbody tr:not(:last-child) td {
            border-bottom: 1px solid #e5e7eb;
        }
        
        .dark-mode .history-table tbody tr:not(:last-child) td {
            border-color: #4a5568;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50 dark:bg-gray-800">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <div class="w-10 h-10 rounded-lg gradient-bg flex items-center justify-center">
                    <span class="text-white font-bold text-xl">R</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">renttool<span class="text-primary">.store</span></h1>
            </div>
            
            <nav class="hidden md:flex space-x-8">
                <a href="/" class="text-gray-600 hover:text-primary font-medium dark:text-gray-300 dark:hover:text-primary">Início</a>
                <a href="/#tools" class="text-gray-600 hover:text-primary font-medium dark:text-gray-300 dark:hover:text-primary">Ferramentas</a>
                <a href="/history" class="text-primary font-medium dark:text-primary">Histórico</a>
                <a href="/#faq" class="text-gray-600 hover:text-primary font-medium dark:text-gray-300 dark:hover:text-primary">FAQ</a>
            </nav>
            
            <div class="flex items-center space-x-4">
                <button id="theme-toggle" class="theme-toggle">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path id="theme-icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Navigation -->
    <div class="md:hidden bg-white shadow-sm dark:bg-gray-800">
        <div class="container mx-auto px-4 py-2 flex justify-around">
            <a href="/" class="text-gray-600 hover:text-primary font-medium py-2 dark:text-gray-300 dark:hover:text-primary">Início</a>
            <a href="/#tools" class="text-gray-600 hover:text-primary font-medium py-2 dark:text-gray-300 dark:hover:text-primary">Ferramentas</a>
            <a href="/history" class="text-primary font-medium py-2 dark:text-primary">Histórico</a>
            <a href="/#faq" class="text-gray-600 hover:text-primary font-medium py-2 dark:text-gray-300 dark:hover:text-primary">FAQ</a>
        </div>
    </div>

    <!-- History Section -->
    <section class="py-16 bg-white dark:bg-gray-800">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4 dark:text-white">Histórico de Pagamentos</h2>
                <p class="text-gray-600 max-w-2xl mx-auto dark:text-gray-300">Veja o histórico de suas ferramentas alugadas e pagamentos realizados.</p>
            </div>
            
            @if($payments->isEmpty())
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-lg dark:bg-yellow-900 dark:border-yellow-700 max-w-3xl mx-auto">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                <span class="font-medium">Nenhum pagamento encontrado.</span> Você ainda não realizou nenhum pagamento ou o cookie de sessão não está disponível.
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto rounded-lg shadow">
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th class="text-left text-gray-500 uppercase tracking-wider dark:text-gray-300">ID</th>
                                <th class="text-left text-gray-500 uppercase tracking-wider dark:text-gray-300">Ferramenta</th>
                                <th class="text-left text-gray-500 uppercase tracking-wider dark:text-gray-300">Valor</th>
                                <th class="text-left text-gray-500 uppercase tracking-wider dark:text-gray-300">Status</th>
                                <th class="text-left text-gray-500 uppercase tracking-wider dark:text-gray-300">Data</th>
                                <th class="text-left text-gray-500 uppercase tracking-wider dark:text-gray-300">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                                <tr>
                                    <td class="font-medium text-gray-900 dark:text-white">{{ $payment->id }}</td>
                                    <td>
                                        @if($payment->tool)
                                            <div class="flex items-center">
                                                @if($payment->tool->photo_patch)
                                                    <img src="{{ asset('storage/' . $payment->tool->photo_patch) }}" alt="{{ $payment->tool->nome }}" class="tool-logo mr-3">
                                                @else
                                                    <div class="tool-icon bg-gray-200 text-gray-500 mr-3">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                                        </svg>
                                                    </div>
                                                @endif
                                                <span class="text-gray-900 dark:text-white">{{ $payment->tool->nome }}</span>
                                            </div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">N/A</span>
                                        @endif
                                    </td>
                                    <td class="text-gray-500 dark:text-gray-300">R${{ number_format($payment->valor, 2, ',', '.') }}</td>
                                    <td>
                                        @if($payment->status === 'pago')
                                            <span class="status-badge bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100">
                                                Pago
                                            </span>
                                        @elseif($payment->status === 'nao pago')
                                            <span class="status-badge bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100">
                                                Não Pago
                                            </span>
                                        @elseif($payment->status === 'refund')
                                            <span class="status-badge bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100">
                                                Reembolsado
                                            </span>
                                        @elseif($payment->status === 'processando')
                                            <span class="status-badge bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-100">
                                                Processando
                                            </span>
                                        @elseif($payment->status === 'success')
                                            <span class="status-badge bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100">
                                                Success
                                            </span>
                                        @else
                                            <span class="status-badge bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-gray-500 dark:text-gray-300">{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($payment->response)
                                            <button onclick="showResponse({{ $payment->id }})" class="text-primary hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                                                Ver Detalhes
                                            </button>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </section>

    <!-- Response Modal -->
    <div id="responseModal" class="response-modal">
        <div class="response-modal-content">
            <div class="response-modal-header">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Detalhes da Resposta</h3>
                <span class="response-modal-close">&times;</span>
            </div>
            <div class="response-modal-body">
                <pre id="responseContent" class="text-sm text-gray-700 dark:text-gray-300 overflow-x-auto"></pre>
            </div>
        </div>
    </div>

    <!-- CTA -->
    <section class="py-16 gradient-bg">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">Precisa de mais ferramentas?</h2>
            <p class="text-blue-100 max-w-2xl mx-auto mb-8 text-lg">Alugue mais ferramentas profissionais de desbloqueio FRP agora mesmo.</p>
            <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                <a href="/#tools" class="bg-white text-primary px-8 py-3 rounded-lg font-bold text-center hover:bg-gray-100 transition shadow-lg dark:bg-gray-200 dark:text-gray-800">
                    Alugar Ferramenta
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-2 mb-6">
                        <div class="w-10 h-10 rounded-lg gradient-bg flex items-center justify-center">
                            <span class="text-white font-bold text-xl">R</span>
                        </div>
                        <h2 class="text-2xl font-bold">renttool<span class="text-primary">.com</span></h2>
                    </div>
                    <p class="text-gray-400">
                        Serviço profissional de aluguel de ferramentas de desbloqueio FRP.
                    </p>
                    <div class="flex space-x-4 mt-4">
                        <a href="#" class="text-gray-400 hover:text-white">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm3 8h-1.35c-.538 0-.65.221-.65.778v1.222h2l-.209 2h-1.791v7h-3v-7h-2v-2h2v-2.308c0-1.769.931-2.692 3.029-2.692h1.971v3z"/>
                            </svg>
                        </a>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Links Rápidos</h3>
                    <ul class="space-y-2">
                        <li><a href="/" class="text-gray-400 hover:text-white">Início</a></li>
                        <li><a href="/#tools" class="text-gray-400 hover:text-white">Ferramentas</a></li>
                        <li><a href="/history" class="text-gray-400 hover:text-white">Histórico</a></li>
                        <li><a href="/#faq" class="text-gray-400 hover:text-white">FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Suporte</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">Central de Ajuda</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Termos de Serviço</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Política de Privacidade</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contato</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li class="flex items-start">
                            <svg class="h-5 w-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                            </svg>
                            <span>suporte@renttool.com</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2023 renttool.com. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Theme toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('theme-toggle');
            const themeIcon = document.getElementById('theme-icon');
            
            // Check for saved theme preference or respect OS preference
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const initialTheme = savedTheme || (prefersDark ? 'dark' : 'light');
            
            if (initialTheme === 'dark') {
                document.body.classList.add('dark-mode');
                themeIcon.setAttribute('d', 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z');
            }
            
            themeToggle.addEventListener('click', function() {
                document.body.classList.toggle('dark-mode');
                
                if (document.body.classList.contains('dark-mode')) {
                    themeIcon.setAttribute('d', 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z');
                    localStorage.setItem('theme', 'dark');
                } else {
                    themeIcon.setAttribute('d', 'M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z');
                    localStorage.setItem('theme', 'light');
                }
            });
            
            // Response modal functionality
            const modal = document.getElementById("responseModal");
            const closeModal = document.getElementsByClassName("response-modal-close")[0];
            
            window.showResponse = function(paymentId) {
                // In a real implementation, you would fetch the response data from the server
                // For now, we'll just show a placeholder
                const responseContent = document.getElementById("responseContent");
                responseContent.textContent = "Detalhes da resposta para o pagamento ID: " + paymentId;
                modal.style.display = "block";
            }
            
            closeModal.onclick = function() {
                modal.style.display = "none";
            }
            
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        });
    </script>
</body>
</html>