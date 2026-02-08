<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::livewire('/login', 'pages::auth.login')
    ->middleware(['guest'])->name('login');

Route::livewire('/forgot-password', 'pages::auth.forgot-password')
    ->middleware(['guest'])->name('password.request');

Route::livewire('/confirm-password', 'pages::auth.confirm-password')
    ->middleware(['auth'])->name('password.confirm');

Route::livewire('/reset-password/{token}', 'pages::auth.reset-password')
    ->middleware('guest')->name('password.reset');

Route::livewire('/verify-email', 'pages::auth.verify-email')
    ->middleware(['auth'])->name('verification.notice');

Route::livewire('/two-factor-challenge', 'pages::auth.two-factor-challenge')
    ->middleware(['guest'])->name('two-factor.login');

Route::livewire('/dashboard', 'pages::dashboard')
    ->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
