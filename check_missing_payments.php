<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\FollowUp;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

$missingCount = 0;
$mismatches = [];

$followUps = DB::table('follow_ups')->where('amount_paid', '>', 0)->get();
foreach ($followUps as $fu) {
    $hasPayment = DB::table('payments')->where('follow_up_id', $fu->id)->where('status', 'posted')->exists();
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

echo "Total legacy follow-ups with amount_paid > 0 but no corresponding payments: {$missingCount}\n";
if (!empty($mismatches)) {
    echo "First 10 mismatches:\n";
    print_r($mismatches);
}
