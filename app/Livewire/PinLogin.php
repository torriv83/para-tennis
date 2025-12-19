<?php

namespace App\Livewire;

use App\Models\Tournament;
use App\Services\PinAuthService;
use Livewire\Attributes\Validate;
use Livewire\Component;

class PinLogin extends Component
{
    public Tournament $tournament;

    public bool $showModal = false;

    #[Validate('required|string|size:6')]
    public string $pin = '';

    public ?string $error = null;

    public function verifyPin(): void
    {
        $this->validate();

        $pinService = app(PinAuthService::class);

        if ($pinService->authenticate($this->tournament, $this->pin)) {
            $this->showModal = false;
            $this->pin = '';
            $this->error = null;
            $this->dispatch('pin-authenticated');
        } else {
            if (! $this->tournament->isPinValid()) {
                $this->error = __('messages.pin_expired');
            } else {
                $this->error = __('messages.invalid_pin');
            }
        }
    }

    public function logout(): void
    {
        $pinService = app(PinAuthService::class);
        $pinService->logout($this->tournament);
        $this->dispatch('pin-logged-out');
    }

    public function openModal(): void
    {
        $this->showModal = true;
        $this->pin = '';
        $this->error = null;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->pin = '';
        $this->error = null;
    }

    public function render()
    {
        $pinService = app(PinAuthService::class);

        return view('livewire.pin-login', [
            'hasAccess' => $pinService->hasAccess($this->tournament),
            'hasPinEnabled' => $this->tournament->pin && $this->tournament->isPinValid(),
        ]);
    }
}
