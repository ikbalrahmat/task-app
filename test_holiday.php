<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Cache;
use App\Services\IndonesiaHolidayService;

// Clear cache dulu biar fresh dari API
Cache::forget('holidays_indonesia_2026');

$service = app(IndonesiaHolidayService::class);
$data    = $service->getHolidays(2026);

echo "Total hari libur 2026: " . count($data) . PHP_EOL;
echo "==============================" . PHP_EOL;

foreach ($data as $date => $h) {
    $type = $h['is_national'] ? '[NASIONAL]  ' : '[CUTI]      ';
    echo $type . $date . "  " . $h['name'] . PHP_EOL;
}
