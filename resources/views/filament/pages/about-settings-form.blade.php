<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament-panels::form wire:submit.prevent="save" class="space-y-6">
            {{ $this->form }}

            <div class="flex justify-end">
                <x-filament::button type="submit" color="primary">
                    {{ __('Simpan') }}
                </x-filament::button>
            </div>
        </x-filament-panels::form>
    </div>
</x-filament-panels::page>
