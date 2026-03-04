<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['cekRole:superadmin,admin,korlap,guest'])->group(function () {
    Route::livewire('/admin/dashboard', 'admin::dashboard.index-dashboard')->name('dashboard');
});

Route::middleware(['cekRole:superadmin'])->group(function () {
    Route::livewire('/admin/users', 'admin::users.index-users')->name('users.index');
});

require __DIR__.'/settings.php';
