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

    Route::livewire('/admin/jukir', 'admin::jukir.index-jukir')->name('jukir.index');
    Route::livewire('/admin/jukir/create', 'admin::jukir.create-jukir')->name('jukir.create');
    Route::livewire('/admin/jukir/{id}/edit', 'admin::jukir.edit-jukir')->name('jukir.edit');
    Route::livewire('/admin/jukir/{id}/detail', 'admin::jukir.detail-jukir')->name('jukir.detail');

    Route::livewire('/admin/merchant', 'admin::merchant.index-merchant')->name('merchant.index');

    Route::livewire('/admin/transaksi/tunai', 'admin::transaksi-tunai.index-transaksi-tunai')->name('transaksi.tunai.index');
    Route::livewire('/admin/transaksi/non-tunai', 'admin::transaksi-non-tunai.index-transaksi-non-tunai')->name('transaksi.non-tunai.index');

    Route::livewire('/admin/berlangganan', 'admin::berlangganan.index-berlangganan')->name('berlangganan.index');
});

require __DIR__.'/settings.php';
