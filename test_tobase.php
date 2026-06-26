<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Patient;

$patient = Patient::find(809);
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
echo "Type of followUp: " . get_class($firstEntry->followUp) . "\n";
echo "Amount Paid (accessor): " . $firstEntry->followUp->amount_paid . "\n";
echo "DB Column: " . DB::table('follow_ups')->where('id', $firstEntry->followUp->id)->value('amount_paid') . "\n";
