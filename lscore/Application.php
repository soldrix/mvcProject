<?php

namespace App\lscore;
use App\controllers\Controller;
use App\lscore\exception\NotFoundException;
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
    public csrfToken $csrfToken;
    public Database $database;
    public Env $env;
    private $tokenApp = "";
    private $dataJson = false;
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
        $this->tokenApp = $_ENV["TOKEN_APP"] ?? "";
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
                $CSRF_Request = $_POST["csrf-token"] ?? $this->request->getHeaders("HTTP_X_CSRF_TOKEN") ?? "";
                if($this->request->method() === "post" && $CSRF_Request  !== $this->csrfToken->getToken()){
                   $this->dataJson = true;
                    throw new TokenCSRF_Exception();
                }else{
                    $path = explode("/", $this->request->getPath());
                    $path = array_filter($path);
                    foreach ($path as $data){
                        if ($data !== "web" && $data !== "api"){
                            $this->dataJson = true;
                            throw new NotFoundException();
                        }
                    }
                    $value = $this->router->resolve();
                }
            }else{
                $value = $this->router->resolve();
            }
            if(empty($value) && !isset($requestTokenApp) && !str_contains($this->request->getPath(),'api') && $this->request->method() !== "post"){
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
            $path = explode("/", $this->request->getPath());
            $path = array_filter($path);
            foreach ($path as $data){
                if($data === "api"){
                    $path = $data;
                }elseif ($data === "web"){
                    $path = $data;
                }
            }
            if (is_int($e->getCode())){
                $this->response->setStatusCode($e->getCode());
            }else{
                $this->response->setStatusCode(200);
            }
            if ($path === "api" || $this->dataJson){
                echo $e->getMessage();
            }else{
                if ($e->getCode() === 401){
                    $this->response->redirect("/login");
                }else{
                    echo $this->router->renderView("_404",[
                        "exceptions" => $e
                    ]);
                }
            }
        }
    }
    public function login($token)
    {
        $this->session->set("authStatus",$token);
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