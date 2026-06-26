<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$nullPaidAt = DB::table('payments')->whereNull('paid_at')->count();
$totalPayments = DB::table('payments')->count();

$output = "Total Payments: {$totalPayments}\n";
$output .= "Payments with null paid_at: {$nullPaidAt}\n";

if ($nullPaidAt > 0) {
    $nullPayments = DB::table('payments')->whereNull('paid_at')->limit(10)->get();
    $output .= "First 10 null paid_at payments:\n" . print_r($nullPayments, true) . "\n";
}

file_put_contents(__DIR__ . '/check_output.txt', $output);
echo "Done\n";
