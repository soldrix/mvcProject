<?php

require_once __DIR__ . '/../vendor/autoload.php';
use App\lscore\Application;
$app = new Application(dirname(__DIR__));
$app->router->setPath("/web");
require_once __DIR__ . '/../ressources/routes/web.php';
$app->router->setPath("/api");
require_once __DIR__ . '/../ressources/routes/api.php';

$app->run();