<?php

require_once __DIR__ . '/../vendor/autoload.php';
use App\controllers\SiteController;
use App\lscore\Application;

$app = new Application(dirname(__DIR__));

$app->router->get('/', [SiteController::class, 'index']);

$app->router->get('/contact', [SiteController::class, 'contact']);

$app->router->post('/contact', [SiteController::class, 'HandleContact']);

$app->router->get('/login', [\App\controllers\AuthControllers::class, 'login']);

$app->router->post('/login', [\App\controllers\AuthControllers::class, 'login']);

$app->router->get('/register', [\App\controllers\AuthControllers::class, 'register']);

$app->router->post('/register', [\App\controllers\AuthControllers::class, 'register']);

$app->run();