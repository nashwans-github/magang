<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfilSaya extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationLabel = 'Profil Saya';
    protected static ?string $title = 'Profil Saya';
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static ?int $navigationSort = 99;

    protected static string $view = 'filament.pages.profil-saya';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true, table: 'users', column: 'email', ignorable: auth()->user()),
                TextInput::make('password')
                    ->label('Password Baru')
                    ->password()
                    ->revealable()
                    ->confirmed()
                    ->rule(Password::default()),
                TextInput::make('password_confirmation')
                    ->label('Konfirmasi Password Baru')
                    ->password()
                    ->revealable(),
                TextInput::make('current_password')
                    ->label('Password Saat Ini')
                    ->password()
                    ->revealable()
                    ->requiredWith('password')
                    ->dehydrated(false)
                    ->rule(function ($get) {
                        return function (string $attribute, $value, \Closure $fail) use ($get) {
                            if (! Hash::check($value, auth()->user()->password)) {
                                $fail('Password saat ini salah.');
                            }
                        };
                    }),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        try {
            $data = $this->form->getState();

            $user = auth()->user();
            $user->name = $data['name'];
            $user->email = $data['email'];

            if (! empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }

            $user->save();

            Notification::make() 
                ->success()
                ->title('Profil berhasil diperbarui')
                ->send();
            
            // Re-fill form to clear passwords but keep name/email
            $this->form->fill([
                'name' => $user->name,
                'email' => $user->email,
            ]);

        } catch (Halt $exception) {
            return;
        }
    }
}
