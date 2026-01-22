<?php

namespace App\Providers;

use App\Models\Announcement;
use App\Models\Document;
use App\Models\Program;
use App\Models\Proposal;
use App\Models\SavedFile;
use App\Policies\AnnouncementPolicy;
use App\Policies\DocumentPolicy;
use App\Policies\ProgramPolicy;
use App\Policies\ProposalPolicy;
use App\Policies\SavedFilePolicy;
use Illuminate\Support\Facades\Gate;
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
        Gate::policy(Proposal::class, ProposalPolicy::class);
        Gate::policy(Program::class, ProgramPolicy::class);
        Gate::policy(SavedFile::class, SavedFilePolicy::class);
        Gate::policy(Document::class, DocumentPolicy::class);
        Gate::policy(Announcement::class, AnnouncementPolicy::class);
    }
}
