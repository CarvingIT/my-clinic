<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SyncService;

class SyncData extends Command
{
    protected $signature = 'MC:SyncData {--date= : The date to sync data for (YYYY-MM-DD)} {--username= : API username} {--password= : API password}';
    protected $description = 'Sync patients and follow-ups from online API for a specific date';

    public function handle()
    {
        $date = $this->option('date');
        $username = $this->option('username');
        $password = $this->option('password');

        // Prompt for date if not provided
        if (!$date) {
            $date = $this->ask('Enter the date to sync data for (YYYY-MM-DD)');
        }

        // Validate date format
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $this->error('Invalid date format. Please use YYYY-MM-DD format.');
            return 1;
        }

        // Validate date is not in the future
        if (strtotime($date) > strtotime('today')) {
            $this->error('Cannot sync data for future dates.');
            return 1;
        }

        // Prompt for credentials if not provided
        if (!$username) {
            $username = $this->ask('Enter API username');
        }
        if (!$password) {
            $password = $this->secret('Enter API password');
        }

        if (!$username || !$password) {
            $this->error('Username and password are required.');
            return 1;
        }

        $syncService = app(SyncService::class);

        try {
            $result = $syncService->syncFromApi($date, $username, $password);

            $this->info($result['message']);

            if (!empty($result['stats'])) {
                $stats = $result['stats'];
                $this->line("Patients restored: {$stats['patients_restored']}");
                $this->line("Patients imported: {$stats['patients_imported']}");
                $this->line("Patients updated: {$stats['patients_updated']}");
                $this->line("Patients skipped: {$stats['patients_skipped']}");
                $this->line("Follow-ups added: {$stats['follow_ups_added']}");
                $this->line("Follow-ups updated: {$stats['follow_ups_updated']}");
                $this->line("Follow-ups skipped: {$stats['follow_ups_skipped']}");
            }

            $this->line("\n<fg=white;bg=blue> Sync completed successfully </>");

        } catch (\Exception $e) {
            $this->error('Sync failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
