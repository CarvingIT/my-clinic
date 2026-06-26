<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$followUps = DB::table('follow_ups')->get();
$mismatchCount = 0;

foreach ($followUps as $fu) {
    $dbAmountPaid = (float)$fu->amount_paid;
    $paymentsSum = (float)DB::table('payments')->where('follow_up_id', $fu->id)->where('status', 'posted')->sum('amount');
    
    if (abs($dbAmountPaid - $paymentsSum) > 0.01) {
        $mismatchCount++;
        if ($mismatchCount <= 20) {
            echo "FollowUp ID: {$fu->id}, Patient ID: {$fu->patient_id}\n";
            echo "  DB Column amount_paid: {$dbAmountPaid}\n";
            echo "  Sum of Linked Payments: {$paymentsSum}\n";
        }
    }
}

echo "Total mismatched follow-ups: {$mismatchCount}\n";
