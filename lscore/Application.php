<?php

namespace App\lscore;
use App\controllers\Controller;
use App\lscore\exception\CustomException;
use App\lscore\exception\TokenAppException;
use App\lscore\exception\TokenCSRF_Exception;
use App\lscore\Middlewares\middleware;

/**
 * Class Application
 *
 * @package App\lscore
 * */
class Application
{
    public static string $ROUTE_DIR;
    public Router $router;
    public Request $request;
    public Response $response;
    public static Application $app;
    public Controller $controller;
    public Session $session;
    public middleware $middleware;
    public Env $env;
    public function __construct($rootPath)
    {
        self::$ROUTE_DIR = $rootPath;
        self::$app = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router  = new Router($this->request, $this->response);
        $this->middleware = new middleware();
        try{
            $this->env = new Env();
                $this->env->loadEnv();
        }catch (\Exception $e){
            $this->router->setLayout(($this->isGuest()) ? "auth" : "app");
            $this->response->setStatusCode($e->getCode() | 200);
            echo $this->router->renderView("_404",[
                "exceptions" => $e
            ]);
            die();
        }
    }
    public function run(): void
    {
        $this->router->setLayout(($this->isGuest()) ? "auth" : "app");
        try {
            $value = $this->router->resolve();
            //pour changer le type de contenu de la requête
            if(gettype($value) !== 'string' && $value != ""){
                json_encode($value);
                if(json_last_error() === JSON_ERROR_NONE){
                    $value = json_encode($value);
                    Header('Content-Type: application/json');
                }
            }
            else{
                Header('Content-Type: text/html');
            }
            echo $value;
        }catch (\Exception $e){
            if (!headers_sent()) {
                $this->response->setStatusCode(($e->getCode() !== 0) ? $e->getCode() : 500);
            }
            echo $this->router->renderView("_404",[
                "exceptions" => $e
            ]);
            die();
        }
    }
    public function login($token): void
    {
        $this->session->set("authToken",$token);
    }

    public function logout(): void
    {
        $this->session->remove('authToken');
    }
    public function isGuest(): bool
    {
        return !$this->session->get('authToken');
    }
}