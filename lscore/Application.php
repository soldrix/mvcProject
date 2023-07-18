<?php

namespace App\lscore;
use App\controllers\Controller;
use App\lscore\Middlewares\middleware;
use App\Models\Users;

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
    public Database $database;
    public function __construct($rootPath, array $config)
    {
        self::$ROUTE_DIR = $rootPath;
        self::$app = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router  = new Router($this->request, $this->response);
        $this->middleware = new middleware();
        $this->csrfToken = new csrfToken();
        $this->database = new Database($config['db']);
        if($this->session->get("CSRF_token") === null){
            $this->session->set('CSRF_token', $this->csrfToken->generateToken(255));
        }
    }
    public function run()
    {
        $this->router->setLayout(($this->isGuest()) ? "auth" : "app");
        try {
            if(Application::$app->request->getHeaders('Accept') !== 'application/json'){
                $CSRF_Request = $_POST["csrf-token"] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? "";
                if($this->request->method() === "post" && $CSRF_Request  !== $this->csrfToken->getToken()){
                    $this->response->setStatusCode(403);
                    $value = [
                        "error" => "Invalid or missing CSRF token"
                    ];
                }else{
                    $value = $this->router->resolve();
                    if($value !== null && gettype($value) !== 'string'){
                        $this->response->setStatusCode(403);
                        $value = [
                            "error" => "Invalid or missing CSRF token"
                        ];
                    }
                }
            }else{
                $value = $this->router->resolve();
            }
            if(empty($value) && $this->request->getHeaders('Accept') !== 'application/json'){
                $this->response->redirect('/login');
            }
            //pour changer le type de contenu de la requête
            if(gettype($value) !== 'string' && $value != ""){
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
            if(Application::$app->request->getHeaders("Content-Type") ?? null === "application/json" ){
                echo $e->getMessage();
            }else{
                $view =  "_404";
                echo $this->router->renderView($view,[
                    "exceptions" => $e
                ]);
            }
        }
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
    public function UserID()
    {
        return Application::$app->session->get('userID');
    }
}