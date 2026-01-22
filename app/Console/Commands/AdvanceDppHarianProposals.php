<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Proposal;
use Illuminate\Support\Facades\Schedule;

class AdvanceDppHarianProposals extends Command
{
    protected $signature = 'proposals:advance-dpp-harian';
    protected $description = 'Memindahkan proposal dari DPP Harian ke tahap Romo jika deadline sudah lewat';

    public function handle(): int
    {
        $count = Proposal::query()
            ->where('stage', 'dpp_harian')
            ->where('status', 'dpp_harian')
            ->whereNotNull('dpp_harian_until')
            ->where('dpp_harian_until', '<=', now())
            ->update([
                'stage'  => 'romo',
                'status' => 'menunggu_romo',
            ]);

        $this->info("Advanced: {$count} proposal(s) to romo stage.");
        return Command::SUCCESS;
        
        Schedule::command('proposals:advance-dpp-harian')->everyMinute();
    }
}