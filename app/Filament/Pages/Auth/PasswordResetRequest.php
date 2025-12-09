<?php

namespace App\Filament\Pages\Auth;

use App\Services\SupabaseService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Pages\Auth\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset;
use Illuminate\Support\Facades\Log;

class PasswordResetRequest extends BaseRequestPasswordReset
{
    protected static string $view = 'filament.pages.auth.password-reset.request-password-reset';

    protected string $targetEmail = 'tiaranafarma@gmail.com';

    public bool $sent = false;
    public ?array $data = [];

    public function mount(): void
    {
        if (filament()->auth()->check()) {
            redirect()->intended(filament()->getUrl());
        }
    }

    public function request(): void
    {
        $email = $this->getTargetEmail();

        $supabase = app(SupabaseService::class);
        $result = $supabase->sendPasswordResetEmail($email);

        Log::info('[PasswordResetRequest] sendPasswordResetEmail', [
            'email' => $email,
            'success' => $result['success'] ?? null,
            'message' => $result['message'] ?? null,
        ]);

        if (! $result['success']) {
            Notification::make()
                ->title('Gagal mengirim email')
                ->body($result['message'])
                ->danger()
                ->send();

            return;
        }

        $this->sent = true;

        Notification::make()
            ->title('Email terkirim')
            ->body('Cek email Anda untuk link reset password.')
            ->success()
            ->send();
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        Hidden::make('email')->default($this->getTargetEmail()),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getFormActions(): array
    {
        if ($this->sent) {
            return [];
        }

        return [
            Action::make('request')
                ->label('Kirim email')
                ->submit('request')
                ->color('primary'),
        ];
    }

    public function getTargetEmail(): string
    {
        return $this->targetEmail;
    }

    /**
     * Hindari pemanggilan infolist bawaan agar tidak bentrok dengan metode form().
     */
    public function getInfolist(string $name): ?Infolist
    {
        return null;
    }

    public function maskEmail(string $email): string
    {
        [$name, $domain] = explode('@', $email, 2);
        $visible = substr($name, 0, 2);
        return $visible . str_repeat('*', max(strlen($name) - 2, 3)) . '@' . $domain;
    }

    public function form(Form $form): Form
    {
        return $form;
    }
}
