<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePassword extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';

    protected static string $view = 'filament.pages.change-password';
    
    protected static ?string $navigationLabel = 'Ubah Password';
    
    protected static ?string $title = 'Ubah Password';
    
    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Ubah Password')
                    ->description('Masukkan password lama dan password baru Anda')
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Password Saat Ini')
                            ->password()
                            ->required()
                            ->revealable()
                            ->autocomplete('current-password')
                            ->rules(['required']),
                        
                        TextInput::make('password')
                            ->label('Password Baru')
                            ->password()
                            ->required()
                            ->revealable()
                            ->autocomplete('new-password')
                            ->rules(['required', 'min:8'])
                            ->validationMessages([
                                'min' => 'Password minimal 8 karakter',
                            ]),
                        
                        TextInput::make('password_confirmation')
                            ->label('Konfirmasi Password Baru')
                            ->password()
                            ->required()
                            ->revealable()
                            ->autocomplete('new-password')
                            ->same('password')
                            ->validationMessages([
                                'same' => 'Konfirmasi password tidak cocok',
                            ]),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    public function updatePassword(): void
    {
        $data = $this->form->getState();

        // Verify current password
        if (!Hash::check($data['current_password'], Auth::user()->password)) {
            Notification::make()
                ->title('Password saat ini salah')
                ->danger()
                ->send();
            
            return;
        }

        // Update password
        $user = Auth::user();
        $user->password = Hash::make($data['password']);
        $user->save();

        // Reset form
        $this->form->fill();

        Notification::make()
            ->title('Password berhasil diubah')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('updatePassword')
                ->label('Ubah Password')
                ->submit('updatePassword'),
        ];
    }
}
