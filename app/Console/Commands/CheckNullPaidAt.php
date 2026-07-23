<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckNullPaidAt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MC:CheckNullPaidAt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for payments with null paid_at date';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $nullPaidAt = DB::table('payments')->whereNull('paid_at')->count();
        $totalPayments = DB::table('payments')->count();

        $output = "Total Payments: {$totalPayments}\n";
        $output .= "Payments with null paid_at: {$nullPaidAt}\n";

        if ($nullPaidAt > 0) {
            $nullPayments = DB::table('payments')->whereNull('paid_at')->limit(10)->get();
            $output .= "First 10 null paid_at payments:\n" . print_r($nullPayments, true) . "\n";
        }

        $this->line("Total Payments: {$totalPayments}");
        $this->line("Payments with null paid_at: {$nullPaidAt}");

        if ($nullPaidAt > 0) {
            $this->line("First 10 null paid_at payments:");
            $this->line(print_r($nullPayments, true));
        }

        file_put_contents(base_path('check_output.txt'), $output);
        $this->info("Done");

        return self::SUCCESS;
    }
}
