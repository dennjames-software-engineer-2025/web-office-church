<?php

use App\Models\Proposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LpjController;
use App\Http\Controllers\SieController;
use App\Http\Controllers\ToolsController;
use App\Http\Controllers\BidangController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\PeopleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\SavedFileController;
use App\Http\Controllers\PengesahanController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ProposalFileController;
use App\Http\Controllers\UserApprovalController;
use App\Http\Controllers\MeetingMinuteController;
use App\Http\Controllers\UserManagementController;

// Route::get('/', function () {
//     return view('welcome');
// });

// =======================================================================================================================================
// SIDE NAVBAR
// =======================================================================================================================================

Route::middleware(['auth','verified'])->group(function () {

    // Dashboard = Pengumuman (list)
    Route::middleware('permission:announcements.view')->group(function () {
        Route::get('/dashboard', [AnnouncementController::class, 'index'])->name('dashboard');
    });

    Route::prefix('announcements')->name('announcements.')->group(function () {

        // CREATE harus didefinisikan sebelum /{announcement}
        Route::middleware('permission:announcements.create')->group(function () {
            Route::get('/create', [AnnouncementController::class, 'create'])->name('create');
            Route::post('/', [AnnouncementController::class, 'store'])->name('store');
        });

        // VIEW
        Route::middleware('permission:announcements.view')->group(function () {
            Route::get('/', [AnnouncementController::class, 'index'])->name('index');

            // penting: batasi hanya angka supaya tidak "nangkep" kata create/edit
            Route::get('/{announcement}', [AnnouncementController::class, 'show'])
                ->whereNumber('announcement')
                ->name('show');
        });

        // UPDATE
        Route::middleware('permission:announcements.update')->group(function () {
            Route::get('/{announcement}/edit', [AnnouncementController::class, 'edit'])
                ->whereNumber('announcement')
                ->name('edit');

            Route::put('/{announcement}', [AnnouncementController::class, 'update'])
                ->whereNumber('announcement')
                ->name('update');
        });

        // DELETE
        Route::middleware('permission:announcements.delete')->group(function () {
            Route::delete('/{announcement}', [AnnouncementController::class, 'destroy'])
                ->whereNumber('announcement')
                ->name('destroy');
        });

    });
});

// =======================================================================================================================================
// SIDE NAVBAR
// =======================================================================================================================================

// =======================================================================================================================================
// LOGIN KEDUDUKAN
// =======================================================================================================================================

Route::get('/', function () {
    return view('welcome-kedudukan');
})->name('welcome.kedudukan');

Route::get('/masuk/kedudukan/{kedudukan}', function (string $kedudukan) {
    $allowed = ['dpp_inti', 'bgkp', 'lingkungan', 'sekretariat'];

    abort_unless(in_array($kedudukan, $allowed, true), 404);

    session(['login_kedudukan' => $kedudukan]);

    return redirect()->route('login');
})->name('login.kedudukan');


// =======================================================================================================================================
// LOGIN KEDUDUKAN
// =======================================================================================================================================

// =======================================================================================================================================
// LOGIN KEDUDUKAN
// =======================================================================================================================================

Route::middleware(['auth', 'verified'])->group(function () {

    // =======================================================================================================================================
    // Profile
    // =======================================================================================================================================

    // Edit User
    Route::get('/profile', [ProfileController::class, 'edit'])
    ->name('profile.edit');

    // Update User
    Route::patch('/profile', [ProfileController::class, 'update'])
    ->name('profile.update');

    // Hapus User
    Route::delete('/profile', [ProfileController::class, 'destroy'])
    ->name('profile.destroy');

    // =======================================================================================================================================
    // End Profile
    // =======================================================================================================================================

    // ======================================================================
    // Documents
    // ======================================================================

    Route::middleware(['auth', 'verified'])->group(function () {

        // CREATE DOCUMENT
        Route::middleware('permission:documents.create')->group(function () {
            Route::get('/documents/create', [DocumentController::class, 'create'])->name('documents.create');
            Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
        });

        // VIEW DOCUMENT
        Route::middleware('permission:documents.view')->group(function () {
            Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');

            // ✅ Preview inline
            Route::get('/documents/{document}/preview', [DocumentController::class, 'preview'])
                ->whereNumber('document')
                ->name('documents.preview');

            // ✅ Download
            Route::get('/documents/{document}/download', [DocumentController::class, 'download'])
                ->whereNumber('document')
                ->name('documents.download');
        });

        // DELETE DOCUMENT
        Route::middleware('permission:documents.delete')->group(function () {
            Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])
                ->whereNumber('document')
                ->name('documents.destroy');
        });
    });

    // ======================================================================
    // END Documents
    // ======================================================================

    // ===============================================================================================================================
    // USER MANAGEMENT 
    // ===============================================================================================================================

    Route::middleware(['auth', 'verified', 'role:super_admin'])->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');

        Route::get('/users/{user}', [UserManagementController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');

        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    });

    // ===============================================================================================================================
    // END USER MANAGEMENT
    // ===============================================================================================================================

    // ===============================================================================================================================
    // BIDANG & SIE (SUPER ADMIN)
    // ===============================================================================================================================

    Route::middleware(['auth', 'verified', 'role:super_admin'])->group(function () {

        // BIDANG
        Route::get('/bidangs', [BidangController::class, 'index'])->name('bidangs.index');
        Route::post('/bidangs', [BidangController::class, 'store'])->name('bidangs.store');
        Route::put('/bidangs/{bidang}', [BidangController::class, 'update'])->name('bidangs.update');
        Route::delete('/bidangs/{bidang}', [BidangController::class, 'destroy'])->name('bidangs.destroy');
        Route::patch('/bidangs/{bidang}/toggle', [BidangController::class, 'toggle'])->name('bidangs.toggle');

        // SIE (khusus DPP Inti - sudah di-guard di controllernya)
        Route::get('/bidangs/{bidang}/sies', [SieController::class, 'index'])->name('sies.index');
        Route::post('/bidangs/{bidang}/sies', [SieController::class, 'store'])->name('sies.store');

        Route::put('/sies/{sie}', [SieController::class, 'update'])->name('sies.update');
        Route::delete('/sies/{sie}', [SieController::class, 'destroy'])->name('sies.destroy');
        Route::patch('/sies/{sie}/toggle', [SieController::class, 'toggle'])->name('sies.toggle');
    });

    // ===============================================================================================================================
    // END BIDANG & SIE
    // ===============================================================================================================================

    // ===============================================================================================================================
    // FITUR MANAJEMEN TEMPLATE
    // ===============================================================================================================================

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/templates', [TemplateController::class, 'index'])->name('templates.index');
        Route::get('/templates/create', [TemplateController::class, 'create'])->name('templates.create');
        Route::post('/templates', [TemplateController::class, 'store'])->name('templates.store');

        Route::get('/templates/{template}', [TemplateController::class, 'show'])->name('templates.show');
        Route::get('/templates/{template}/download', [TemplateController::class, 'download'])->name('templates.download');
        Route::get('/templates/{template}/stream', [TemplateController::class, 'stream'])->name('templates.stream');
        Route::get('/templates/{template}/view', [TemplateController::class, 'view'])->name('templates.view');

        Route::delete('/templates/{template}', [TemplateController::class, 'destroy'])->name('templates.destroy');
    });

    // ===============================================================================================================================
    // FITUR MANAJEMEN TEMPLATE
    // ===============================================================================================================================

    // ==============================================================================================================================
    // Tools Signature Pad
    // ==============================================================================================================================

    Route::get('/signature', [ToolsController::class, 'signature'])->middleware('role:super_admin')->name('tools.signature');
    Route::post('/signature/save', [ToolsController::class, 'saveSignature'])->middleware('role:super_admin')->name('tools.signature.save');

    // ==============================================================================================================================
    // Tools Signature Pad
    // ==============================================================================================================================

    // ======================================================================
    // PENGESAHAN — ADMIN (Super Admin & Ketua)
    // ======================================================================

    Route::prefix('pengesahan/admin')
        ->middleware(['role:super_admin|ketua'])
        ->group(function () {

        Route::get('/', [PengesahanController::class, 'index'])
            ->name('pengesahan.admin.index');

        Route::get('/{doc}/preview', [PengesahanController::class, 'preview'])
            ->name('pengesahan.admin.preview');

        Route::post('/{doc}/accept', [PengesahanController::class, 'accept'])
            ->name('pengesahan.admin.accept');

        Route::get('/{doc}/reject', [PengesahanController::class, 'rejectForm'])
            ->name('pengesahan.admin.rejectForm');

        Route::post('/{doc}/reject', [PengesahanController::class, 'reject'])
            ->name('pengesahan.admin.reject');

        Route::get('/{doc}/surat', [PengesahanController::class, 'suratForm'])
            ->name('pengesahan.admin.suratForm');

        Route::post('/{doc}/surat', [PengesahanController::class, 'suratStore'])
            ->name('pengesahan.admin.suratStore');

        Route::get('/{doc}/watermark', [PengesahanController::class, 'watermarkForm'])
            ->name('pengesahan.admin.watermarkForm');

        Route::post('/{doc}/watermark', [PengesahanController::class, 'watermarkStore'])
            ->name('pengesahan.admin.watermarkStore');

        Route::delete('/{doc}', [PengesahanController::class, 'destroy'])
            ->name('pengesahan.admin.destroy');
    });

    // ======================================================================
    // PENGESAHAN — ADMIN (Super Admin & Ketua)
    // ======================================================================


    // ======================================================================
    // PENGESAHAN — USER (Tim Bidang & Tim Inti Non-Ketua)
    // ======================================================================

    Route::prefix('pengesahan/user')
        ->middleware(['role:tim_bidang|wakil_ketua|sekretaris_1|sekretaris_2|bendahara_1|bendahara_2'])
        ->group(function () {

        Route::get('/upload', [PengesahanController::class, 'userCreate'])
            ->name('pengesahan.userCreate');

        Route::post('/upload', [PengesahanController::class, 'userStore'])
            ->name('pengesahan.userStore');

        Route::get('/history', [PengesahanController::class, 'userHistory'])
            ->name('pengesahan.userHistory');

        Route::delete('/{doc}/delete', [PengesahanController::class, 'userDestroy'])
            ->name('pengesahan.userDestroy');
    });

    // ======================================================================
    // PENGESAHAN — USER (Tim Bidang & Tim Inti Non-Ketua)
    // ======================================================================


    // ======================================================================
    // PROGRAM (DISABLE sementara via feature flag)
    // ======================================================================

    // Route::middleware(['auth', 'verified', 'feature:programs'])->group(function () {

    //     Route::get('/programs', [ProgramController::class, 'index'])
    //         ->name('programs.index');

    //     Route::get('/programs/create', [ProgramController::class, 'create'])
    //         ->name('programs.create');

    //     Route::post('/programs', [ProgramController::class, 'store'])
    //         ->name('programs.store');

    //     Route::get('/programs/{program}', [ProgramController::class, 'show'])
    //         ->name('programs.show');

    //     Route::delete('/programs/{program}', [ProgramController::class, 'destroy'])
    //         ->name('programs.destroy');

    //     Route::patch('/programs/{program}/status', [ProgramController::class, 'changeStatus'])
    //         ->name('programs.change-status');

    //     Route::get('/programs/{program}/edit', [ProgramController::class, 'edit'])
    //         ->name('programs.edit');

    //     Route::put('/programs/{program}', [ProgramController::class, 'update'])
    //         ->name('programs.update');
    // });

    // ======================================================================
    // PROGRAM
    // ======================================================================

    // ======================================================================
    // PENGAJUAN PROPOSAL (TANPA PROGRAM)
    // ======================================================================

    Route::middleware(['auth', 'verified'])->group(function () {

        // list inbox sesuai role
        Route::get('/proposals', [ProposalController::class, 'index'])->name('proposals.index');

        // create proposal (Ketua Sie)
        Route::get('/proposals/create', [ProposalController::class, 'create'])->name('proposals.create');
        Route::post('/proposals', [ProposalController::class, 'store'])->name('proposals.store');

        // detail
        Route::get('/proposals/{proposal}', [ProposalController::class, 'show'])->name('proposals.show');

        // aksi Ketua Bidang
        Route::patch('/proposals/{proposal}/approve-ketua-bidang', [ProposalController::class, 'approveKetuaBidang'])
            ->name('proposals.approve_ketua_bidang');

        // sekretaris atur durasi dpp harian
        Route::patch('/proposals/{proposal}/set-dpp-deadline', [ProposalController::class, 'setDppDeadline'])
            ->name('proposals.set_dpp_deadline');

        // sekretaris kasih notes ke romo
        Route::patch('/proposals/{proposal}/notes', [ProposalController::class, 'addNotes'])
            ->name('proposals.add_notes');

        // Romo approve/reject
        Route::patch('/proposals/{proposal}/approve-romo', [ProposalController::class, 'approveRomo'])
            ->name('proposals.approve_romo');

        Route::patch('/proposals/{proposal}/reject-romo', [ProposalController::class, 'rejectRomo'])
            ->name('proposals.reject_romo');

        // file preview/download
        Route::get('/proposals/{proposal}/files/{file}/preview', [ProposalFileController::class, 'preview'])
            ->name('proposals.files.preview');

        Route::get('/proposals/{proposal}/files/{file}/download', [ProposalFileController::class, 'download'])
            ->name('proposals.files.download');

        // receipt preview/download (PDF bukti penerimaan)
        Route::get('/proposals/{proposal}/receipt/preview', [ProposalController::class, 'receiptPreview'])
            ->name('proposals.receipt.preview');

        Route::get('/proposals/{proposal}/receipt/download', [ProposalController::class, 'receiptDownload'])
            ->name('proposals.receipt.download');

        Route::patch('/proposals/{proposal}/end-dpp-harian', [ProposalController::class, 'endDppHarian'])
            ->name('proposals.end_dpp_harian');

        Route::delete('/proposals/{proposal}', [ProposalController::class, 'destroy'])
            ->name('proposals.destroy');

        Route::patch('/proposals/{proposal}/archive', [ProposalController::class, 'archive'])
            ->name('proposals.archive');

        Route::patch('/proposals/{proposal}/unarchive', [ProposalController::class, 'unarchive'])
            ->name('proposals.unarchive');


        /* Route untuk LPJ (Scoped di dalam Proposal) */
        Route::prefix('/proposals/{proposal}')->group(function () {
            Route::get('/lpj/create', [LpjController::class, 'create'])->name('lpj.create');
            Route::post('/lpj', [LpjController::class, 'store'])->name('lpj.store');

            // ✅ list LPJ dalam 1 proposal (untuk DPP Harian / Ketua Bidang dll)
            Route::get('/lpjs', [LpjController::class, 'listByProposal'])->name('lpj.by_proposal');

            Route::get('/lpj/{lpj}', [LpjController::class, 'show'])->name('lpj.show');

            Route::get('/lpj/{lpj}/files/{lpjFile}/preview', [LpjController::class, 'preview'])
                ->name('lpj.files.preview');
            Route::get('/lpj/{lpj}/files/{lpjFile}/download', [LpjController::class, 'download'])
                ->name('lpj.files.download');

            Route::patch('/lpj/{lpj}/approve-ketua-bidang', [LpjController::class, 'approveKetuaBidang'])
                ->name('lpj.approve_ketua_bidang');
            Route::patch('/lpj/{lpj}/approve-final', [LpjController::class, 'approveFinal'])
                ->name('lpj.approve_final');
            Route::patch('/lpj/{lpj}/reject-final', [LpjController::class, 'rejectFinal'])
                ->name('lpj.reject_final');
            Route::patch('/lpj/{lpj}/notes', [LpjController::class, 'addNotes'])
                ->name('lpj.add_notes');

            Route::delete('/lpj/{lpj}', [LpjController::class, 'destroy'])->name('lpj.destroy');
        });
    });
    /**
         * OPTIONAL (global lpjs)
         * Kalau kamu tetap mau halaman global /lpjs, biarkan.
         * Kalau kamu fokus “LPJ harus di dalam proposal”, boleh hapus route ini.
         */
        Route::get('/lpjs', [LpjController::class, 'index'])->name('lpjs.index');

    // ======================================================================
    // END PENGAJUAN PROPOSAL
    // ======================================================================

    // ======================================================================
    // FILE MANAGEMENT 
    // ======================================================================

    /* Sekretaris */
    Route::middleware(['auth', 'verified', 'permission:files.manage'])->group(function () {
        Route::get('/folders', [FolderController::class, 'index'])->name('folders.index');
        Route::post('/folders', [FolderController::class, 'store'])->name('folders.store');
        Route::get('/folders/{folder}', [FolderController::class, 'show'])->name('folders.show');
        Route::delete('/folders/{folder}', [FolderController::class, 'destroy'])->name('folders.destroy');

        Route::post('/folders/{folder}/items', [SavedFileController::class, 'store'])->name('folders.items.store');
        Route::put('folders/items/{item}', [SavedFileController::class, 'update'])->name('folders.items.update');
        Route::delete('folders/items/{item}', [SavedFileController::class, 'destroy'])->name('folders.items.destroy');
    });

    /* Role Lain */
    Route::middleware(['auth', 'verified', 'permission:files.view'])->group(function () {
        Route::get('/files', [SavedFileController::class, 'sharedIndex'])->name('files.shared.index');
    });
    
    // ===============================
    // FILE MANAGEMENT 
    // ===============================

    // MEETING MINUTE / NOTULENSI
Route::middleware(['auth','verified'])->prefix('minutes')->name('minutes.')->group(function () {

    // VIEW (semua yg boleh lihat)
    Route::middleware('permission:notulensi.view')->group(function () {
        Route::get('/', [MeetingMinuteController::class, 'index'])->name('index');
        Route::get('/{minute}', [MeetingMinuteController::class, 'show'])
            ->whereNumber('minute')
            ->name('show');
    });

    // MANAGE (sekretaris / super admin / yg kamu izinkan)
    Route::middleware('permission:notulensi.manage')->group(function () {
        Route::get('/create', [MeetingMinuteController::class, 'create'])->name('create');
        Route::post('/', [MeetingMinuteController::class, 'store'])->name('store');

        Route::get('/{minute}/edit', [MeetingMinuteController::class, 'edit'])
            ->whereNumber('minute')
            ->name('edit');

        Route::put('/{minute}', [MeetingMinuteController::class, 'update'])
            ->whereNumber('minute')
            ->name('update');

        Route::delete('/{minute}', [MeetingMinuteController::class, 'destroy'])
            ->whereNumber('minute')
            ->name('destroy');
    });

}); 

});

require __DIR__.'/auth.php';
