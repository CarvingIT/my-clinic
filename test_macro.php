<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    PDF::macro('loadHTMLWithMinFont', function($html) {
        return "MACRO_OK";
    });
    echo "MACRO SUPPORTED\n";
} catch (\Exception $e) {
    echo "NO MACRO SUPPORT\n";
}
