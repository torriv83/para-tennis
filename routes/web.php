<?php

use App\Livewire\Tournament\Dashboard;
use Illuminate\Support\Facades\Route;

Route::get('/{tournament?}', Dashboard::class)->name('home');
