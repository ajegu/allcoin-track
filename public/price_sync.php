<?php
/** @var Application $app */

use AllCoinTrack\Lambda\PriceSyncLambda;
use Laravel\Lumen\Application;

$app = require __DIR__ . '/../bootstrap/app.php';

return $app->make(PriceSyncLambda::class);
