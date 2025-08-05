<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Patient;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ExportData extends Command
{
    protected $signature = 'MC:ExportData {--start-date=}';
    protected $description = 'Export patients and follow-ups to JSON. Optional: --start-date=YYYY-MM-DD';

    public function handle()
    {
        $timezone = config('app.timezone', 'Asia/Kolkata');
        $now = now()->setTimezone($timezone);

        $startDate = $this->option('start-date');
        $query = Patient::with(['followUps' => function ($q) use ($startDate) {
            if ($startDate) {
                $q->where('updated_at', '>=', $startDate);
            }
        }]);

        if ($startDate) {
            $parsedDate = Carbon::parse($startDate)->startOfDay();
            $query->where(function ($q) use ($parsedDate) {
                $q->where('updated_at', '>=', $parsedDate);
            });
        }

        $patients = $query->get();

        if ($patients->isEmpty()) {
            $this->warn('No patients found for given criteria.');
            return;
        }

        // Normalize follow-up timestamps
        $exported = $patients->map(function ($patient) use ($timezone) {
            $data = $patient->toArray();

            $data['follow_ups'] = collect($data['follow_ups'])->map(function ($fup) use ($timezone) {
                $fup['created_at'] = Carbon::parse($fup['created_at'])->setTimezone($timezone)->toDateTimeString();
                $fup['updated_at'] = Carbon::parse($fup['updated_at'])->setTimezone($timezone)->toDateTimeString();
                return $fup;
            });

            return $data;
        });

        $jsonData = json_encode($exported, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $fileName = 'backup-' . now()->format('Y-m-d_H-i-s') . '.json';

        Storage::put("backup/{$fileName}", $jsonData);
        // Storage::disk('public')->put($fileName, $jsonData); // Save to public disk

        $this->info("Data exported to: storage/app/private/backup/$fileName");
        // $this->info("Data exported to: storage/app/backup/{$fileName}");
    }
}
