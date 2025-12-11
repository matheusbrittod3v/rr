<x-filament-panels::page>
    <div class="space-y-6">
        <!-- User Avatar & Info Header -->
        <x-filament::section>
            <div class="flex items-center gap-6">
                <div class="flex-shrink-0">
                    <div class="w-20 h-20 bg-primary-600 rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                </div>
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-gray-950 dark:text-white">
                        {{ auth()->user()->name }}
                    </h2>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">
                        {{ auth()->user()->email }}
                    </p>
                    @if(auth()->user()->numero)
                        <p class="text-gray-500 dark:text-gray-400 text-sm">
                            📱 {{ auth()->user()->numero }}
                        </p>
                    @endif
                    @if(auth()->user()->is_provider)
                        <div class="mt-2">
                            <x-filament::badge color="success" icon="heroicon-o-check-badge">
                                Provider Account
                            </x-filament::badge>
                        </div>
                    @endif
                </div>
                <div class="hidden md:block text-right">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Member Since</p>
                    <p class="text-base font-semibold text-gray-950 dark:text-white">
                        {{ auth()->user()->created_at->format('M Y') }}
                    </p>
                </div>
            </div>
        </x-filament::section>

        <!-- Form -->
        <form wire:submit="save">
            {{ $this->form }}
            
            <div class="mt-6 flex justify-end">
                <x-filament::button type="submit" icon="heroicon-o-check">
                    Save Changes
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>
