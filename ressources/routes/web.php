<?php
use App\controllers\SiteController;
$app->router->get('/', [SiteController::class, 'redirectHome']);
$app->router->get('/home', [SiteController::class, 'index']);

$app->router->get('/login', [\App\controllers\AuthControllers::class, 'login'], true);
$app->router->get('/forgot-password', [\App\controllers\AuthControllers::class, 'forgot_password'], true);
$app->router->post('/forgot-password', [\App\controllers\AuthControllers::class, 'forgot_password'],true);

$app->router->post('/login', [\App\controllers\AuthControllers::class, 'login'],true);

$app->router->post('/logout', [\App\controllers\AuthControllers::class, 'logout']);


