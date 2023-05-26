<?php

use App\controllers\SiteController;

$app->router->setPath("/".basename(__FILE__, '.php'));

$app->router->get('/jean', [SiteController::class, 'testApi']);
$app->router->get('/users', [\App\controllers\AuthControllers::class, 'getUsers']);
$app->router->get('/findUser', [\App\controllers\AuthControllers::class, 'findUser']);
$app->router->post('/jean', [SiteController::class, 'HandleContact']);
$app->router->post('/delete/user', [\App\controllers\AuthControllers::class, 'deleteUser']);