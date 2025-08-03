<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Patient;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ExportData extends Command
{
    protected $signature = 'MC:ExportData {--start-date=} {--end-date=}';
    protected $description = 'Export patients and follow-ups to JSON. Optional: --start-date=YYYY-MM-DD and --end-date=YYYY-MM-DD';

    public function handle()
    {
        $startDate = $this->option('start-date');
        $endDate = $this->option('end-date');
        $this->info('Exporting patients...');

        $query = Patient::with('followUps');

        try {
            if ($startDate) {
                $parsedStartDate = Carbon::parse($startDate)->startOfDay();
                $query->where('created_at', '>=', $parsedStartDate);
                $this->info("Filtered from date: $parsedStartDate");
            }

            if ($endDate) {
                $parsedEndDate = Carbon::parse($endDate)->endOfDay();
                $query->where('created_at', '<=', $parsedEndDate);
                $this->info("Filtered until date: $parsedEndDate");
            }
        } catch (\Exception $e) {
            $this->error("Invalid date format. Use YYYY-MM-DD.");
            return;
        }

        $patients = $query->get();

        if ($patients->isEmpty()) {
            $this->warn('No patients found for given criteria.');
        }

        $jsonData = $patients->toJson(JSON_PRETTY_PRINT);
        $fileName = 'backup-' . now()->format('Y-m-d_H-i-s') . '.json';

        Storage::put('backup/' . $fileName, $jsonData);
        // Storage::disk('public')->put($fileName, $jsonData); // Save to public disk

        $this->info("Data exported to: storage/app/private/backup/$fileName");
    }
}
