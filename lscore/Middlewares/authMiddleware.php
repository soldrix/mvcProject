<?php

namespace App\lscore\Middlewares;

use App\controllers\TokenAuthController;
use App\lscore\Application;
use App\lscore\exception\ForbiddenExecption;
use App\lscore\exception\NotAuthenticatedException;
use App\lscore\exception\NotFoundException;
use App\lscore\exception\TokenAuthException;
use App\Models\Users;

class authMiddleware extends middleware
{

    public function authenticate($callable)
    {
        $token = Application::$app->request->getHeaders('Authorization');
        if (isset($token)){
            $token = str_replace("Bearer ", '', $token);
        }
        if (!isset($token)){
            $token = Application::$app->session->get('authStatus');
        }
        if($token !== null && !str_contains($token, "null")){
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
        $token = Application::$app->request->getHeaders('Authorization');
        if (isset($token)){
            $token = str_replace("Bearer ", '', $token);
        }else{
            $token = Application::$app->session->get('authStatus');
        }
        if ($token !== null){
            if(Application::$app->router->routeExist($path,$method.'Auth')){
                Application::$app->router->setAuthRoutes('Auth');
                return false;
            }
            return throw new NotFoundException();
        }else{
            if(Application::$app->router->routeExist($path,$method.'Auth')){
                $path = array_filter(explode("/", $path));
                if (in_array("api", $path, true)) {
                    return throw new TokenAuthException();
                }
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