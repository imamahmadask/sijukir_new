<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['cekRole:superadmin,admin,korlap,guest'])->group(function () {
    Route::livewire('/admin/dashboard', 'admin::dashboard.index-dashboard')->name('dashboard');
});

Route::middleware(['cekRole:superadmin'])->group(function () {
    Route::livewire('/admin/users', 'admin::users.index-users')->name('users.index');
});

Route::middleware(['cekRole:superadmin,admin'])->group(function () {
    Route::livewire('/admin/korlap', 'admin::korlap.index-korlap')->name('korlap.index');
    
    Route::livewire('/admin/lokasi', 'admin::lokasi.index-lokasi')->name('lokasi.index');
    Route::livewire('/admin/lokasi/create', 'admin::lokasi.create-lokasi')->name('lokasi.create');
    Route::livewire('/admin/lokasi/{id}/edit', 'admin::lokasi.edit-lokasi')->name('lokasi.edit');
    Route::livewire('/admin/lokasi/{id}/detail', 'admin::lokasi.detail-lokasi')->name('lokasi.detail');
});

require __DIR__.'/settings.php';
