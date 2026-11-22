<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "CALLING MIGRATE...\n";
    $status = \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    echo "MIGRATE STATUS: " . $status . "\n";
    echo \Illuminate\Support\Facades\Artisan::output() . "\n";

    echo "CALLING DB:SEED...\n";
    $seedStatus = \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
    echo "SEED STATUS: " . $seedStatus . "\n";
    echo \Illuminate\Support\Facades\Artisan::output() . "\n";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
