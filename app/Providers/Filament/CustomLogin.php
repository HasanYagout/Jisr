<?php

namespace App\Providers\Filament;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Validation\ValidationException;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;

class CustomLogin extends BaseLogin
{
    protected static string $view = 'filament.auth.login';

}
