<?php

use App\controllers\SiteController;

$app->router->get('/jean', [SiteController::class, 'testApi']);
$app->router->get('/users', [\App\controllers\AuthController::class, 'getUsers']);
$app->router->post('/usersUpdate', [\App\controllers\AuthController::class, 'userUpdate']);

$app->router->post('/login', [\App\controllers\API\AuthController::class, 'login']);
$app->router->post('/register', [\App\controllers\API\AuthController::class, 'register']);

$app->router->get('/findUser', [\App\controllers\AuthController::class, 'findUser']);
$app->router->get('/test', [\App\controllers\AuthController::class, 'testJoin']);
$app->router->post('/delete/user', [\App\controllers\AuthController::class, 'deleteUser']);
$app->router->post('/testPost', [SiteController::class, 'HandleContact']);

$app->middleware->middlewares('authMiddleware::authenticate',function($route){
    $route->post('/jean', [SiteController::class, 'HandleContact']);
    $route->get('/testAuth', [\App\controllers\API\AuthController::class, 'testAuth']);
});

