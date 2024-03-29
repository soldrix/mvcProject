<?php
namespace App\controllers;

use App\lscore\Application;

/**
 * Class controller
 *
 * @package App\controllers
 * */
class Controller
{
    public function setLayout($layout = "app")
    {
        Application::$app->router->setLayout($layout);
    }

    public function render($view, $params = [])
    {
        return Application::$app->router->renderView($view, $params);
    }
    public function redirect($url)
    {
        Application::$app->response->redirect($url);
    }
    public function isAuth(){
        return Application::$app->isGuest() === false;
    }
    public function response(int $code, $data)
    {
        Application::$app->response->setStatusCode($code);
        return $data;
    }
}