<?php
/** @var Application $app */

use AllCoinTrack\Lambda\AssetSyncLambda;
use Laravel\Lumen\Application;

$app = require __DIR__ . '/../bootstrap/app.php';

return $app->make(AssetSyncLambda::class);
