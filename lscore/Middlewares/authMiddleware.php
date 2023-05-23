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
        }
    }

}