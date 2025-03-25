<?php

require_once __DIR__ . '/../vendor/autoload.php';
use App\lscore\Application;
$app = new Application(dirname(__DIR__));
require_once __DIR__ . '/../ressources/routes/web.php';

$app->run();