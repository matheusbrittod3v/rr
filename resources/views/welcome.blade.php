<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Conectamos clientes aos melhores profissionais de desbloqueio. Solicite orçamentos personalizados e receba serviços seguros e confiáveis.">
    <meta name="keywords" content="desbloqueio de celulares, serviços de unlock, profissionais de desbloqueio, orçamento personalizado, renttool">
    
    <title>renttool - Conectando Clientes aos Melhores Profissionais de Desbloqueio</title>

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
                        primary: '#3B82F6',      // Blue Primary
                        secondary: '#2563EB',    // Blue
                        accent: '#0EA5E9',       // Sky Blue
                        dark: '#0F172A',         // Slate 900
                        light: '#F8FAFC'         // Slate 50
                    }
                }
            }
        }    </script>
    <style>
        /* Enhanced Card Hover Effects */
        .tool-card, .feature-card, .marca-card, .service-card {
            transition: all var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
            transform: translateY(0);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(0, 0, 0, 0.03);
        }
        
        .tool-card:hover, .feature-card:hover, .marca-card:hover, .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(59, 130, 246, 0.2);
        }
        
        /* CSS Custom Properties */
        :root {
            --primary-gradient: linear-gradient(135deg, #3B82F6 0%, #2563EB 50%, #0EA5E9 100%);
            --card-gradient: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(37, 99, 235, 0.1) 100%);
            --transition-speed: 0.3s;
        }
        
        /* Background Styles */
        .hero-pattern {
            background: 
                radial-gradient(circle at 10% 20%, rgba(59, 130, 246, 0.15) 0%, transparent 20%), 
                radial-gradient(circle at 90% 80%, rgba(37, 99, 235, 0.15) 0%, transparent 20%),
                radial-gradient(circle at 50% 30%, rgba(14, 165, 233, 0.1) 0%, transparent 30%);
        }
        
        .gradient-bg {
            background: var(--primary-gradient);
            background-size: 200% 200%;
            animation: gradientShift 8s ease infinite;
        }
        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        /* Component Styles */
        /* Tool Icons */
        .tool-icon {
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            background: var(--card-gradient);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(59, 130, 246, 0.1);
        }
        
        .tool-logo {
            width: 70px;
            height: 70px;
            object-fit: contain;
            border-radius: 16px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        /* Modals */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .modal.show {
            opacity: 1;
        }
        
        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 0;
            border-radius: 20px;
            width: 95%;
            max-width: 900px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            display: flex;
            flex-direction: row;
            overflow: hidden;
            transform: translateY(20px);
            transition: transform 0.3s ease;
        }
        
        .modal.show .modal-content {
            transform: translateY(0);
        }
        
        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.2s ease;
        }
        
        .close:hover {
            color: #000;
        }
        
        .modal.show {
            opacity: 1;
        }
        
        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 0;
            border-radius: 20px;
            width: 95%;
            max-width: 900px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            display: flex;
            flex-direction: row;
            overflow: hidden;
            transform: translateY(20px);
            transition: transform 0.3s ease;
        }
        
        .modal.show .modal-content {
            transform: translateY(0);
        }
        
        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.2s ease;
        }
        
        .close:hover {
            color: #000;
        }
        
        /* Enhanced Dark Mode Styles */
        .dark-mode {
            background-color: #0F172A;
            color: #e2e8f0;
        }
        
        .dark-mode .bg-white {
            background-color: #1E293B;
        }
        
        .dark-mode .text-slate-900 {
            color: #f1f5f9;
        }
        
        .dark-mode .text-slate-600 {
            color: #94a3b8;
        }
        
        .dark-mode .text-slate-500 {
            color: #64748b;
        }
        
        .dark-mode .text-slate-400 {
            color: #94a3b8;
        }
        
        .dark-mode .border-slate-100 {
            border-color: #334155;
        }
        
        .dark-mode .border-slate-200 {
            border-color: #334155;
        }
        
        .dark-mode .border-slate-300 {
            border-color: #475569;
        }
        
        .dark-mode .border-slate-800 {
            border-color: #1E293B;
        }
        
        .dark-mode .bg-slate-50 {
            background-color: #0F172A;
        }
        
        .dark-mode .modal-content {
            background-color: #1E293B;
        }
        
        .dark-mode .close {
            color: #94a3b8;
        }
        
        .dark-mode .close:hover {
            color: #e2e8f0;
        }
        
        .dark-mode input, 
        .dark-mode textarea {
            background-color: #334155;
            border-color: #475569;
            color: #e2e8f0;
        }
        
        .dark-mode input:focus {
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        
        .dark-mode .bg-yellow-50 {
            background-color: #1E293B;
        }
        
        .dark-mode .bg-blue-50 {
            background-color: #1E293B;
        }
        
        .dark-mode .bg-blue-100 {
            background-color: #1e3a8a;
        }
        
        .dark-mode .bg-green-100 {
            background-color: #14532d;
        }
        
        .dark-mode .bg-blue-100 {
            background-color: #4c1d95;
        }
        
        .dark-mode .bg-yellow-100 {
            background-color: #713f12;
        }
        
        .dark-mode .bg-dark {
            background-color: #0F172A;
        }
        
        .dark-mode .gradient-bg {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #0ea5e9 100%);
        }
        
        /* Theme Toggle */
        .theme-toggle {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .theme-toggle:hover {
            background-color: #edf2f7;
            transform: rotate(15deg);
        }
        
        .dark-mode .theme-toggle:hover {
            background-color: #334155;
            transform: rotate(15deg);
        }
        
        /* Responsive Styles */
        @media (max-width: 768px) {
            .modal-content {
                flex-direction: column; /* On mobile, use vertical layout */
                margin: 2% auto;
                width: 95%;
                max-width: 95%;
                max-height: 90vh;
                overflow-y: auto;
                border-radius: 16px;
            }
            
            .modal-content > div:first-child,
            .modal-content > div:last-child {
                width: 100%;
                padding: 20px;
                max-height: none;
            }
            
            .grid-cols-1.md\:grid-cols-2.lg\:grid-cols-3 {
                grid-template-columns: repeat(1, minmax(0, 1fr));
            }
            
            .grid-cols-1.md\:grid-cols-3 {
                grid-template-columns: repeat(1, minmax(0, 1fr));
            }
            
            .grid-cols-1.md\:grid-cols-2.lg\:grid-cols-4 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            
            .grid-cols-1.md\:grid-cols-4 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            
            .text-5xl {
                font-size: 2.5rem;
            }
            
            .text-3xl {
                font-size: 1.875rem;
            }
            
            .text-2xl {
                font-size: 1.5rem;
            }
            
            .flex-col.sm\:flex-row {
                flex-direction: column;
            }
            
            .space-y-4.sm\:space-y-0.sm\:space-x-4 > * {
                margin-right: 0;
                margin-bottom: 1rem;
            }
            
            .space-y-4.sm\:space-y-0.sm\:space-x-4 {
                margin-bottom: 1rem;
            }
            
            .hidden.md\:flex {
                display: none;
            }
            
            .tool-card {
                max-width: 100%;
            }
            
            /* Mobile menu improvements */
            .mobile-menu {
                display: flex;
                flex-direction: column;
                background-color: white;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                padding: 1rem;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
                z-index: 100;
                border-radius: 0 0 12px 12px;
            }
            
            .dark-mode .mobile-menu {
                background-color: #2d3748;
            }
        }
        
        @media (max-width: 480px) {
            .modal-content {
                flex-direction: column;
                margin: 5% auto;
                border-radius: 12px;
            }
            
            .grid-cols-1.md\:grid-cols-2.lg\:grid-cols-4 {
                grid-template-columns: repeat(1, minmax(0, 1fr));
            }
            
            .grid-cols-1.md\:grid-cols-4 {
                grid-template-columns: repeat(1, minmax(0, 1fr));
            }
            
            .tool-logo {
                width: 50px;
                height: 50px;
            }
            
            h2 {
                font-size: 1.5rem;
            }
            
            h3 {
                font-size: 1.25rem;
            }
            
            /* Improved mobile padding */
            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }
        
        /* Adiciona rolagem à página quando o modal está aberto */
        .modal-open {
            overflow: hidden;
        }
        
        .modal-open .modal {
            overflow-y: auto;
            padding-top: 20px;
        }
        
        /* Service Modal Specific Styles */
        @media (max-width: 768px) {
            .modal-content {
                flex-direction: column;
            }
            
            .modal-content > div:first-child,
            .modal-content > div:last-child {
                width: 100%;
            }
        }
        
        /* QR Code Modal Specific Styles */
        .qr-code-img {
            max-width: 200px;
            height: auto;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px;
            background-color: #fff;
            display: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .copy-button {
            transition: all 0.2s ease;
        }
        
        .copy-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .copied-message {
            transition: opacity 0.3s ease;
        }
        
        /* Utilities */
        .spinner {
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            border-top: 4px solid #3B82F6;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Buttons */
        .btn-primary, .btn-secondary {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            letter-spacing: 0.025em;
        }
        
        .btn-primary {
            box-shadow: 0 4px 6px rgba(59, 130, 246, 0.2);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px rgba(59, 130, 246, 0.3);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        .btn-secondary {
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
        }
        
        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px rgba(37, 99, 235, 0.3);
        }
        
        .btn-secondary:active {
            transform: translateY(0);
        }
        
        /* Forms */
        .form-input {
            transition: all 0.2s ease;
            border-width: 1px;
        }
        
        .form-input:focus {
            border-color: #2563EB;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        }
        
        /* Improved card styles */
        .feature-card {
            transition: all 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
        }
        
        /* Improved section spacing */
        section {
            scroll-margin-top: 80px;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #c7d2fe;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #3B82F6;
        }
        
        .dark-mode ::-webkit-scrollbar-track {
            background: #1e293b;
        }
        
        .dark-mode ::-webkit-scrollbar-thumb {
            background: #4c1d95;
        }
        
        .dark-mode ::-webkit-scrollbar-thumb:hover {
            background: #3B82F6;
        }
    </style>
</head>
<body class="bg-slate-50 font-sans">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50 dark:bg-slate-800">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <div class="w-10 h-10 rounded-xl gradient-bg flex items-center justify-center">
                    <span class="text-white font-bold text-xl">TT</span>
                </div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">renttool<span class="text-primary">.store</span></h1>
            </div>
            
            <!-- Desktop Navigation -->
            <nav class="hidden md:flex space-x-8">
                <a href="#services" class="text-slate-600 hover:text-primary font-medium dark:text-slate-300 dark:hover:text-primary">Serviços</a>
                <a href="#how-it-works" class="text-slate-600 hover:text-primary font-medium dark:text-slate-300 dark:hover:text-primary">Como Funciona</a>
                <a href="#benefits" class="text-slate-600 hover:text-primary font-medium dark:text-slate-300 dark:hover:text-primary">Benefícios</a>
                <a href="#faq" class="text-slate-600 hover:text-primary font-medium dark:text-slate-300 dark:hover:text-primary">FAQ</a>
            </nav>
            
            <div class="flex items-center space-x-4">
                <button id="theme-toggle" class="theme-toggle">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path id="theme-icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>

                <a href="#" id="refundLink" class="hidden md:inline-block bg-yellow-500 text-white px-4 py-2 rounded-lg font-medium hover:bg-yellow-600 transition btn-secondary">
                    Reembolso
                </a>
                <a href="#services" class="hidden md:inline-block bg-primary text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition btn-primary">
                    Solicitar Serviço
                </a>
                
                <!-- Mobile menu button -->
                <button id="mobile-menu-button" class="md:hidden text-slate-500 dark:text-slate-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Mobile Navigation -->
        <div id="mobile-menu" class="mobile-menu hidden md:hidden">
            <div class="flex flex-col space-y-4 pb-4">
                <a href="#services" class="text-slate-600 hover:text-primary font-medium dark:text-slate-300 dark:hover:text-primary">Serviços</a>
                <a href="#how-it-works" class="text-slate-600 hover:text-primary font-medium dark:text-slate-300 dark:hover:text-primary">Como Funciona</a>
                <a href="#benefits" class="text-slate-600 hover:text-primary font-medium dark:text-slate-300 dark:hover:text-primary">Benefícios</a>
                <a href="#faq" class="text-slate-600 hover:text-primary font-medium dark:text-slate-300 dark:hover:text-primary">FAQ</a>
                

                <a href="#" id="refundLinkMobile" class="bg-yellow-500 text-white px-4 py-2 rounded-lg font-medium hover:bg-yellow-600 transition btn-secondary text-center">
                    Reembolso
                </a>
                <a href="#services" class="bg-primary text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition btn-primary text-center">
                    Solicitar Serviço
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="py-16 md:py-24 gradient-bg hero-pattern">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-6">Conectando Clientes aos Melhores Profissionais de Desbloqueio</h1>
            <p class="text-xl text-blue-100 max-w-2xl mx-auto mb-10">Solicite orçamentos personalizados e receba serviços seguros e confiáveis de profissionais especializados.</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="#services" class="bg-white text-primary px-8 py-4 rounded-lg font-bold text-center hover:bg-slate-100 transition btn-primary shadow-lg">
                    Ver Serviços
                </a>
                <a href="#how-it-works" class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-lg font-bold text-center hover:bg-white hover:text-primary transition btn-primary">
                    Como Funciona
                </a>
            </div>
        </div>
    </section>

    <!-- Marcas Section (Initial View) -->
    <section id="services" class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div id="marcasView" class="marcas-view">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4 dark:text-white">Escolha a Marca</h2>
                    <p class="text-slate-600 max-w-2xl mx-auto dark:text-slate-300">Selecione a marca do seu dispositivo para ver os serviços disponíveis.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    @php
                        // Group services by marca_id and get unique marca objects
                        $marcas = $services->groupBy('marca_id')->map(function($items) {
                            return $items->first()->marca;
                        })->filter(function($marca) {
                            // Filter out any null marca entries
                            return $marca !== null;
                        })->unique('id')->values();
                    @endphp
                    
                    @foreach($marcas as $marca)
                    <!-- Marca Card -->
                    <div class="marca-card bg-white rounded-2xl shadow-sm p-6 border border-slate-100 transition-all duration-300 feature-card cursor-pointer hover:shadow-lg hover:border-primary" data-marca-id="{{ $marca->id }}">
                        <div class="text-center">
                            @if($marca->logo_path)
                                <img src="{{ asset('storage/' . $marca->logo_path) }}" alt="{{ $marca->nome }}" class="w-24 h-24 object-contain mx-auto mb-4 rounded-xl">
                            @else
                                <div class="w-24 h-24 bg-slate-100 rounded-xl mx-auto mb-4 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                            <h3 class="font-bold text-lg text-slate-900 dark:text-white">{{ $marca->nome }}</h3>
                            <p class="text-slate-600 text-sm dark:text-slate-300 mt-2">Clique para ver serviços</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Services View (Hidden Initially) -->
            <div id="servicosView" class="servicos-view hidden">
                <div class="text-center mb-12">
                    <button id="backToMarcas" class="inline-block mb-4 bg-slate-300 text-slate-700 px-4 py-2 rounded-lg font-medium hover:bg-slate-400 transition">
                        ← Voltar para Marcas
                    </button>
                    <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4 dark:text-white" id="servicosTitle">Serviços</h2>
                    <p class="text-slate-600 max-w-2xl mx-auto dark:text-slate-300">Selecione o serviço desejado.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="servicosGrid">
                    <!-- Serviços serão preenchidos dinamicamente -->
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section id="how-it-works" class="py-16 bg-slate-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4 dark:text-white">Como Funciona o renttool</h2>
                <p class="text-slate-600 max-w-2xl mx-auto dark:text-slate-300">Processo simples para conectar você aos melhores profissionais de desbloqueio em minutos.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="text-center p-6 feature-card bg-white rounded-2xl shadow-sm dark:bg-slate-800">
                    <div class="w-16 h-16 rounded-2xl bg-primary flex items-center justify-center mx-auto mb-6">
                        <span class="text-white font-bold text-xl">1</span>
                    </div>
                    <h3 class="font-bold text-xl text-slate-900 mb-3 dark:text-white">Escolha o Serviço</h3>
                    <p class="text-slate-600 dark:text-slate-300">Navegue em nossa seleção de serviços de desbloqueio e selecione o que você precisa.</p>
                </div>
                
                <div class="text-center p-6 feature-card bg-white rounded-2xl shadow-sm dark:bg-slate-800">
                    <div class="w-16 h-16 rounded-2xl bg-primary flex items-center justify-center mx-auto mb-6">
                        <span class="text-white font-bold text-xl">2</span>
                    </div>
                    <h3 class="font-bold text-xl text-slate-900 mb-3 dark:text-white">Solicite Orçamento</h3>
                    <p class="text-slate-600 dark:text-slate-300">Clique em "Solicitar Orçamento" e informe seus dados para receber propostas personalizadas.</p>
                </div>
                
                <div class="text-center p-6 feature-card bg-white rounded-2xl shadow-sm dark:bg-slate-800">
                    <div class="w-16 h-16 rounded-2xl bg-primary flex items-center justify-center mx-auto mb-6">
                        <span class="text-white font-bold text-xl">3</span>
                    </div>
                    <h3 class="font-bold text-xl text-slate-900 mb-3 dark:text-white">Pague com PIX</h3>
                    <p class="text-slate-600 dark:text-slate-300">Efetue o pagamento via PIX e aguarde a confirmação para prosseguir.</p>
                </div>
                
                <div class="text-center p-6 feature-card bg-white rounded-2xl shadow-sm dark:bg-slate-800">
                    <div class="w-16 h-16 rounded-2xl bg-primary flex items-center justify-center mx-auto mb-6">
                        <span class="text-white font-bold text-xl">4</span>
                    </div>
                    <h3 class="font-bold text-xl text-slate-900 mb-3 dark:text-white">Conecte-se ao Profissional</h3>
                    <p class="text-slate-600 dark:text-slate-300">Seja conectado diretamente ao profissional para realizar o serviço e obter instruções.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4 dark:text-white">Benefícios do renttool</h2>
                <p class="text-slate-600 max-w-2xl mx-auto dark:text-slate-300">Plataforma que conecta clientes e profissionais de desbloqueio com segurança e eficiência.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center p-6 feature-card bg-slate-50 rounded-2xl dark:bg-slate-800">
                    <div class="w-16 h-16 rounded-2xl bg-blue-100 flex items-center justify-center mx-auto mb-6 dark:bg-blue-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 mb-3 dark:text-white">Para Clientes</h3>
                    <p class="text-slate-600 dark:text-slate-300">Acesso a profissionais especializados com orçamentos personalizados e pagamento seguro via PIX.</p>
                </div>
                
                <div class="text-center p-6 feature-card bg-slate-50 rounded-2xl dark:bg-slate-800">
                    <div class="w-16 h-16 rounded-2xl bg-green-100 flex items-center justify-center mx-auto mb-6 dark:bg-green-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 mb-3 dark:text-white">Para Profissionais</h3>
                    <p class="text-slate-600 dark:text-slate-300">Mais clientes, maior visibilidade e pagamento garantido após a conclusão do serviço.</p>
                </div>
                
                <div class="text-center p-6 feature-card bg-slate-50 rounded-2xl dark:bg-slate-800">
                    <div class="w-16 h-16 rounded-2xl bg-blue-100 flex items-center justify-center mx-auto mb-6 dark:bg-blue-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 mb-3 dark:text-white">Sistema de Reembolso</h3>
                    <p class="text-slate-600 dark:text-slate-300">Proteção ao cliente com reembolso em casos de falha comprovada do serviço.</p>
                </div>
                
                <div class="text-center p-6 feature-card bg-slate-50 rounded-2xl dark:bg-slate-800">
                    <div class="w-16 h-16 rounded-2xl bg-yellow-100 flex items-center justify-center mx-auto mb-6 dark:bg-yellow-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 mb-3 dark:text-white">Comunicação Direta</h3>
                    <p class="text-slate-600 dark:text-slate-300">Chat em tempo real entre cliente e profissional para melhor execução do serviço.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-16 gradient-bg hero-pattern">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">Pronto para Começar a Desbloquear FRP?</h2>
            <p class="text-blue-100 max-w-2xl mx-auto mb-8 text-lg">Junte-se a milhares de profissionais que confiam no renttool para suas necessidades de desbloqueio.</p>
            <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                <a href="#tools" class="bg-white text-primary px-8 py-3 rounded-lg font-bold text-center hover:bg-slate-100 transition btn-primary shadow-lg dark:bg-slate-200 dark:text-slate-800">
                    Ver Ferramentas
                </a>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section id="faq" class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">Perguntas Frequentes</h2>
                <p class="text-slate-600 max-w-2xl mx-auto">Tudo o que você precisa saber sobre conectar-se a profissionais de desbloqueio.</p>
            </div>
            
            <div class="max-w-3xl mx-auto">
                <div class="border-b border-slate-200 py-6 transition-all duration-300 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-lg px-4 -mx-4">
                    <h3 class="font-bold text-lg text-slate-900 mb-2 dark:text-white">Como sou conectado ao profissional após o pagamento?</h3>
                    <p class="text-slate-600 dark:text-slate-300">Após completar seu pagamento via PIX, você será conectado diretamente ao profissional através de um chat em tempo real.</p>
                </div>
                
                <div class="border-b border-slate-200 py-6 transition-all duration-300 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-lg px-4 -mx-4">
                    <h3 class="font-bold text-lg text-slate-900 mb-2 dark:text-white">Preciso me cadastrar para solicitar um serviço?</h3>
                    <p class="text-slate-600 dark:text-slate-300">Não, nosso sistema é sem cadastro. Basta clicar em "Solicitar Orçamento", informar seus dados no formulário e aguardar as propostas dos profissionais.</p>
                </div>
                
                <div class="border-b border-slate-200 py-6 transition-all duration-300 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-lg px-4 -mx-4">
                    <h3 class="font-bold text-lg text-slate-900 mb-2 dark:text-white">Quais métodos de pagamento vocês aceitam?</h3>
                    <p class="text-slate-600 dark:text-slate-300">Aceitamos pagamentos exclusivamente via PIX para sua conveniência e segurança.</p>
                </div>
                
                <div class="border-b border-slate-200 py-6 transition-all duration-300 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-lg px-4 -mx-4">
                    <h3 class="font-bold text-lg text-slate-900 mb-2 dark:text-white">Como funciona o reembolso?</h3>
                    <p class="text-slate-600 dark:text-slate-300">Oferecemos reembolso em casos de falha comprovada do serviço. Basta enviar uma solicitação com vídeo comprobatório e dados do pedido.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8">
                <div class="lg:col-span-2">
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-10 h-10 rounded-xl gradient-bg flex items-center justify-center mr-2">
                            <span class="text-white font-bold text-xl">TT</span>
                        </div>
                        <h2 class="text-2xl font-bold">renttool<span class="text-primary">.com</span></h2>
                    </div>
                    <p class="text-slate-400 mb-6">Aluguel profissional de ferramentas de desbloqueio FRP. Economize dinheiro com nosso sistema de preços por hora.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-slate-400 hover:text-white">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                        </a>
                        <a href="#" class="text-slate-400 hover:text-white">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.689-.07-4.948 0-3.204.013-3.583.07-4.849 0-3.259.014-3.667.072-4.947c-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.584-.012 4.849-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.584-.012 4.849-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                    </div>
                </div>
                

                
                <div>
                    <h3 class="font-bold text-lg mb-6">Empresa</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-slate-400 hover:text-white">Sobre Nós</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white">Contato</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white">Política de Privacidade</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white">Termos de Serviço</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="font-bold text-lg mb-6">Suporte</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-slate-400 hover:text-white">Central de Ajuda</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white">Documentação</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white">Status da API</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white">Comunidade</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-slate-800 mt-12 pt-8 text-center text-slate-400">
                <p>&copy; 2025 renttool.com. Todos os direitos reservados. Aluguel profissional de ferramentas de desbloqueio FRP. --Projetado por @Matheus.britto.dev</p>
            </div>
        </div>
    </footer>
    
    <!-- Quote Request Modal -->
    <div id="quoteRequestModal" class="modal">
        <div class="modal-content">
            <!-- Left side - Service info -->
            <div class="w-full md:w-2/5 bg-gradient-to-b from-primary to-blue-700 text-white p-6 md:p-8 flex flex-col justify-between">
                <div>
                    <span class="close text-white text-3xl font-bold absolute top-4 right-6 cursor-pointer">&times;</span>
                    <h2 class="text-2xl font-bold mb-6 text-white">Solicitar Orçamento</h2>
                    
                    <div class="mb-6">
                        <h3 id="modalServiceName" class="font-bold text-xl mb-2 text-white">Nome do Serviço</h3>
                    </div>
                    
                    <div class="mt-8">
                        <h3 class="font-bold text-lg mb-3 text-white">Como funciona?</h3>
                        <ul class="space-y-2 text-sm text-white">
                            <li class="flex items-start">
                                <span class="mr-2">•</span>
                                <span>Preencha seus dados</span>
                            </li>
                            <li class="flex items-start">
                                <span class="mr-2">•</span>
                                <span>Receba orçamentos personalizados</span>
                            </li>
                            <li class="flex items-start">
                                <span class="mr-2">•</span>
                                <span>Escolha o melhor profissional</span>
                            </li>
                            <li class="flex items-start">
                                <span class="mr-2">•</span>
                                <span>Pague com PIX e inicie o serviço</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Right side - Form -->
            <div class="w-full md:w-3/5 p-6 md:p-8">
                <form id="quoteRequestForm">
                    <input type="hidden" id="serviceId" value="">
                    <div class="mb-6">
                        <label for="whatsapp" class="block text-slate-700 font-medium mb-2 dark:text-slate-300">Número do WhatsApp</label>
                        <input type="tel" id="whatsapp" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent form-input dark:bg-slate-700 dark:border-slate-600 dark:text-white" placeholder="(55) 99999-9999">
                        <p class="text-sm text-slate-500 mt-1 dark:text-slate-400">O número será formatado automaticamente. Para regiões Sul e Sudeste: o dígito 9 será removido automaticamente.</p>
                    </div>
                    
                    <div class="mb-6">
                        <label for="email" class="block text-slate-700 font-medium mb-2 dark:text-slate-300">E-mail</label>
                        <input type="email" id="email" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent form-input dark:bg-slate-700 dark:border-slate-600 dark:text-white" placeholder="seu@email.com">
                    </div>
                    
                    <div class="mb-6" id="additional-fields-container">
                        <!-- Additional fields will be loaded here -->
                    </div>
                    
                    <div class="mb-8 p-4 bg-yellow-50 rounded-lg dark:bg-slate-700">
                        <p class="text-slate-700 dark:text-slate-300">
                            <span class="font-bold dark:text-white">Importante:</span> Após enviar sua solicitação, você receberá orçamentos personalizados de nossos profissionais via WhatsApp e e-mail.
                        </p>
                    </div>
                    
                    <div class="flex space-x-4">
                        <button type="button" id="closeModal" class="flex-1 px-4 py-3 border border-slate-300 text-slate-700 rounded-lg font-medium hover:bg-slate-50 transition dark:text-white dark:border-slate-600 dark:hover:bg-slate-700">
                            Cancelar
                        </button>
                        <button type="submit" id="requestQuoteButton" class="flex-1 px-4 py-3 bg-primary text-white rounded-lg font-medium hover:bg-blue-700 transition btn-primary">
                            Solicitar Orçamento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Loading Modal -->
    <div id="loadingModal" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <div class="p-12">
                <!-- Loading State -->
                <div id="loadingState" class="text-center">
                    <div class="spinner mb-6"></div>
                    <h3 class="text-2xl font-bold mt-4 text-slate-900 dark:text-white" id="loadingModalTitle">Estamos gerando seu orçamento</h3>
                    <p class="text-slate-600 dark:text-slate-300 mt-3 text-lg" id="loadingModalMessage">Aguarde nessa tela. Não recarregue ou mude de página.</p>
                </div>
                
                <!-- Response State -->
                <div id="budgetResponseContainer" class="hidden">
                    <!-- Accepted Response -->
                    <div id="acceptedResponse" class="hidden text-center">
                        <div class="mb-6">
                            <h2 class="text-2xl font-bold text-slate-900 mb-2">Orçamento Aceito</h2>
                            <p class="text-slate-600 dark:text-slate-300 text-base mb-4">O profissional aceitou sua solicitação. Confira o valor abaixo e gere o pagamento via PIX quando estiver pronto.</p>
                        </div>

                        <div class="p-6 rounded-lg mb-8 bg-white border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                            <p class="text-slate-700 dark:text-slate-300 mb-2">Valor do Serviço</p>
                            <p class="text-2xl font-semibold text-slate-900 dark:text-white" id="budgetValue"></p>
                        </div>

                        <div class="flex flex-col space-y-3">
                            <button id="generatePaymentBtn" onclick="onGeneratePaymentClick()" class="w-full bg-primary text-white font-medium py-3 px-6 rounded-lg hover:bg-blue-700 transition">
                                Gerar Pagamento
                            </button>
                            <button id="cancelBudgetBtn" onclick="onCancelBudgetClick()" class="w-full bg-slate-100 text-slate-800 font-medium py-3 px-6 rounded-lg hover:bg-slate-200 transition">
                                Cancelar
                            </button>
                        </div>
                    </div>
                    
                    <!-- Rejected Response -->
                    <div id="rejectedResponse" class="hidden text-center">
                        <div class="mb-6">
                            <div class="text-6xl mb-4">❌</div>
                            <h2 class="text-3xl font-bold text-slate-200 mb-4">Orçamento Recusado</h2>
                            <p class="text-slate-700 dark:text-slate-300 text-lg mb-4">O profissional não aceitou sua solicitação no momento.</p>
                            <p class="text-slate-600 dark:text-slate-400">Você será notificado de outras propostas em breve.</p>
                        </div>
                        
                        <button id="closeRejectedBtn" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition">
                            OK
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- QR Code Modal -->
    <div id="qrCodeModal" class="modal">
        <div class="modal-content" style="max-width: 800px;">
            <div class="w-full md:w-1/2 p-6 md:p-8 border-r border-slate-200 dark:border-slate-700">
                <span class="close text-slate-500 text-3xl font-bold absolute top-4 right-6 cursor-pointer dark:text-slate-300">&times;</span>
                <h2 class="text-2xl font-bold mb-6 text-center text-slate-900 dark:text-white">Pague com PIX</h2>
                
                <div class="qr-code-container text-center">
                    <img id="qrCodeImage" class="qr-code-img mx-auto" src="" alt="QR Code para pagamento" style="max-width: 200px;">
                    <p class="mt-4 font-bold text-lg">Valor: R$<span id="qrCodeAmount"></span></p>
                    <p class="mt-2 text-slate-600 dark:text-slate-300">Serviço: <span id="qrCodeServiceName"></span></p>
                </div>
                
                <div class="mt-8 p-4 bg-blue-50 rounded-lg dark:bg-blue-900">
                    <p class="text-slate-700 text-center dark:text-blue-100">
                        <span class="font-bold">Instruções:</span> Após o pagamento, você será conectado diretamente ao profissional especializado.
                    </p>
                </div>
            </div>
            
            <div class="w-full md:w-1/2 p-6 md:p-8">
                <h3 class="text-xl font-bold mb-4 text-slate-900 dark:text-white">Código PIX (Copia e Cola)</h3>
                
                <div class="flex">
                    <input type="text" id="pixCode" class="flex-1 px-4 py-2 border border-slate-300 rounded-l-lg dark:bg-slate-700 dark:border-slate-600 dark:text-white" readonly>
                    <button id="copyPixCode" class="copy-button bg-primary text-white px-4 py-2 rounded-r-lg font-medium hover:bg-blue-700 transition">Copiar</button>
                </div>
                <div id="copiedMessage" class="copied-message text-green-600 mt-2 text-sm hidden">Código copiado!</div>
                
                <div class="mt-8">
                    <h3 class="text-lg font-bold mb-3 text-slate-900 dark:text-white">Como pagar com PIX</h3>
                    <ul class="space-y-2 text-slate-600 dark:text-slate-300">
                        <li class="flex items-start">
                            <span class="mr-2 text-primary">1.</span>
                            <span>Abra o app do seu banco</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2 text-primary">2.</span>
                            <span>Escolha a opção PIX</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2 text-primary">3.</span>
                            <span>Escaneie o QR Code ou cole o código</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2 text-primary">4.</span>
                            <span>Confirme o valor e finalize o pagamento</span>
                        </li>
                    </ul>
                </div>
                
                <div class="mt-8 text-center">
                    <button id="closeQrCodeModal" class="px-6 py-3 bg-slate-200 text-slate-800 rounded-lg font-medium hover:bg-slate-300 transition dark:bg-slate-700 dark:text-white dark:hover:bg-slate-600">
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Refund Modal -->
    <div id="refundModal" class="modal">
        <div class="modal-content">
            <div class="w-full p-6 md:p-8">
                <span class="close text-slate-500 text-3xl font-bold absolute top-4 right-6 cursor-pointer dark:text-slate-300">&times;</span>
                <h2 class="text-2xl font-bold mb-6 text-center text-slate-900 dark:text-white">Solicitação de Reembolso</h2>
                
                <form id="refundForm">
                    <input type="hidden" id="refundToolId" value="">
                    
                    <div class="mb-6">
                        <label for="orderId" class="block text-slate-700 font-medium mb-2 dark:text-slate-300">ID do Pedido *</label>
                        <input type="text" id="orderId" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent form-input dark:bg-slate-700 dark:border-slate-600 dark:text-white" placeholder="ID do pedido" required>
                    </div>
                    
                    <div class="mb-6">
                        <label for="problemLink" class="block text-slate-700 font-medium mb-2 dark:text-slate-300">Link para o vídeo comprobatório *</label>
                        <input type="text" id="problemLink" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent form-input dark:bg-slate-700 dark:border-slate-600 dark:text-white" placeholder="https://..." required>
                    </div>
                    
                    <div class="mb-6">
                        <label for="refundWhatsapp" class="block text-slate-700 font-medium mb-2 dark:text-slate-300">Número (WhatsApp registrado no pedido) *</label>
                        <input type="tel" id="refundWhatsapp" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent form-input dark:bg-slate-700 dark:border-slate-600 dark:text-white" placeholder="(55) 99999-9999" required>
                    </div>
                    
                    <div class="mb-6">
                        <label for="refundEmail" class="block text-slate-700 font-medium mb-2 dark:text-slate-300">E-mail registrado no pedido *</label>
                        <input type="email" id="refundEmail" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent form-input dark:bg-slate-700 dark:border-slate-600 dark:text-white" placeholder="seu@email.com" required>
                    </div>
                    
                    <div class="mb-6">
                        <label for="problemNote" class="block text-slate-700 font-medium mb-2 dark:text-slate-300">Relato do problema *</label>
                        <textarea id="problemNote" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent form-input dark:bg-slate-700 dark:border-slate-600 dark:text-white" rows="4" placeholder="Descreva detalhadamente o problema" required></textarea>
                    </div>
                    
                    <div class="mb-6 p-4 bg-green-100 rounded-lg dark:bg-green-900">
                        <p class="text-green-700 dark:text-green-100">
                            <span class="font-bold">Serviço gratuito:</span> Este serviço é gratuito, não é necessário pagamento.
                        </p>
                    </div>
                    
                    <div class="flex space-x-4">
                        <button type="button" id="closeRefundModal" class="flex-1 px-4 py-3 border border-slate-300 text-slate-700 rounded-lg font-medium hover:bg-slate-50 transition dark:text-white dark:border-slate-600 dark:hover:bg-slate-700">
                            Cancelar
                        </button>
                        <button type="submit" class="flex-1 px-4 py-3 bg-primary text-white rounded-lg font-medium hover:bg-blue-700 transition btn-primary">
                            Enviar Solicitação
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- IP Geolocation Script -->
    <script>
        // Função para detectar o país do usuário e definir o idioma
        function detectLanguage() {
            // Em uma implementação real, você usaria uma API de geolocalização por IP
            // Por enquanto, vamos usar a configuração do navegador como exemplo
            const userLang = navigator.language || navigator.userLanguage;
            
            // Se o usuário estiver no Brasil ou falar português, manter a versão em português
            if (userLang.toLowerCase().includes('pt') || userLang.toLowerCase().includes('br')) {
                // Já estamos na versão em português
                return 'pt';
            } else {
                // Redirecionar para a versão em inglês (você precisaria criar essa página)
                // window.location.href = '/en';
                return 'en';
            }
        }
        
        // Theme toggle functionality
        function initTheme() {
            const themeToggle = document.getElementById('theme-toggle');
            const themeIcon = document.getElementById('theme-icon');
            
            // Check for saved theme preference or respect OS preference
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const isDarkMode = savedTheme === 'dark' || (savedTheme !== 'light' && prefersDark);
            
            if (isDarkMode) {
                document.body.classList.add('dark-mode');
                themeIcon.setAttribute('d', 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z');
            }
            
            themeToggle.addEventListener('click', () => {
                const isDark = document.body.classList.toggle('dark-mode');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                
                if (isDark) {
                    themeIcon.setAttribute('d', 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z');
                } else {
                    themeIcon.setAttribute('d', 'M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z');
                }
            });
        }
        
        // Brazilian regions for phone number formatting
        const southernStates = ["RS", "SC", "PR"];
        const southeasternStates = ["SP", "RJ", "MG", "ES"];
        const northernStates = ["AC", "AP", "AM", "PA", "RO", "RR", "TO"];
        const northeasternStates = ["AL", "BA", "CE", "MA", "PB", "PE", "PI", "RN", "SE"];
        const midwestStates = ["DF", "GO", "MT", "MS"];
        
        // Format phone number based on region
        function formatPhoneNumber(number) {
            // Remove all non-digit characters
            let cleanNumber = number.replace(/\D/g, '');
            
            // Remove country code if present
            if (cleanNumber.startsWith('55')) {
                cleanNumber = cleanNumber.substring(2);
            }
            
            // Define area codes for Southern and Southeastern regions (where 9 digit is removed)
            const southernSoutheasternAreaCodes = [
                '11', '12', '13', '14', '15', '16', '17', '18', '19',  // SP
                '21', '22', '24',  // RJ
                '27', '28',        // ES
                '31', '32', '33', '34', '35', '37', '38',  // MG
                '41', '42', '43', '44', '45', '46',        // PR
                '51', '53', '54', '55',                    // RS
                '47', '48', '49',                          // SC
                '79'                                       // SE
            ];
            
            // Check if we have enough digits to determine area code
            if (cleanNumber.length >= 10) {
                const areaCode = cleanNumber.substring(0, 2);
                
                // For Southern and Southeastern regions with 11 digits (9 + 8 digits)
                // we remove the 9 digit
                if (southernSoutheasternAreaCodes.includes(areaCode) && 
                    cleanNumber.length === 11 && cleanNumber.charAt(0) === '9') {
                    // Remove the 9 for Southern/Southeastern regions
                    cleanNumber = cleanNumber.substring(1);
                }
            }
            
            return cleanNumber;
        }
        
        // Auto-format phone number as user types
        function autoFormatPhoneNumber(input) {
            let isDeleting = false;
            let previousValue = '';
            
            // Add event listener for real-time formatting
            input.addEventListener('input', function(e) {
                const currentValue = e.target.value;
                isDeleting = currentValue.length < previousValue.length;
                previousValue = currentValue;
                
                let value = currentValue.replace(/\D/g, '');
                
                // Check if we're deleting and at the beginning of the input
                if (isDeleting && e.target.selectionStart <= 4) {
                    // Allow deletion of country code
                    if (value.startsWith('55')) {
                        value = value.substring(2);
                    }
                } else {
                    // Add country code automatically if not present and we have enough digits
                    if (value.length > 0 && !value.startsWith('55')) {
                        value = '55' + value;
                    }
                }
                
                // Define area codes for Southern and Southeastern regions (where 9 digit is removed)
                const southernSoutheasternAreaCodes = [
                    '11', '12', '13', '14', '15', '16', '17', '18', '19',  // SP
                    '21', '22', '24',  // RJ
                    '27', '28',        // ES
                    '31', '32', '33', '34', '35', '37', '38',  // MG
                    '41', '42', '43', '44', '45', '46',        // PR
                    '51', '53', '54', '55',                    // RS
                    '47', '48', '49',                          // SC
                    '79'                                       // SE
                ];
                
                // Check if we have enough digits to determine area code
                if (value.length >= 4) {
                    const areaCode = value.substring(2, 4);
                    
                    // For Southern and Southeastern regions with 13 digits (55 + 9 + 8 digits)
                    // we remove the 9 digit
                    if (southernSoutheasternAreaCodes.includes(areaCode) && 
                        value.length === 13 && value.charAt(4) === '9') {
                        // Remove the 9 for Southern/Southeastern regions
                        value = value.substring(0, 4) + value.substring(5);
                    }
                }
                
                // Format for display: +55 XX XXXXX-XXXX or +55 XX XXXX-XXXX
                let formattedValue = '';
                if (value.length <= 2) {
                    formattedValue = value;
                } else if (value.length <= 4) {
                    formattedValue = `+${value.substring(0, 2)} ${value.substring(2)}`;
                } else if (value.length <= 8) {
                    formattedValue = `+${value.substring(0, 2)} ${value.substring(2, 4)} ${value.substring(4)}`;
                } else if (value.length <= 12) {
                    // With 9 digit (Northern, Northeastern, and Midwest regions)
                    formattedValue = `+${value.substring(0, 2)} ${value.substring(2, 4)} ${value.substring(4, 9)}${value.substring(9)}`;
                } else if (value.length <= 13) {
                    // Without 9 digit (Southern/Southeastern regions)
                    formattedValue = `+${value.substring(0, 2)} ${value.substring(2, 4)} ${value.substring(4, 8)}${value.substring(8)}`;
                } else {
                    // Limit to maximum length
                    if (southernSoutheasternAreaCodes.includes(value.substring(2, 4))) {
                        // Format for Southern/Southeastern (12 digits total)
                        formattedValue = `+${value.substring(0, 2)} ${value.substring(2, 4)} ${value.substring(4, 8)}${value.substring(8, 12)}`;
                    } else {
                        // Format for others (13 digits total)
                        formattedValue = `+${value.substring(0, 2)} ${value.substring(2, 4)} ${value.substring(4, 9)}${value.substring(9, 13)}`;
                    }
                }
                
                // Only update the value if it's different to prevent cursor jumping
                if (e.target.value !== formattedValue) {
                    const cursorPosition = e.target.selectionStart;
                    const oldLength = e.target.value.length;
                    e.target.value = formattedValue;
                    
                    // Adjust cursor position after formatting
                    const newLength = formattedValue.length;
                    const diff = newLength - oldLength;
                    const newCursorPosition = cursorPosition + diff;
                    e.target.setSelectionRange(newCursorPosition, newCursorPosition);
                }
            });
            
            // Handle keydown for better backspace/delete behavior
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' || e.key === 'Delete') {
                    isDeleting = true;
                }
                
                // Allow deletion of formatting characters (parentheses, spaces, hyphens)
                const formattingChars = ['(', ')', ' ', '-'];
                if (e.key === 'Backspace' && formattingChars.includes(e.target.value[e.target.selectionStart - 1])) {
                    e.preventDefault();
                    const start = e.target.selectionStart;
                    const end = e.target.selectionEnd;
                    const value = e.target.value;
                    
                    // Remove the character before the cursor
                    e.target.value = value.substring(0, start - 1) + value.substring(end);
                    e.target.setSelectionRange(start - 1, start - 1);
                }
            });
        }
        
        // Marcas/Serviços Navigation
        function initMarcasServicosNavigation() {
            // Get all data from PHP
            const allServices = {!! json_encode($services) !!};
            
            // Get DOM elements
            const marcasView = document.getElementById('marcasView');
            const servicosView = document.getElementById('servicosView');
            const marcaCards = document.querySelectorAll('.marca-card');
            const backToMarcasBtn = document.getElementById('backToMarcas');
            const servicosGrid = document.getElementById('servicosGrid');
            const servicosTitle = document.getElementById('servicosTitle');
            
            // Handle marca card click
            marcaCards.forEach(card => {
                card.addEventListener('click', function() {
                    const marcaId = parseInt(this.dataset.marcaId);
                    const marcaNome = this.querySelector('h3').textContent;
                    
                    // Filter services for this marca
                    const servicosDaMarca = allServices.filter(s => s.marca_id === marcaId);
                    
                    // Update title
                    servicosTitle.textContent = `Serviços - ${marcaNome}`;
                    
                    // Populate grid with services
                    servicosGrid.innerHTML = '';
                    servicosDaMarca.forEach(service => {
                        const serviceCard = document.createElement('div');
                        serviceCard.className = 'service-card bg-white rounded-2xl shadow-sm p-6 border border-slate-100 transition-all duration-300 feature-card';
                        
                        const photoHtml = service.photo_patch 
                            ? `<img src="/storage/${service.photo_patch}" alt="${service.nome_servico}" class="tool-logo mr-4 cursor-pointer" loading="lazy">`
                            : `<div class="tool-icon bg-slate-200 text-slate-500 mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                </svg>
                            </div>`;
                        
                        serviceCard.innerHTML = `
                            <div class="flex items-start mb-4">
                                ${photoHtml}
                                <div>
                                    <h3 class="font-bold text-lg text-slate-900">${service.nome_servico}</h3>
                                    <p class="text-slate-600 text-sm">${marcaNome}</p>
                                </div>
                            </div>
                            <p class="text-slate-600 mb-4">${service.descricao || 'Serviço profissional de desbloqueio especializado.'}</p>
                            <div class="flex justify-center">
                                <button class="request-quote-btn bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition btn-primary" data-service-id="${service.id}">
                                    Solicitar Orçamento
                                </button>
                            </div>
                        `;
                        
                        servicosGrid.appendChild(serviceCard);
                        
                        // Add click listener to request quote button
                        serviceCard.querySelector('.request-quote-btn').addEventListener('click', function() {
                            openQuoteRequestModal(service.id);
                        });
                    });
                    
                    // Switch views
                    marcasView.classList.add('hidden');
                    servicosView.classList.remove('hidden');
                    
                    // Scroll to services
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            });
            
            // Handle back to marcas button
            if (backToMarcasBtn) {
                backToMarcasBtn.addEventListener('click', function() {
                    marcasView.classList.remove('hidden');
                    servicosView.classList.add('hidden');
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            }
        }
        
        // Function to open quote request modal
        function openQuoteRequestModal(serviceId) {
            const quoteRequestModal = document.getElementById("quoteRequestModal");
            const serviceIdInput = document.getElementById("serviceId");
            const services = {!! json_encode($services) !!};
            const service = services.find(s => s.id === serviceId);
            
            if (service) {
                serviceIdInput.value = serviceId;
                const modalServiceName = document.getElementById("modalServiceName");
                const additionalFieldsContainer = document.getElementById("additional-fields-container");
                
                // Update modal title
                if (modalServiceName) {
                    modalServiceName.textContent = service.nome_servico;
                }
                
                // Load additional fields
                loadAdditionalFields(serviceId);
                
                // Show modal
                quoteRequestModal.style.display = "block";
                setTimeout(() => {
                    quoteRequestModal.classList.add('show');
                }, 10);
                document.body.classList.add('modal-open');
            }
        }
        
        // Global variables for polling and payment requests
        let pollingIntervalId = null;
        let currentPaymentAbortController = null;
        
        // Modal functionality
        document.addEventListener('DOMContentLoaded', function() {
            detectLanguage();
            initTheme();
            
            // Initialize Marcas/Services navigation
            initMarcasServicosNavigation();
            
            // Mobile menu toggle
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if (mobileMenuButton) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }
            
            // Close mobile menu when clicking outside
            document.addEventListener('click', function(event) {
                const isClickInside = mobileMenuButton.contains(event.target) || mobileMenu.contains(event.target);
                if (!isClickInside && !mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.add('hidden');
                }
            });
            
            // Get modal elements
            const quoteRequestModal = document.getElementById("quoteRequestModal");
            const loadingModal = document.getElementById("loadingModal");
            const qrCodeModal = document.getElementById("qrCodeModal");
            const closeModalBtns = document.getElementsByClassName("close");
            const closeBtn = document.getElementById("closeModal");
            const closeQrCodeModalBtn = document.getElementById("closeQrCodeModal");
            const copyPixCodeBtn = document.getElementById("copyPixCode");
            const copiedMessage = document.getElementById("copiedMessage");
            const serviceImages = document.querySelectorAll(".service-image");
            const quoteRequestForm = document.getElementById("quoteRequestForm");
            
            // Get modal content elements
            const modalServiceName = document.getElementById("modalServiceName");
            const serviceIdInput = document.getElementById("serviceId");
            const requestQuoteButton = document.getElementById("requestQuoteButton");
            const qrCodeImage = document.getElementById("qrCodeImage");
            const qrCodeAmount = document.getElementById("qrCodeAmount");
            const qrCodeServiceName = document.getElementById("qrCodeServiceName");
            const pixCode = document.getElementById("pixCode");
            
            // Close modal functions
            function closeQuoteRequestModal() {
                quoteRequestModal.classList.remove('show');
                setTimeout(() => {
                    quoteRequestModal.style.display = "none";
                    document.body.classList.remove('modal-open');
                }, 300);
            }
            
            function closeLoadingModal() {
                loadingModal.classList.remove('show');
                setTimeout(() => {
                    loadingModal.style.display = "none";
                }, 300);
            }
            
            function closeQrCodeModal() {
                qrCodeModal.classList.remove('show');
                setTimeout(() => {
                    qrCodeModal.style.display = "none";
                    document.body.classList.remove('modal-open');
                }, 300);
            }
            
            // Copy PIX code to clipboard
            if (copyPixCodeBtn) {
                copyPixCodeBtn.addEventListener("click", function() {
                    pixCode.select();
                    document.execCommand("copy");
                    
                    // Show copied message
                    copiedMessage.classList.remove("hidden");
                    
                    // Hide message after 2 seconds
                    setTimeout(function() {
                        copiedMessage.classList.add("hidden");
                    }, 2000);
                });
            }
            
            // Add event listeners to all close buttons
            for (let i = 0; i < closeModalBtns.length; i++) {
                closeModalBtns[i].onclick = closeQuoteRequestModal;
            }
            closeBtn.onclick = closeQuoteRequestModal;
            closeQrCodeModalBtn.onclick = closeQrCodeModal;
            
            // Close modal when clicking outside of it
            window.onclick = function(event) {
                if (event.target == quoteRequestModal) {
                    closeQuoteRequestModal();
                }
                if (event.target == qrCodeModal) {
                    closeQrCodeModal();
                }
                if (event.target == loadingModal) {
                    closeLoadingModal();
                }
            }
            
            // Note: Service image listeners are now handled by initMarcasServicosNavigation()
            

            // Initialize auto-formatting for phone input
            const whatsappInput = document.getElementById("whatsapp");
            if (whatsappInput) {
                autoFormatPhoneNumber(whatsappInput);
            }
            
            // Handle quote request form submission
            if (quoteRequestForm) {
                quoteRequestForm.addEventListener("submit", function(e) {
                    e.preventDefault();
                    processQuoteRequest();
                });
            }

            // Process quote request function
            function processQuoteRequest() {
                const whatsapp = document.getElementById("whatsapp").value;
                const email = document.getElementById("email").value;
                const serviceId = serviceIdInput.value;

                if (!whatsapp || !email || !serviceId) {
                    alert("Por favor, preencha todos os campos.");
                    return;
                }

                // Show loading modal
                quoteRequestModal.style.display = "none";
                loadingModal.style.display = "block";
                setTimeout(() => {
                    loadingModal.classList.add('show');
                }, 10);
                document.body.classList.add('modal-open');

                // Format the phone number for submission (remove all formatting)
                const cleanPhone = whatsapp.replace(/\D/g, '');

                // Collect additional field data
                const additionalFields = {};
                const customFieldInputs = document.querySelectorAll('#additional-fields-container input[name^="custom_field_"]');
                customFieldInputs.forEach(input => {
                    const fieldName = input.name;
                    const fieldValue = input.value;
                    additionalFields[fieldName] = fieldValue;
                });

                // Send quote request to server
                fetch('/request-service-quote', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        service_id: serviceId,
                        phone: cleanPhone, // Send the clean phone number with country code
                        email: email,
                        additional_fields: additionalFields // Include additional fields
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Keep loading modal open and wait for budget response
                        // Close the quote request modal but keep loading modal visible
                        closeQuoteRequestModal();
                        
                        // Store the orcamento_id for listening to updates
                        const orcamentoId = data.orcamento_id;
                        const uniqueId = data.unique_id;
                        
                        // Start listening for budget response via polling or WebSocket
                        startListeningForBudgetResponse(orcamentoId, uniqueId);
                        
                        // Keep loading modal open
                        document.getElementById('loadingModal').style.display = 'block';
                    } else {
                        // Show error message
                        closeLoadingModal();
                        alert("Erro ao enviar solicitação de orçamento: " + (data.message || 'Erro desconhecido'));
                        quoteRequestModal.style.display = "block";
                        document.body.classList.add('modal-open');
                    }
                })
                .catch(error => {
                    closeLoadingModal();
                    console.error('Erro ao enviar solicitação:', error);
                    alert("Erro ao enviar solicitação de orçamento: " + error.message);
                    quoteRequestModal.style.display = "block";
                    setTimeout(() => {
                        quoteRequestModal.classList.add('show');
                    }, 10);
                    document.body.classList.add('modal-open');
                });
            }
            

            
            // Handle form submission
            if (rentalForm) {
                rentalForm.addEventListener("submit", function(e) {
                    e.preventDefault();
                    
                    const whatsapp = document.getElementById("whatsapp").value;
                    const email = document.getElementById("email").value;
                    
                    if (!whatsapp || !email) {
                        alert("Por favor, preencha todos os campos.");
                        return;
                    }
                    
                    // Format the phone number based on region
                    const formattedPhone = formatPhoneNumber(whatsapp);
                    
                    // In a real implementation, you would process the payment here
                    // For now, we'll just show a success message
                    alert(`Pedido de aluguel enviado!

Ferramenta: ${modalToolName.textContent}
Valor: R$${modalToolPrice.textContent}

Os dados de acesso serão enviados para:
WhatsApp: +55 ${formattedPhone}
E-mail: ${email}`);
                    closeModal();
                    
                    // Reset form
                    rentalForm.reset();
                });
            }
            

            
            // Handle refund form submission
            const refundForm = document.getElementById("refundForm");
            if (refundForm) {
                refundForm.addEventListener("submit", function(e) {
                    e.preventDefault();
                    
                    const toolId = document.getElementById("refundToolId").value;
                    const whatsapp = document.getElementById("refundWhatsapp").value;
                    const email = document.getElementById("refundEmail").value;
                    const problemNote = document.getElementById("problemNote").value;
                    const orderId = document.getElementById("orderId").value;
                    const problemLink = document.getElementById("problemLink").value;
                    
                    if (!orderId || !problemLink || !whatsapp || !email || !problemNote) {
                        alert("Por favor, preencha todos os campos obrigatórios.");
                        return;
                    }
                    
                    // Show loading state
                    const submitButton = this.querySelector('button[type="submit"]');
                    const originalText = submitButton.textContent;
                    submitButton.textContent = "Enviando...";
                    submitButton.disabled = true;
                    
                    // Send refund request to server
                    fetch('/process-refund-request', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            tool_id: toolId,
                            numero: whatsapp,
                            email: email,
                            relato_problema: problemNote,
                            id_pedido: orderId,
                            link: problemLink
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("Solicitação de reembolso enviada com sucesso! Nossa equipe irá analisar sua solicitação.");
                            
                            // Close refund modal
                            document.getElementById("refundModal").style.display = "none";
                            document.body.classList.remove('modal-open');
                            
                            // Reset form
                            refundForm.reset();
                        } else {
                            alert("Erro ao enviar solicitação de reembolso: " + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert("Erro ao enviar solicitação de reembolso. Por favor, tente novamente.");
                    })
                    .finally(() => {
                        // Restore button state
                        submitButton.textContent = originalText;
                        submitButton.disabled = false;
                    });
                });
            }
            
            // Add event listeners for refund links
            const refundLinks = document.querySelectorAll('#refundLink, #refundLinkMobile');
            refundLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.getElementById("refundToolId").value = "00";
                    document.getElementById("refundModal").style.display = "block";
                    document.body.classList.add('modal-open');
                });
            });
            
            // Close refund modal
            const closeRefundModalBtn = document.getElementById("closeRefundModal");
            if (closeRefundModalBtn) {
                closeRefundModalBtn.onclick = function() {
                    document.getElementById("refundModal").style.display = "none";
                    document.body.classList.remove('modal-open');
                };
            }
            
            // Close refund modal when clicking outside of it
            const refundModal = document.getElementById("refundModal");
            if (refundModal) {
                refundModal.addEventListener("click", function(event) {
                    if (event.target == this) {
                        this.style.display = "none";
                        document.body.classList.remove('modal-open');
                    }
                });
            }
            
        });
    </script>
    
    
    <script>
        // Service modal functionality
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('serviceModal');
            const closeBtn = document.querySelector('.close');
            const cancelBtn = document.getElementById('cancelRequest');
            const serviceImages = document.querySelectorAll('.service-image');
            
            // If modal doesn't exist, exit early
            if (!modal) return;
            
            // Open modal when clicking on service image or button
            serviceImages.forEach(image => {
                image.addEventListener('click', function() {
                    const serviceId = this.getAttribute('data-service-id');
                    openServiceModal(serviceId);
                });
            });
            
            // Close modal when clicking the close button
            if (closeBtn) {
                closeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    modal.style.display = 'none';
                    document.body.classList.remove('modal-open');
                });
            }
            
            // Close modal when clicking the cancel button
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function() {
                    modal.style.display = 'none';
                    document.body.classList.remove('modal-open');
                });
            }
            
            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    event.preventDefault();
                    modal.style.display = 'none';
                    document.body.classList.remove('modal-open');
                }
            });
            
            // Handle form submission
            document.getElementById('serviceRequestForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Get form data
                const formData = new FormData(this);
                
                // Add custom fields to formData
                const customFields = document.querySelectorAll('[name^="custom_field_"]');
                customFields.forEach(field => {
                    formData.append(field.name, field.value);
                });
                
                // Show loading state
                const submitButton = this.querySelector('button[type="submit"]');
                const originalText = submitButton.textContent;
                submitButton.textContent = 'Enviando...';
                submitButton.disabled = true;
                
                // Send request to backend
                fetch('/request-service-quote', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Show loading modal and wait for response instead of alert
                        closeServiceModal();
                        showLoadingModal();
                        startListeningForBudgetResponse(data.orcamento_id, data.unique_id);
                    } else {
                        alert('Erro ao solicitar orçamento: ' + (data.message || 'Erro desconhecido'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erro ao solicitar orçamento. Por favor, tente novamente.');
                })
                .finally(() => {
                    // Restore button state
                    submitButton.textContent = originalText;
                    submitButton.disabled = false;
                });
            });
        });
        
        function openServiceModal(serviceId) {
            try {
                // Get service data from the PHP variable (converted to JavaScript)
                const services = {!! json_encode($services) !!};
                const service = services.find(s => s.id == serviceId);
                
                if (service) {
                    // Set service ID in hidden field
                    document.getElementById('modal-service-id').value = serviceId;
                    
                    // Load service image
                    const imageContainer = document.getElementById('modal-service-image-container');
                    if (service.photo_patch) {
                        imageContainer.innerHTML = `<img src="/storage/${service.photo_patch}" alt="${service.nome_servico}" class="w-full h-auto rounded-lg">`;
                    } else {
                        imageContainer.innerHTML = `
                            <div class="tool-icon bg-slate-200 text-slate-500 w-full h-48 flex items-center justify-center rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                </svg>
                            </div>
                        `;
                    }
                    
                    // Load custom fields
                    const customFieldsContainer = document.getElementById('custom-fields-container');
                    let customFieldsHtml = '<h4 class="font-bold text-lg text-slate-900 dark:text-white mb-4">Informações Adicionais</h4>';
                    
                    if (service.custom_fields && service.custom_fields.length > 0) {
                        service.custom_fields.forEach(field => {
                            const fieldName = field.parametros_campo?.field_name || '';
                            const fieldType = field.parametros_campo?.field_type || 'text';
                            
                            if (fieldName) {
                                customFieldsHtml += `
                                    <div class="mb-4">
                                        <label class="block text-slate-700 dark:text-slate-300 font-medium mb-2">${fieldName} ${fieldType === 'number' ? '(Numérico)' : '(Texto)'}</label>
                                        <input type="${fieldType === 'number' ? 'number' : 'text'}" 
                                               name="custom_field_${field.id}" 
                                               class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary dark:bg-slate-600 dark:border-slate-500 dark:text-white form-input">
                                    </div>
                                `;
                            }
                        });
                    } else {
                        customFieldsHtml += '<p class="text-slate-600 dark:text-slate-400">Nenhum campo adicional necessário para este serviço.</p>';
                    }
                    
                    customFieldsContainer.innerHTML = customFieldsHtml;
                    
                    // Show modal
                    document.getElementById('serviceModal').style.display = 'block';
                    document.body.classList.add('modal-open');
                }
            } catch (error) {
                console.error('Error opening service modal:', error);
                alert('Ocorreu um erro ao abrir o modal. Por favor, tente novamente.');
            }
        }
        
        // Process quote request function
        function processQuoteRequest() {
            const whatsapp = document.getElementById("whatsapp").value;
            const email = document.getElementById("email").value;
            const serviceId = serviceIdInput.value;

            if (!whatsapp || !email || !serviceId) {
                alert("Por favor, preencha todos os campos.");
                return;
            }

            // Show loading modal
            quoteRequestModal.style.display = "none";
            loadingModal.style.display = "block";
            document.body.classList.add('modal-open');

            // Format the phone number for submission (remove all formatting)
            const cleanPhone = whatsapp.replace(/\D/g, '');

            // Collect additional field data
            const additionalFields = {};
            const customFieldInputs = document.querySelectorAll('#additional-fields-container input[name^="custom_field_"]');
            customFieldInputs.forEach(input => {
                const fieldName = input.name;
                const fieldValue = input.value;
                additionalFields[fieldName] = fieldValue;
            });

            // Send quote request to server
            fetch('/request-service-quote', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    service_id: serviceId,
                    phone: cleanPhone, // Send the clean phone number with country code
                    email: email,
                    additional_fields: additionalFields // Include additional fields
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Keep loading modal open and wait for budget response
                    // Close the quote request modal but keep loading modal visible
                    closeQuoteRequestModal();
                    
                    // Store the orcamento_id for listening to updates
                    const orcamentoId = data.orcamento_id;
                    const uniqueId = data.unique_id;
                    
                    // Start listening for budget response via polling or WebSocket
                    startListeningForBudgetResponse(orcamentoId, uniqueId);
                    
                    // Keep loading modal open
                    document.getElementById('loadingModal').style.display = 'block';
                } else {
                    // Show error message
                    closeLoadingModal();
                    alert("Erro ao enviar solicitação de orçamento: " + (data.message || 'Erro desconhecido'));
                    quoteRequestModal.style.display = "block";
                    setTimeout(() => {
                        quoteRequestModal.classList.add('show');
                    }, 10);
                    document.body.classList.add('modal-open');
                }
            })
            .catch(error => {
                closeLoadingModal();
                console.error('Full error:', error);
                alert("Erro ao enviar solicitação de orçamento: " + (error.message || 'Erro desconhecido'));
                quoteRequestModal.style.display = "block";
                document.body.classList.add('modal-open');
            });
        }
        
        // Load additional fields based on service data
        function loadAdditionalFields(serviceId) {
            try {
                // Get service data from the PHP variable (converted to JavaScript)
                const services = {!! json_encode($services) !!};
                const service = services.find(s => s.id == serviceId);
                
                if (service) {
                    // Load custom fields
                    const additionalFieldsContainer = document.getElementById('additional-fields-container');
                    let customFieldsHtml = '<h4 class="font-bold text-lg text-slate-900 dark:text-white mb-4">Informações Adicionais</h4>';
                    
                    if (service.custom_fields && service.custom_fields.length > 0) {
                        service.custom_fields.forEach(field => {
                            const fieldName = field.parametros_campo?.field_name || '';
                            const fieldType = field.parametros_campo?.field_type || 'text';
                            
                            if (fieldName) {
                                customFieldsHtml += `
                                    <div class="mb-4">
                                        <label class="block text-slate-700 dark:text-slate-300 font-medium mb-2">${fieldName} ${fieldType === 'number' ? '(Numérico)' : '(Texto)'}</label>
                                        <input type="${fieldType === 'number' ? 'number' : 'text'}" 
                                               name="custom_field_${field.id}" 
                                               class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary dark:bg-slate-600 dark:border-slate-500 dark:text-white form-input">
                                    </div>
                                `;
                            }
                        });
                    } else {
                        customFieldsHtml += '<p class="text-slate-600 dark:text-slate-400">Nenhum campo adicional necessário para este serviço.</p>';
                    }
                    
                    additionalFieldsContainer.innerHTML = customFieldsHtml;
                }
            } catch (error) {
                console.error('Error loading additional fields:', error);
            }
        }
        
        // Handle form submission
        document.getElementById('serviceForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            const xhr = new XMLHttpRequest();
            xhr.open('POST', this.action, true);
            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    alert('Service booked successfully!');
                    document.getElementById('serviceModal').style.display = 'none';
                    document.body.classList.remove('modal-open');
                } else {
                    alert('Error booking service. Please try again.');
                }
            };
            xhr.send(formData);
        });
        
        function startListeningForBudgetResponse(orcamentoId, uniqueId) {
            // Clear any existing interval
            if (window.budgetPollingIntervalId) {
                clearInterval(window.budgetPollingIntervalId);
            }
            
            // Track polling start time to implement timeout
            const startTime = Date.now();
            const timeoutMs = 300000; // 5 minutes timeout
            
            console.log('Starting budget polling for orcamento ID:', orcamentoId);
            
            // Poll for budget updates every 2 seconds
            window.budgetPollingIntervalId = setInterval(() => {
                // Skip polling if page is not visible (performance optimization)
                if (document.hidden) {
                    console.log('Page hidden, skipping budget poll');
                    return;
                }
                
                // Check if we've exceeded timeout
                if (Date.now() - startTime > timeoutMs) {
                    clearInterval(window.budgetPollingIntervalId);
                    console.log('Budget polling timeout reached');
                    return;
                }
                
                fetch(`/api/orcamentos/${orcamentoId}/status`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data && data.status && (data.status === 'sim' || data.status === 'nao')) {
                        // Budget has been answered!
                        clearInterval(window.budgetPollingIntervalId);
                        console.log('Budget response received:', data.status);
                        displayBudgetResponse(data);
                    } else if (data && data.error) {
                        // Error occurred, stop polling
                        clearInterval(window.budgetPollingIntervalId);
                        console.error('Budget polling error:', data.error);
                    } else if (data && data.status === null) {
                        // Budget not yet responded, continue polling
                        console.log('Budget not yet responded, continuing to poll...');
                    }
                    // Add explicit check for when data.status is undefined or falsy but not null
                    else if (data && typeof data.status !== 'undefined' && data.status !== null) {
                        // Unexpected status value, stop polling to prevent infinite loop
                        clearInterval(window.budgetPollingIntervalId);
                        console.warn('Unexpected budget status value, stopping polling:', data.status);
                    }
                })
                .catch(error => {
                    console.error('Error polling for budget response:', error);
                    // Stop polling after persistent errors
                    if (Date.now() - startTime > 60000) { // Stop after 1 minute of errors
                        clearInterval(window.budgetPollingIntervalId);
                    }
                });
            }, 2000);
        }        
        function displayBudgetResponse(budgetData) {
            // Hide loading state
            document.getElementById('loadingState').classList.add('hidden');
            
            // Show response container
            const responseContainer = document.getElementById('budgetResponseContainer');
            responseContainer.classList.remove('hidden');
            
            // Store budget data globally for later use
            window.currentBudgetData = budgetData;
            
            if (budgetData.status === 'sim') {
                // Show accepted response
                document.getElementById('acceptedResponse').classList.remove('hidden');
                document.getElementById('rejectedResponse').classList.add('hidden');
                
                // Update value display
                const valueElement = document.getElementById('budgetValue');
                if (budgetData.valor) {
                    valueElement.innerHTML = `R$ ${parseFloat(budgetData.valor).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                } else {
                    valueElement.innerHTML = 'A definir';
                }
            } else if (budgetData.status === 'nao') {
                // Show rejected response
                document.getElementById('acceptedResponse').classList.add('hidden');
                document.getElementById('rejectedResponse').classList.remove('hidden');
            }
        }
        
        // Handle "Gerar Pagamento" button click
        document.addEventListener('DOMContentLoaded', function() {
            // Clean up intervals when page is unloaded
            window.addEventListener('beforeunload', function() {
                if (window.budgetPollingIntervalId) {
                    clearInterval(window.budgetPollingIntervalId);
                }
                if (window.paymentPollingIntervalId) {
                    clearInterval(window.paymentPollingIntervalId);
                }
            });
            
            // Setup generate payment button with debounce
            const generatePaymentBtn = document.getElementById('generatePaymentBtn');
            if (generatePaymentBtn) {
                let isGeneratingPayment = false;
                
                generatePaymentBtn.addEventListener('click', function() {
                    // Prevent multiple clicks (debounce)
                    if (isGeneratingPayment) {
                        console.warn('Payment generation already in progress');
                        return;
                    }
                    
                    isGeneratingPayment = true;
                    
                    // Call the global function
                    generatePayment()
                        .finally(() => {
                            isGeneratingPayment = false;
                        });
                });
            }
            
            const cancelBudgetBtn = document.getElementById('cancelBudgetBtn');
            if (cancelBudgetBtn) {
                cancelBudgetBtn.addEventListener('click', function() {
                    // If a payment request is in-flight, abort it
                    if (window.currentPaymentAbortController) {
                        try { window.currentPaymentAbortController.abort(); } catch(e){}
                        window.currentPaymentAbortController = null;
                    }

                    // Restore loading modal texts to default and close
                    const loadingTitle = document.getElementById('loadingModalTitle');
                    const loadingMessage = document.getElementById('loadingModalMessage');
                    if (loadingTitle) loadingTitle.textContent = 'Estamos gerando seu orçamento';
                    if (loadingMessage) loadingMessage.textContent = 'Aguarde nessa tela. Não recarregue ou mude de página.';

                    closeLoadingModal();
                });
            }
            
            const closeRejectedBtn = document.getElementById('closeRejectedBtn');
            if (closeRejectedBtn) {
                closeRejectedBtn.addEventListener('click', function() {
                    closeLoadingModal();
                });
            }
        });
        
        // Function to display QR Code modal
        function displayQRCodeModal(paymentData) {
            // Update QR Code modal with payment data
            const qrCodeImage = document.getElementById('qrCodeImage');
            const qrCodeAmount = document.getElementById('qrCodeAmount');
            const qrCodeServiceName = document.getElementById('qrCodeServiceName');
            const pixCode = document.getElementById('pixCode');
            const qrCodeModal = document.getElementById('qrCodeModal');
            const loadingModal = document.getElementById('loadingModal'); // Add missing declaration
            
            // Close loading modal function (defined here to ensure availability)
            function closeLoadingModal() {
                if (loadingModal) {
                    loadingModal.style.display = "none";
                }
            }
            
            // Handle different data structures for QR code
            let qrCodeData = paymentData.qr_code || (paymentData.data && paymentData.data.qr_code);
            let qrCodeBase64 = paymentData.qr_code_base64 || (paymentData.data && paymentData.data.qr_code_base64);
            
            if (qrCodeBase64) {
                // If we have base64 data, use it directly
                qrCodeImage.src = `data:image/png;base64,${qrCodeBase64}`;
                qrCodeImage.style.display = 'block';
            } else if (qrCodeData) {
                // Create image from base64 if available, otherwise use raw code
                if (qrCodeData.startsWith('data:image')) {
                    qrCodeImage.src = qrCodeData;
                    qrCodeImage.style.display = 'block';
                } else if (qrCodeData.startsWith('http')) {
                    // If it's a URL, set it directly
                    qrCodeImage.src = qrCodeData;
                    qrCodeImage.style.display = 'block';
                } else {
                    // For raw QR code data, we might need to generate or handle differently
                    // Try to convert to data URL if it looks like base64
                    try {
                        atob(qrCodeData); // Test if it's valid base64
                        qrCodeImage.src = `data:image/png;base64,${qrCodeData}`;
                        qrCodeImage.style.display = 'block';
                    } catch (e) {
                        // If not valid base64, hide the image
                        qrCodeImage.style.display = 'none';
                    }
                }
            } else {
                // No QR code data available
                qrCodeImage.style.display = 'none';
            }
            
            if (qrCodeAmount) {
                // Handle different data structures for amount
                let amount = paymentData.valor || (paymentData.data && paymentData.data.valor) || (paymentData.data && paymentData.data.amount);
                qrCodeAmount.textContent = amount || '0.00';
            }
            
            if (pixCode) {
                // Handle different data structures for PIX code
                let pixCodeData = paymentData.qr_code_raw || paymentData.qr_code || (paymentData.data && paymentData.data.qr_code_raw) || (paymentData.data && paymentData.data.qr_code);
                pixCode.value = pixCodeData || '';
            }
            
            // Handle service name
            if (qrCodeServiceName) {
                // Handle different data structures for service name
                let serviceName = paymentData.service_name || paymentData.servico || (paymentData.data && paymentData.data.service_name) || (paymentData.data && paymentData.data.servico);
                qrCodeServiceName.textContent = serviceName || 'Serviço';
            }
            
            // Close loading modal and show QR Code modal
            closeLoadingModal();
            if (qrCodeModal) {
                qrCodeModal.style.display = 'block';
                setTimeout(() => {
                    qrCodeModal.classList.add('show');
                }, 10);
                document.body.classList.add('modal-open');
            }
        }

        /**
         * Check payment status using polling
         * The MercadoPago API endpoint returns JSON with redirect_url when payment is approved
         * This function polls the endpoint and redirects the user when payment is confirmed
         */
        function checkPaymentStatusWithPolling(paymentId) {
            // Clear any existing interval
            if (window.paymentPollingIntervalId) {
                clearInterval(window.paymentPollingIntervalId);
            }
            
            // Track polling start time to implement timeout
            const startTime = Date.now();
            const timeoutMs = 300000; // 5 minutes timeout
            let pollCount = 0;
            
            console.log('Starting payment status polling for payment ID:', paymentId);
            
            // Poll the server for payment status
            window.paymentPollingIntervalId = setInterval(() => {
                // Skip polling if page is not visible (performance optimization)
                if (document.hidden) {
                    console.log('Page hidden, skipping payment poll');
                    return;
                }
                
                // Check if we've exceeded timeout
                if (Date.now() - startTime > timeoutMs) {
                    clearInterval(window.paymentPollingIntervalId);
                    console.log('Payment polling timeout reached after', pollCount, 'attempts');
                    alert('Tempo limite para verificação de pagamento excedido. Por favor, verifique seu histórico de pagamentos.');
                    return;
                }
                
                pollCount++;
                console.log(`Payment poll attempt #${pollCount} for payment ID:`, paymentId);
                
                fetch(`/api/mercadopago/check-payment-status/${paymentId}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log('Payment status response:', data);
                        
                        // Check if payment is approved and we have a redirect URL
                        if (data.success && data.redirect_url) {
                            // Payment approved, stop polling
                            clearInterval(window.paymentPollingIntervalId);
                            console.log('Payment approved! Redirecting to:', data.redirect_url);
                            
                            // Close QR code modal
                            const qrCodeModal = document.getElementById('qrCodeModal');
                            if (qrCodeModal) {
                                qrCodeModal.style.display = 'none';
                                document.body.classList.remove('modal-open');
                            }
                            
                            // Show success message and redirect
                            alert('Pagamento aprovado com sucesso! Você será redirecionado para a sala de chat.');
                            
                            // Redirect to chat room using the URL from API
                            window.location.href = data.redirect_url;
                        } else if (!data.success && data.message) {
                            // Error occurred, stop polling
                            console.error('Error checking payment status:', data.message);
                            clearInterval(window.paymentPollingIntervalId);
                            alert('Erro ao verificar status do pagamento: ' + data.message);
                        }
                        // If not approved yet, continue polling
                    })
                    .catch(error => {
                        console.error('Error checking payment status:', error);
                        // Stop polling after too many errors
                        if (Date.now() - startTime > 60000) { // Stop after 1 minute of errors
                            clearInterval(window.paymentPollingIntervalId);
                            alert('Erro de conexão ao verificar status do pagamento. Por favor, tente novamente.');
                        }
                    });
            }, 5000); // Check every 5 seconds
        }



        /**
         * Generate payment for the current budget
         * Returns a Promise that resolves when payment is generated or rejects on error
         */
        function generatePayment() {
            return new Promise((resolve, reject) => {
                const budgetData = window.currentBudgetData;
                
                if (!budgetData) {
                    alert('Erro: Dados do orçamento não encontrados.');
                    reject(new Error('Budget data not found'));
                    return;
                }

                const orcamentoId = budgetData.orcamento_id || budgetData.id;
                if (!orcamentoId) {
                    alert('Erro: ID do orçamento não encontrado.');
                    reject(new Error('Orcamento ID not found'));
                    return;
                }

                // Abort any existing payment request
                if (window.currentPaymentAbortController) {
                    try { window.currentPaymentAbortController.abort(); } catch(e){}
                }
                window.currentPaymentAbortController = new AbortController();

                // Get UI elements
                const loadingStateEl = document.getElementById('loadingState');
                const responseContainerEl = document.getElementById('budgetResponseContainer');
                const loadingTitle = document.getElementById('loadingModalTitle');
                const loadingMessage = document.getElementById('loadingModalMessage');
                const generatePaymentBtn = document.getElementById('generatePaymentBtn');

                // Update UI to loading state
                if (loadingTitle) loadingTitle.textContent = 'Gerando pagamento...';
                if (loadingMessage) loadingMessage.textContent = 'Aguarde. Estamos gerando o PIX e o QR Code.';
                if (loadingStateEl) loadingStateEl.classList.remove('hidden');
                if (responseContainerEl) responseContainerEl.classList.add('hidden');
                if (generatePaymentBtn) {
                    generatePaymentBtn.disabled = true;
                    generatePaymentBtn.innerHTML = 'Gerando pagamento...';
                }

                // Call payment API
                fetch('/api/payments/orcamento-pix', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ orcamento_id: orcamentoId }),
                    signal: window.currentPaymentAbortController.signal
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    window.currentPaymentAbortController = null;

                    // Re-enable button
                    if (generatePaymentBtn) {
                        generatePaymentBtn.disabled = false;
                        generatePaymentBtn.innerHTML = 'Gerar Pagamento';
                    }

                    if (data.success) {
                        // Display QR Code modal
                        displayQRCodeModal(data);
                        
                        // Start polling for payment status
                        if (data.payment_id) {
                            console.log('Starting payment polling for ID:', data.payment_id);
                            checkPaymentStatusWithPolling(data.payment_id);
                        } else {
                            console.warn('No payment_id returned from API');
                        }
                        
                        resolve(data);
                    } else {
                        // Restore UI on error
                        if (loadingStateEl) loadingStateEl.classList.add('hidden');
                        if (responseContainerEl) responseContainerEl.classList.remove('hidden');
                        if (loadingTitle) loadingTitle.textContent = 'Estamos gerando seu orçamento';
                        if (loadingMessage) loadingMessage.textContent = 'Aguarde nessa tela. Não recarregue ou mude de página.';
                        
                        const errorMsg = data.message || 'Erro desconhecido';
                        alert('Erro ao gerar QR Code: ' + errorMsg);
                        reject(new Error(errorMsg));
                    }
                })
                .catch(error => {
                    // Handle abort
                    if (error.name === 'AbortError') {
                        console.warn('Payment generation aborted by user');
                        if (loadingStateEl) loadingStateEl.classList.add('hidden');
                        if (responseContainerEl) responseContainerEl.classList.remove('hidden');
                        if (loadingTitle) loadingTitle.textContent = 'Estamos gerando seu orçamento';
                        if (loadingMessage) loadingMessage.textContent = 'Aguarde nessa tela. Não recarregue ou mude de página.';
                        if (generatePaymentBtn) {
                            generatePaymentBtn.disabled = false;
                            generatePaymentBtn.innerHTML = 'Gerar Pagamento';
                        }
                        reject(error);
                        return;
                    }

                    // Handle other errors
                    console.error('Error generating payment:', error);
                    if (loadingStateEl) loadingStateEl.classList.add('hidden');
                    if (responseContainerEl) responseContainerEl.classList.remove('hidden');
                    if (loadingTitle) loadingTitle.textContent = 'Estamos gerando seu orçamento';
                    if (loadingMessage) loadingMessage.textContent = 'Aguarde nessa tela. Não recarregue ou mude de página.';
                    if (generatePaymentBtn) {
                        generatePaymentBtn.disabled = false;
                        generatePaymentBtn.innerHTML = 'Gerar Pagamento';
                    }
                    
                    alert('Erro ao gerar pagamento: ' + (error.message || 'Erro desconhecido'));
                    reject(error);
                });
            });
        }

        /**
         * Global handler for inline onclick events
         * Wrapper around generatePayment() for backward compatibility
         */
        function onGeneratePaymentClick() {
            generatePayment().catch(err => {
                console.error('Payment generation failed:', err);
            });
        }

        function onCancelBudgetClick() {
            if (window.currentPaymentAbortController) {
                try { window.currentPaymentAbortController.abort(); } catch(e){}
                window.currentPaymentAbortController = null;
            }

            const loadingTitle = document.getElementById('loadingModalTitle');
            const loadingMessage = document.getElementById('loadingModalMessage');
            if (loadingTitle) loadingTitle.textContent = 'Estamos gerando seu orçamento';
            if (loadingMessage) loadingMessage.textContent = 'Aguarde nessa tela. Não recarregue ou mude de página.';

            // Close loading modal
            const loadingModal = document.getElementById('loadingModal');
            if (loadingModal) loadingModal.style.display = 'none';
            document.body.classList.remove('modal-open');
        }
        
        /**
         * Payment Status Monitoring
         * 
         * This application uses HTTP polling to check payment status.
         * When a payment is generated, checkPaymentStatusWithPolling() is called
         * which polls /api/mercadopago/check-payment-status/{paymentId} every 5 seconds.
         * 
         * When the payment is approved, the API returns:
         * {
         *   "success": true,
         *   "redirect_url": "http://example.com/chat/{room_code}",
         *   "message": "Payment approved and processed successfully"
         * }
         * 
         * The frontend then redirects the user to the chat room using window.location.href
         */
        
    </script>
    
</body>
</html>
</html>