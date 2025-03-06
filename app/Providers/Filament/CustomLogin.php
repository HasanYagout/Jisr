<?php

namespace App\Providers\Filament;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Facades\Filament;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Models\Contracts\FilamentUser;
use Filament\Notifications\Notification;

use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Validation\ValidationException;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;

class CustomLogin extends BaseLogin
{
    protected static string $view = 'filament.auth.login';

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->form->getState();

        // Attempt to authenticate the user
        if (! Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
        }

        // Get the authenticated user
        $user = Filament::auth()->user();

        // Check if the user's status is 1
        if ($user->status !== 1) {
            // Log the user out if their status is not 1
            Filament::auth()->logout();

            Notification::make()
                ->title('Account Inactive')
                ->body('Your account is not active. Please contact support.')
                ->danger()
                ->send();

            return null; // Prevent further action
        }

        // Check if the user can access the panel
        if (
            ($user instanceof FilamentUser) &&
            (! $user->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Filament::auth()->logout();

            $this->throwFailureValidationException();
        }

        // Regenerate the session
        session()->regenerate();

        // Return the login response
        return app(LoginResponse::class);
    }

}
