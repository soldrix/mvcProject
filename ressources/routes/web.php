<?php
use App\controllers\SiteController;
$app->router->get('/', [SiteController::class, 'redirectLogin']);
$app->router->get('/login', [\App\controllers\AuthController::class, 'login']);
$app->router->get('/forgot-password', [\App\controllers\AuthController::class, 'forgot_password']);
$app->router->post('/forgot-password', [\App\controllers\AuthController::class, 'forgot_password']);

$app->router->post('/login', [\App\controllers\AuthController::class, 'sendFormLogin']);

$app->router->post('/logout', [\App\controllers\AuthController::class, 'logout']);
$app->router->get('/register', [\App\controllers\AuthController::class, "registerForm"]);
$app->router->post('/register', [\App\controllers\AuthController::class, "register"]);
$app->router->get('/gettt', [SiteController::class, 'testApi']);

$app->middleware->middlewares('authMiddleware::authenticate',function($route){
    $route->get('/contact', [SiteController::class, 'test']);
    $route->get('/home', [SiteController::class, 'index']);
    $route->get('/', [SiteController::class, 'redirectHome']);
    $route->GroupController(SiteController::class,function ($route){
        $route->get('/toto' , 'test');
        $route->post('/contact' , 'HandleContact');
    });
});

