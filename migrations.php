<?php

require_once __DIR__ . '/vendor/autoload.php';
use App\lscore\Application;

$app = new Application(__DIR__);
if ($app->env->checkTokenAppExist()){
    $app->database->applyMigrations();
}else{
    echo "\033[0;31m Missing TOKEN_APP in .env \n please generate the token\n";
}