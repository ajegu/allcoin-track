<?php
/** @var Application $app */

use AllCoinTrack\Lambda\BinanceAssetSyncLambda;
use Laravel\Lumen\Application;

$app = require __DIR__ . '/../bootstrap/app.php';

return $app->make(BinanceAssetSyncLambda::class);
