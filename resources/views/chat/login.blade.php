@extends('layouts.app')

@section('title', 'Acesso à Sala de Chat - Renttool')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Acesso à Sala de Chat
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Digite a senha para acessar a sala de chat
            </p>
        </div>
        <form class="mt-8 space-y-6" action="{{ route('chat.authenticate', $roomCode) }}" method="POST">
            @csrf
            <input type="hidden" name="room_code" value="{{ $roomCode }}">
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="password" class="sr-only">Senha</label>
                    <input id="password" name="password" type="password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Digite a senha">
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger text-red-600 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Acessar Sala
                </button>
            </div>
        </form>
    </div>
</div>
@endsection