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
        $authToken = Application::$app->session->get('authToken');
        if($authToken === null || str_contains($authToken, "null"))
        {
            Application::$app->router->setAuthRoutes('Auth');
        }
        return $callable(Application::$app->router);
    }

    /**
     * @throws NotFoundException
     *
     */
    public static function verifyRoute()
    {
        $method = Application::$app->request->method();
        $path = Application::$app->request->getPath();
        $authToken = Application::$app->session->get('authToken');
        if (Application::$app->router->routeExist($path, $method))
        {
            return false;
        }
        if (Application::$app->router->routeExist($path, $method . 'Auth'))
        {
            if ($authToken !== null)
            {
                Application::$app->router->setAuthRoutes('Auth');
            } else
            {
                Application::$app->response->redirect('/login');
            }
            return false;
        }
        throw new NotFoundException();
    }
    public function __destruct()
    {
        Application::$app->router->setAuthRoutes('');
    }
}