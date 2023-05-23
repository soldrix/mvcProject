<?php

namespace App\lscore\Middlewares;

use App\lscore\Application;

class authMiddleware extends middleware
{

    public function authenticate($callable)
    {
        return $callable(Application::$app->router);
    }

}