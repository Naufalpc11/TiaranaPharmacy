<x-filament-panels::page.simple>
    @if (filament()->hasRegistration())
        <x-slot name="heading">
            {{ __('filament-panels::pages/auth/login.heading') }}
        </x-slot>

        <x-slot name="subheading">
            {{ __('filament-panels::pages/auth/login.subheading') }}
            <a href="{{ filament()->getRegistrationUrl() }}" class="underline hover:no-underline focus:no-underline rounded-md outline-none ring-offset-2 ring-offset-white transition duration-75 ring-primary-500 focus:ring-2">
                {{ __('filament-panels::pages/auth/login.subheading.link') }}
            </a>
        </x-slot>
    @else
        <x-slot name="heading">
            {{ __('filament-panels::pages/auth/login.heading') }}
        </x-slot>
    @endif

    <form wire:submit="authenticate" class="space-y-6">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="filament()->isSidebarCollapsibleOnDesktop()"
        />
    </form>

    @if (filament()->hasPasswordReset())
        <div class="text-center mt-6">
            <a href="{{ route('password.forgot.form') }}" class="text-primary-600 hover:text-primary-700 font-medium text-sm">
                {{ __('filament-panels::pages/auth/login.buttons.forgot_password.label') }}
            </a>
        </div>
    @endif
</x-filament-panels::page.simple>
