<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::livewire('/dashboard', 'pages::dashboard')
        ->middleware(['verified'])->name('dashboard');

    Route::livewire('/verify-email', 'pages::auth.verify-email')
        ->middleware(['auth'])->name('verification.notice');

    Route::livewire('/confirm-password', 'pages::auth.confirm-password')
        ->middleware(['auth'])->name('password.confirm');
});

Route::middleware(['guest'])->group(function () {
    Route::livewire('/login', 'pages::auth.login')->name('login');

    Route::livewire('/forgot-password', 'pages::auth.forgot-password')->name('password.request');

    Route::livewire('/reset-password/{token}', 'pages::auth.reset-password')->name('password.reset');

    Route::livewire('/two-factor-challenge', 'pages::auth.two-factor-challenge')->name('two-factor.login');
});


require __DIR__ . '/settings.php';
