<?php

namespace App\lscore;
use App\controllers\Controller;
use App\lscore\exception\NotFoundException;
use App\lscore\exception\TokenAppException;
use App\lscore\exception\TokenCSRF_Exception;
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
                throw new TokenAppException();
            }elseif(!isset($requestTokenApp)){
                $CSRF_Request = $_POST["csrf-token"] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? "";
                $value = $this->router->resolve();
                if($this->request->method() === "post" && $CSRF_Request  !== $this->csrfToken->getToken() || ($value !== null && gettype($value) !== 'string')){
                    throw new TokenCSRF_Exception();
                }
            }else{
                $value = $this->router->resolve();
            }
            if(empty($value) && !isset($requestTokenApp) && !str_contains($this->request->getPath(),'api')){
                $this->response->redirect('/login');
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
            $path = "web";
            if(str_contains($this->request->getPath(),'api')){
                $path = explode("/", $this->request->getPath());
                $path = $path[1];
            }
            $this->response->setStatusCode($e->getCode());
            if ($path === "api"){
                echo $e->getMessage();
            }else{
                echo $this->router->renderView("_404",[
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