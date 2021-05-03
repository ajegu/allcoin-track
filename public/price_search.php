<?php
/** @var Application $app */

use AllCoinTrack\Lambda\PriceSearchLambda;
use Laravel\Lumen\Application;

$app = require __DIR__ . '/../bootstrap/app.php';

return $app->make(PriceSearchLambda::class);
