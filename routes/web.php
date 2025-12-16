<?php

use App\Livewire\Tournament\CreateTournament;
use App\Livewire\Tournament\Dashboard;
use Illuminate\Support\Facades\Route;

Route::get('/tournament/create', CreateTournament::class)
    ->middleware('auth')
    ->name('tournament.create');

Route::get('/{tournament?}', Dashboard::class)->name('home');
