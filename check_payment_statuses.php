<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$statuses = DB::table('payments')->select('status', DB::raw('count(*) as count'))->groupBy('status')->get();
foreach ($statuses as $status) {
    echo "Status: {$status->status}, Count: {$status->count}\n";
}
