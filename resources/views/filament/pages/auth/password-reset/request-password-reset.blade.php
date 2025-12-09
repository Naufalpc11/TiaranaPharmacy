<x-filament-panels::page.simple>
    @php
        $target = $this->getTargetEmail();
        $masked = $this->maskEmail($target);
    @endphp

    @if (! $this->sent)
        <div class="space-y-6 text-center">
            <div class="space-y-2">
                <h2 class="text-xl font-semibold text-gray-900">Kirim tautan reset</h2>
                <p class="text-sm text-gray-600">
                    Kami akan mengirim tautan reset password ke
                    <span class="font-semibold text-gray-900">{{ $masked }}</span>.
                </p>
            </div>

            <x-filament::button wire:click="request" color="primary" class="w-full">
                Kirim email
            </x-filament::button>
        </div>
    @else
        <div class="space-y-4 text-center">
            <h2 class="text-xl font-semibold text-gray-900">Periksa email Anda</h2>
            <p class="text-sm text-gray-600">
                Kami sudah mengirim tautan reset ke
                <span class="font-semibold text-gray-900">{{ $masked }}</span>. Ikuti petunjuk di email untuk membuat password baru.
            </p>
            <a href="{{ filament()->getLoginUrl() }}" class="text-primary-600 hover:text-primary-700 font-medium text-sm">
                Kembali ke login
            </a>
        </div>
    @endif
</x-filament-panels::page.simple>
