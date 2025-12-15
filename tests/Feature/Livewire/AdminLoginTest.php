<?php

use App\Livewire\AdminLogin;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

test('modal starts closed', function () {
    $component = Livewire::test(AdminLogin::class);

    expect($component->showModal)->toBeFalse();
});

test('can open modal', function () {
    Livewire::test(AdminLogin::class)
        ->call('openModal')
        ->assertSet('showModal', true);
});

test('can close modal', function () {
    Livewire::test(AdminLogin::class)
        ->set('showModal', true)
        ->set('email', 'test@example.com')
        ->set('password', 'password')
        ->call('closeModal')
        ->assertSet('showModal', false)
        ->assertSet('email', '')
        ->assertSet('password', '');
});

test('validates required fields', function () {
    Livewire::test(AdminLogin::class)
        ->set('showModal', true)
        ->call('login')
        ->assertHasErrors(['email', 'password']);
});

test('validates email format', function () {
    Livewire::test(AdminLogin::class)
        ->set('email', 'not-an-email')
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors(['email']);
});

test('rate limits login attempts', function () {
    RateLimiter::clear('admin-login:'.request()->ip());

    $component = Livewire::test(AdminLogin::class);

    // Make 5 failed attempts
    for ($i = 0; $i < 5; $i++) {
        $component
            ->set('email', 'wrong@example.com')
            ->set('password', 'wrongpassword')
            ->call('login');
    }

    // 6th attempt should be rate limited
    $component
        ->set('email', 'wrong@example.com')
        ->set('password', 'wrongpassword')
        ->call('login')
        ->assertHasErrors(['email']);

    // Clean up
    RateLimiter::clear('admin-login:'.request()->ip());
});

test('shows error on failed login', function () {
    Livewire::test(AdminLogin::class)
        ->set('email', 'wrong@example.com')
        ->set('password', 'wrongpassword')
        ->call('login')
        ->assertHasErrors(['email']);
});
