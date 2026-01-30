<?php

namespace App\Providers;

use App\Models\Program;
use App\Models\Document;
use App\Models\Proposal;
use App\Models\Template;
use App\Models\SavedFile;
use App\Models\Announcement;
use App\Models\Lpj;
use App\Models\LpjFile;
use App\Models\ProposalFile;
use App\Policies\ProgramPolicy;
use App\Policies\DocumentPolicy;
use App\Policies\ProposalPolicy;
use App\Policies\SavedFilePolicy;
use App\Policies\AnnouncementPolicy;
use App\Policies\LpjPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Gate::policy(Proposal::class, ProposalPolicy::class);
        Gate::policy(Program::class, ProgramPolicy::class);
        Gate::policy(SavedFile::class, SavedFilePolicy::class);
        Gate::policy(Document::class, DocumentPolicy::class);
        Gate::policy(Announcement::class, AnnouncementPolicy::class);
        Gate::policy(Lpj::class, LpjPolicy::class);

            // ✅ FIX: route model binding harus bisa akses soft-deleted (withTrashed)
        Route::bind('document', fn ($value) => Document::withTrashed()->findOrFail($value));
        Route::bind('template', fn ($value) => Template::withTrashed()->findOrFail($value));
        Route::bind('proposal', fn ($value) => Proposal::withTrashed()->findOrFail($value));
        Route::bind('file', fn ($value) => ProposalFile::withTrashed()->findOrFail($value));
        Route::bind('lpj', fn ($value) => Lpj::withTrashed()->findOrFail($value));
        Route::bind('lpjFile', fn ($value) => LpjFile::withTrashed()->findOrFail($value));
    }
}