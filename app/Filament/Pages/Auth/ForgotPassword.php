<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use App\Services\SupabaseService;

class ForgotPassword extends \Filament\Pages\Page
{
    protected static string $view = 'filament.pages.auth.forgot-password';
    
    protected static ?string $title = 'Lupa Password';
    
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->autocomplete('email'),
            ])
            ->statePath('data');
    }

    public function sendResetLink(): void
    {
        $data = $this->form->getState();

        $user = \App\Models\User::where('email', $data['email'])->first();
        
        if (! $user) {
            Notification::make()
                ->title('Email tidak ditemukan')
                ->danger()
                ->send();
            return;
        }

        $supabase = app(SupabaseService::class);
        $result = $supabase->sendPasswordResetEmail($user->email);

        if (! $result['success']) {
            \Log::error('Supabase reset password email: ' . $result['message']);

            Notification::make()
                ->title('Gagal mengirim email')
                ->body($result['message'])
                ->danger()
                ->send();
            return;
        }

        Notification::make()
            ->title('Email berhasil dikirim')
            ->body('Silakan cek email Anda untuk link reset password')
            ->success()
            ->send();

        $this->form->fill();
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('send')
                ->label('Kirim Link Reset')
                ->submit('sendResetLink')
                ->color('primary'),
        ];
    }
}
