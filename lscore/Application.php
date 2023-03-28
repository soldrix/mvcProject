<?php

namespace App\lscore;
use App\controllers\Controller;

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

    public function __construct($rootPath)
    {
        self::$ROUTE_DIR = $rootPath;
        self::$app = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->router  = new Router($this->request, $this->response);
    }

    public function run()
    {

        $value = $this->router->resolve();
        if(gettype($value) !== 'string'){
            if(json_last_error() === JSON_ERROR_NONE){
                $value = json_encode($value);
                Header('Content-type: application/json');
            }
        }
            echo $value;

    }

    /**
     * @return Controller
     */
    public function getController(): Controller
    {
        return $this->controller;
    }

    /**
     * @param Controller $controller
     */
    public function setController(Controller $controller): void
    {
        $this->controller = $controller;
    }
}