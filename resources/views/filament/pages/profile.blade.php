<x-filament::page>
    <form wire:submit.prevent="submit">
        {{ $this->form }}

        <div class="mt-4">
            <button type="submit" class="filament-button filament-button-size-md filament-button-color-primary">
                Salvar
            </button>
        </div>
    </form>
</x-filament::page>
