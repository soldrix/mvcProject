<?php

require_once __DIR__ . '/vendor/autoload.php';
use App\lscore\Application;

$app = new Application(__DIR__);
$app->env->addTokenApp();