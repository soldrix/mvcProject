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
    public Session $session;

    public function __construct($rootPath)
    {
        self::$ROUTE_DIR = $rootPath;
        self::$app = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router  = new Router($this->request, $this->response);
    }

    public function run()
    {
        try {
            $value = $this->router->resolve();
            //pour changer le type de contenu de la requête
            if(gettype($value) !== 'string'){
                json_encode($value);
                if(json_last_error() === JSON_ERROR_NONE){
                    $value = json_encode($value);
                    Header('Content-type: application/json');
                }
            }else{
                Header('Content-type: text/html');
            }
            echo $value;
        }catch (\Exception $e){
            $this->response->setStatusCode($e->getCode());
            echo $this->router->renderView('_404',[
                "exceptions" => $e
            ]);
        }
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

    public function login()
    {
        $this->session->set("authStatus",true);
    }

    public function logout()
    {
        $this->session->remove('authStatus');
    }
    public function isGuest()
    {
        return !$this->session->get('authStatus');
    }
}