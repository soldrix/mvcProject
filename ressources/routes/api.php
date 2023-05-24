<?php

use App\controllers\SiteController;

$app->router->setPath("/".basename(__FILE__, '.php'));

$app->router->get('/jean', [SiteController::class, 'testApi']);
$app->router->post('/jean', [SiteController::class, 'HandleContact']);