<?php

namespace App\lscore;
use App\controllers\Controller;
use App\lscore\exception\NotFoundException;
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
    public csrfToken $csrfToken;
    public function __construct($rootPath)
    {
        self::$ROUTE_DIR = $rootPath;
        self::$app = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router  = new Router($this->request, $this->response);
        $this->middleware = new middleware();
        $this->csrfToken = new csrfToken();
    }
    public function run()
    {
        $this->router->setLayout(($this->isGuest()) ? "auth" : "app");
        try {
            $value = $this->router->resolve();


            //pour changer le type de contenu de la requête
            if(gettype($value) !== 'string' && $value = ""){
                json_encode($value);
                if(json_last_error() === JSON_ERROR_NONE){
                    if(!str_contains($this->request->getPath(),"api")){
                        if(($_POST["csrf-token"] ?? $_SERVER['HTTP_X_CSRF_TOKEN'])  !== Application::$app->csrfToken->getToken()){
                            $this->response->setStatusCode(403);
                            $value = [
                                "error" => "Invalid or missing CSRF token"
                            ];
                        }
                    }
                    $value = json_encode($value);
                    Header('Content-type: application/json');
                }
            }else{
                Header('Content-type: text/html');
            }
            echo $value;
        }catch (\Exception $e){
            if(!str_contains($this->request->getPath(),"api")){
                $view = ($this->isGuest()) ? "login" : "_404";
                $this->response->setStatusCode(($this->isGuest()) ? 308 : $e->getCode());
                echo $this->router->renderView($view,[
                    "exceptions" => $e
                ]);
            }else{
                echo "Route not found.";
            }

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
        $this->csrfToken->resetToken();
        $this->session->remove('authStatus');
    }
    public function isGuest()
    {
        return !$this->session->get('authStatus');
    }
}