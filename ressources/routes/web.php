<?php
use App\controllers\SiteController;

$app->router->get('/login', [\App\controllers\AuthControllers::class, 'login'], true);
$app->router->get('/forgot-password', [\App\controllers\AuthControllers::class, 'forgot_password'], true);
$app->router->post('/forgot-password', [\App\controllers\AuthControllers::class, 'forgot_password'],true);

$app->router->post('/login', [\App\controllers\AuthControllers::class, 'login'],true);

$app->router->post('/logout', [\App\controllers\AuthControllers::class, 'logout']);

$app->middleware->middlewares('authMiddleware::authenticate',function($route){

    $route->get('/test', [SiteController::class, 'test']);
    $route->get('/home', [SiteController::class, 'index']);
    $route->get('/', [SiteController::class, 'redirectHome']);
    $route->GroupController(SiteController::class,function ($route){
        $route->get('/toto' , 'test');
    });
});

