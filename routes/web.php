<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SieController;
use App\Http\Controllers\BidangController;
use App\Http\Controllers\PeopleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\UserApprovalController;
use App\Http\Controllers\UserManagementController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth', 'verified')->group(function () {

    // ===============================================================================================================================
    // Profile
    // ===============================================================================================================================

    // Edit User
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    // Update User
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Hapus User
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ===============================================================================================================================
    // End Profile
    // ===============================================================================================================================



    // ===============================================================================================================================
    // Documents
    // ===============================================================================================================================

    // Halaman utama Dokumen
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');

    // Membuat Dokumen
    Route::get('/documents/create', [DocumentController::class, 'create'])->name('documents.create');

    // Reload Dokumen
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');

    // Unduh Dokumen
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');

    // Hapus Dokumen
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

    // ===============================================================================================================================
    // Documents
    // ===============================================================================================================================



    // ===============================================================================================================================
    // User
    // ===============================================================================================================================

    Route::get('/users/pending', [UserApprovalController::class, 'index'])->name('users.pending');
    Route::post('/users/{user}/approve', [UserApprovalController::class, 'approve'])->name('users.approve');
    Route::post('/users/{user}/reject', [UserApprovalController::class, 'reject'])->name('users.reject');

    // Create Tim Inti
    Route::get('/users/create-tim-inti', [UserManagementController::class, 'createTimInti'])
        ->middleware(['verified', 'role:super_admin'])
        ->name('users.create_inti');
    
    // Store Tim Inti
    Route::post('/users/create-inti', [UserManagementController::class, 'storeTimInti'])
        ->middleware(['verified', 'role:super_admin'])
        ->name('users.store_inti');

    // Menampilkan halaman Manajemen User
    Route::get('/users', [UserManagementController::class, 'index'])
        ->middleware(['verified', 'role:super_admin'])
        ->name('users.index');
    // End 

    // Menampilkan halaman Edit Manajemen User
    Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])
        ->middleware('verified', 'role:super_admin')
        ->name('users.edit');
    // End

    // Simpan perubahan 
    Route::put('/users/{user}', [UserManagementController::class, 'update'])
        ->whereNumber('user')
        ->middleware('verified', 'role:super_admin')
        ->name('users.update');
    // End

    // Menampilkan halaman Detail akun
    Route::get('/users/{user}', [UserManagementController::class, 'show'])
        ->middleware('verified', 'role:super_admin')
        ->name('users.show');
    // End

    // Menghapus Akun User
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])
        ->middleware('verified', 'role:super_admin')
        ->name('users.destroy');
    // End

    // ===============================================================================================================================
    // End User
    // ===============================================================================================================================



    // Mengunduh Template (Semua Role bisa melihat Template)
    Route::get('/templates', [TemplateController::class, 'index'])->name('templates.index');
    Route::get('/templates/{template}/download', [TemplateController::class, 'download'])->name('templates.download');
    // End 

    // Upload Template
    Route::post('/templates', [TemplateController::class, 'store'])->middleware(['role:super_admin|tim_inti|tim_bidang'])->name('templates.store');
    // End

    // Create Templates
    Route::get('/templates/create', [TemplateController::class, 'create'])->name('templates.create');
    // End 

    // Read Template
    Route::get('/templates/{template}', [TemplateController::class, 'show'])->name('templates.show');

    Route::get('/templates/{template}/stream', [TemplateController::class, 'stream'])
    ->name('templates.stream');

    Route::get('/templates/{template}/view', [TemplateController::class, 'view'])
    ->name('templates.view');
    // End

    // Hapus Template
    Route::delete('/templates/{template}', [TemplateController::class, 'destroy'])->middleware(['role:super_admin|tim_inti|tim_bidang'])->name('templates.destroy');
    // End

    // Bidang
    Route::get('/bidangs', [BidangController::class, 'index'])->middleware(['role:super_admin|tim_inti'])->name('bidangs.index');
    Route::post('/bidangs', [BidangController::class, 'store'])->middleware(['role:super_admin|tim_inti'])->name('bidangs.store');
    Route::put('/bidangs/{bidang}', [BidangController::class, 'update'])->middleware(['role:super_admin|tim_inti'])->name('bidangs.update');
    Route::patch('/bidangs/{bidang}/toggle', [BidangController::class, 'toggle'])->name('bidangs.toggle');
    Route::delete('/bidangs/{bidang}', [BidangController::class, 'destroy'])->middleware(['role:super_admin|tim_inti'])->name('bidangs.destroy');
    // End

    // Sie
    Route::get('/bidangs/{bidang}/sies', [SieController::class, 'index'])->name('sies.index');
    Route::post('/bidangs/{bidang}/sies', [SieController::class, 'store'])->name('sies.store');
    Route::put('/sies/{sie}', [SieController::class, 'update'])->name('sies.update');
    Route::patch('/sies/{sie}/toggle', [SieController::class, 'toggle'])->name('sies.toggle');
    Route::delete('sies/{sie}', [SieController::class, 'destroy'])->middleware(['role:super_admin|tim_inti'])->name('sies.destroy');
    // End
});

require __DIR__.'/auth.php';
