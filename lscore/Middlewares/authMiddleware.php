<?php

namespace App\lscore\Middlewares;

use App\lscore\Application;
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
        if (Application::$app->request->getHeaders('Accept') === 'application/json'){
            Header('Content-type: application/json');
            if(Application::$app->router->routeExist($path,$method.'Auth')){
                return ["message" => "not Authenticated !"];
            }
            return ["message" => "Route not found !"];
        }
        if (Application::$app->session->get('authStatus') !== null){
            return throw new NotFoundException();
        }
        return "";
    }
    public function __destruct()
    {
        Application::$app->router->setAuthRoutes('');
    }
}