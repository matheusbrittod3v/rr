<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Chat - uniontechnicals')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    
    <!-- Styles - Vite CSS -->
    @vite(['resources/css/app.css'])
    
    @yield('styles')
</head>
<body class="bg-gray-100">
    <main>
        @yield('content')
    </main>
    
    <!-- Scripts - Vite JS (deve vir antes dos scripts inline) -->
    @vite(['resources/js/app.js'])
    
    @yield('scripts')
</body>
</html>