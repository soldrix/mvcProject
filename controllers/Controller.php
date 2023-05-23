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
    public String $layout = 'app';
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    public function render($view, $params = [])
    {
        return Application::$app->router->renderView($view, $params);
    }
    public function redirect($url)
    {
        Application::$app->response->redirect($url);
    }
    public function getAuth(){
        return Application::$app->isGuest();
    }
}