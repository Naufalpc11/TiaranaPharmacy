<x-filament-panels::page>
    <div class="max-w-md mx-auto">
        <div class="mb-6">
            <h2 class="text-2xl font-bold mb-2">Lupa Password?</h2>
            <p class="text-gray-600">Masukkan email Anda dan kami akan mengirimkan link untuk reset password</p>
        </div>

        <x-filament-panels::form wire:submit="sendResetLink">
            {{ $this->form }}
            
            <x-filament-panels::form.actions
                :actions="$this->getFormActions()"
            />
        </x-filament-panels::form>

        <div class="mt-6 text-center">
            <a href="{{ route('filament.admin.auth.login') }}" class="text-primary-600 hover:text-primary-700 font-medium">
                ← Kembali ke Login
            </a>
        </div>
    </div>
</x-filament-panels::page>
