<?php
/** @var Application $app */

use AllCoinTrack\Lambda\BinancePriceSyncLambda;
use Laravel\Lumen\Application;

$app = require __DIR__ . '/../bootstrap/app.php';

return $app->make(BinancePriceSyncLambda::class);
