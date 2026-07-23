<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckMissingPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MC:CheckMissingPayments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for legacy follow-ups with amount_paid > 0 but no corresponding payments';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $missingCount = 0;
        $mismatches = [];

        $followUps = DB::table('follow_ups')->where('amount_paid', '>', 0)->get();
        foreach ($followUps as $fu) {
            $hasPayment = DB::table('payments')
                ->where('follow_up_id', $fu->id)
                ->where('status', 'posted')
                ->exists();

            if (!$hasPayment) {
                $missingCount++;
                if ($missingCount <= 10) {
                    $mismatches[] = [
                        'id' => $fu->id,
                        'patient_id' => $fu->patient_id,
                        'amount_billed' => $fu->amount_billed,
                        'amount_paid' => $fu->amount_paid,
                        'created_at' => $fu->created_at,
                    ];
                }
            }
        }

        $this->info("Total legacy follow-ups with amount_paid > 0 but no corresponding payments: {$missingCount}");
        
        if (!empty($mismatches)) {
            $this->info("First 10 mismatches:");
            $this->table(
                ['ID', 'Patient ID', 'Amount Billed', 'Amount Paid', 'Created At'],
                $mismatches
            );
        }

        return self::SUCCESS;
    }
}
