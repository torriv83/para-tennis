<?php

use App\Livewire\Tournament\Dashboard;
use Illuminate\Support\Facades\Route;

Route::get('/', Dashboard::class)->name('home');
