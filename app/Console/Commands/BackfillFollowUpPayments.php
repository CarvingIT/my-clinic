<?php

namespace App\Console\Commands;

use App\Models\FollowUp;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Console\Command;

class BackfillFollowUpPayments extends Command
{
    protected $signature = 'MC:BackfillFollowUpPayments
                            {--dry-run : Show what would be inserted without writing}
                            {--chunk=200 : Chunk size for processing}
                            {--from-id=1 : Start from a follow_up id}';

    protected $description = 'Backfill legacy follow-up amount_paid data into payments table safely';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $chunkSize = max(1, (int) $this->option('chunk'));
        $fromId = max(1, (int) $this->option('from-id'));

        $this->info($dryRun ? 'Running dry-run backfill...' : 'Running backfill...');

        $query = FollowUp::query()
            ->where('id', '>=', $fromId)
            ->where('amount_paid', '>', 0)
            ->orderBy('id');

        $processed = 0;
        $inserted = 0;
        $skipped = 0;

        $query->chunkById($chunkSize, function ($followUps) use (&$processed, &$inserted, &$skipped, $dryRun) {
            foreach ($followUps as $followUp) {
                $processed++;

                $exists = Payment::where('follow_up_id', $followUp->id)
                    ->whereIn('source', ['followup_legacy', 'followup_auto'])
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                if ($dryRun) {
                    $inserted++;
                    continue;
                }

                $checkUpInfo = json_decode($followUp->check_up_info ?? '{}', true) ?: [];
                $receivedBy = User::whereKey($followUp->doctor_id)->exists() ? $followUp->doctor_id : null;

                Payment::create([
                    'patient_id' => $followUp->patient_id,
                    'follow_up_id' => $followUp->id,
                    'received_by' => $receivedBy,
                    'amount' => $followUp->amount_paid,
                    'payment_method' => $checkUpInfo['payment_method'] ?? 'cash',
                    'paid_at' => $followUp->created_at,
                    'status' => 'posted',
                    'reference_no' => null,
                    'notes' => 'Backfilled from follow_ups.amount_paid',
                    'branch_id' => $checkUpInfo['branch_id'] ?? null,
                    'branch_name' => $checkUpInfo['branch_name'] ?? null,
                    'source' => 'followup_legacy',
                ]);

                $inserted++;
            }
        });

        $this->line('Processed: ' . $processed);
        $this->line($dryRun ? 'Would insert: ' . $inserted : 'Inserted: ' . $inserted);
        $this->line('Skipped (already exists): ' . $skipped);

        $this->info('Done.');

        return self::SUCCESS;
    }
}
