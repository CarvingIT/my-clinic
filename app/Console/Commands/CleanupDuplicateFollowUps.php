<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SyncService;

class CleanupDuplicateFollowUps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MC:CleanupDuplicates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove duplicate follow-ups, keeping only the most recent for each patient+content combination';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $syncService = app(SyncService::class);

        $this->info('Starting cleanup of duplicate follow-ups...');

        $removed = $syncService->cleanupDuplicateFollowUps();

        $this->info("Cleanup completed. Removed {$removed} duplicate follow-ups.");
    }
}
