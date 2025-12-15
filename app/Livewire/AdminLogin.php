<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class AdminLogin extends Component
{
    public bool $showModal = false;

    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public function openModal(): void
    {
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['email', 'password', 'remember']);
        $this->resetValidation();
    }

    public function login(): void
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'E-post er påkrevd',
            'email.email' => 'Ugyldig e-postadresse',
            'password.required' => 'Passord er påkrevd',
        ]);

        $key = 'admin-login:'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('email', "For mange forsøk. Prøv igjen om {$seconds} sekunder.");

            return;
        }

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::clear($key);
            session()->regenerate();

            $this->redirect(request()->header('Referer', '/'), navigate: true);
        } else {
            RateLimiter::hit($key, 60);
            $this->addError('email', 'Feil e-post eller passord');
        }
    }

    public function logout(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        $this->redirect('/', navigate: true);
    }

    public function render()
    {
        return view('livewire.admin-login');
    }
}
