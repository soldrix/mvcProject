<?php

namespace App\lscore;
use App\controllers\Controller;
use App\lscore\exception\NotFoundException;
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
    public Env $env;
    private $tokenApp = "";
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
        $this->env = new Env();
        $this->env->loadEnv();
        $this->tokenApp = $_ENV["TOKEN_APP"];
        $this->database = new Database();
        if($this->session->get("CSRF_token") === null){
            $this->session->set('CSRF_token', $this->csrfToken->generateToken(255));
        }
    }
    public function run()
    {
        $this->router->setLayout(($this->isGuest()) ? "auth" : "app");
        $requestTokenApp = Application::$app->request->getHeaders('AuthorizationApp');
        try {
            if($requestTokenApp !== $this->tokenApp && str_contains($this->request->getPath(),'api')){
                $value = [
                    "error" => "Invalid or missing tokenApp !"
                ];
            }elseif(!isset($requestTokenApp)){
                $CSRF_Request = $_POST["csrf-token"] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? "";
                $value = $this->router->resolve();
                if($this->request->method() === "post" && $CSRF_Request  !== $this->csrfToken->getToken() || ($value !== null && gettype($value) !== 'string')){
                    $this->response->setStatusCode(403);
                    $value = [
                        "error" => "Invalid or missing CSRF token"
                    ];
                }
            }else{
                $value = $this->router->resolve();
            }
            if(empty($value)){
                if (!isset($requestTokenApp) && !str_contains($this->request->getPath(),'api')){
                    $this->response->redirect('/login');
                }else{
                    throw new NotFoundException();
                }
            }
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
            $this->response->setStatusCode($e->getCode());
            if (!isset($requestTokenApp)){
                echo $this->router->renderView("_404",[
                    "exceptions" => $e
                ]);
            }elseif ($requestTokenApp !== $this->tokenApp){
                $value =  [
                    "error" => "Invalid or missing CSRF token"
                ];
                echo json_encode($value);
            }else{
                echo $e->getMessage();
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