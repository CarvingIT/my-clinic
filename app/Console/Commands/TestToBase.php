<?php

namespace App\Console\Commands;

use App\Models\Patient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestToBase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MC:TestToBase {patient_id=809}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test toBase() conversion and amount_paid accessor resolution';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $patientId = $this->argument('patient_id');
        $patient = Patient::find($patientId);

        if (!$patient) {
            $patient = Patient::first();
        }

        if (!$patient) {
            $this->error('No patients found in the database.');
            return self::FAILURE;
        }

        $this->info("Testing for Patient ID: {$patient->id}");

        $timelineEntries = $patient->followUps()
            ->with('uploads')
            ->get()
            ->toBase()
            ->map(function ($followUp) {
                return (object) [
                    'type' => 'followup',
                    'date' => $followUp->created_at,
                    'followUp' => $followUp,
                ];
            });

        $firstEntry = $timelineEntries->first();
        if (!$firstEntry) {
            $this->warn('This patient has no follow-ups.');
            return self::SUCCESS;
        }

        $this->line("Type of followUp: " . get_class($firstEntry->followUp));
        $this->line("Amount Paid (accessor): " . $firstEntry->followUp->amount_paid);
        $this->line("DB Column: " . DB::table('follow_ups')->where('id', $firstEntry->followUp->id)->value('amount_paid'));

        return self::SUCCESS;
    }
}
