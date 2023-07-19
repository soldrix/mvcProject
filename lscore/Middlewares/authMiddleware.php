<?php

namespace App\lscore\Middlewares;

use App\lscore\Application;
use App\lscore\exception\ForbiddenExecption;
use App\lscore\exception\NotAuthenticatedException;
use App\lscore\exception\NotFoundException;

class authMiddleware extends middleware
{

    public function authenticate($callable)
    {
        if(Application::$app->session->get('authStatus') !== null){
            //change de callback si le callback sans connexion est vide par la page de connexion.
            return $callable(Application::$app->router);
        }else{
            Application::$app->router->setAuthRoutes('Auth');
            return $callable(Application::$app->router);
        }
    }
    public static function verifyRoute()
    {
        $method = Application::$app->request->method();
        $path = Application::$app->request->getPath();
        if (Application::$app->session->get('authStatus') !== null){
            return throw new NotFoundException();
        }else{
            if(Application::$app->router->routeExist($path,$method.'Auth')){
                return throw new NotAuthenticatedException();
            }
            if (!str_contains($path,'api')){
                return "";
            }
            return throw new NotFoundException();
        }
    }
    public function __destruct()
    {
        Application::$app->router->setAuthRoutes('');
    }
}