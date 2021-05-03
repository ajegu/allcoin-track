<?php
/** @var Application $app */

use AllCoinTrack\Lambda\AssetListLambda;
use Laravel\Lumen\Application;

$app = require __DIR__ . '/../bootstrap/app.php';

return $app->make(AssetListLambda::class);
