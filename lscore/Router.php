<?php
namespace App\lscore;


use App\lscore\exception\NotFoundException;
use App\lscore\exception\RedirectionExecption;
use App\lscore\exception\unauthenticatedException;
use App\lscore\Middlewares\authMiddleware;


/**
 * Class Router
 *
 * @package App\lscore
 * */
class Router
{
    public Request $request;
    public Response $response;
    protected $routes = [];
    protected $layout = "app";
    protected $routePath = "";
    protected $Auth = "";
    /**
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response )
    {
        $this->request = $request;
        $this->response = $response;
    }
    public function setPath($path)
    {
        $this->routePath = $path;
    }
    public function getRoutePath()
    {
        return $this->routePath;
    }
    /**
     *Cette fonction permet récupérer les routes de type GET et les ajoutes dans un tableau.
     * */
    public function get($path, $callback){
        //ajoute la route dans le tableau routes car la route a besoin d'une connexion.
        $this->routes['get'.$this->Auth][$this->routePath.$path] = $callback;
    }
    /**
     *Cette fonction permet récupérer les routes de type POST et les ajoutes dans un tableau.
     * */
    public function post($path, $callback){
        $this->routes['post'.$this->Auth][$this->routePath.$path] = $callback;
    }
    public function GroupController($controller,$callable){
        $app = new class {
            public string $controller = "";
            public function get(string $name, string $fn){
                $application = Application::$app->router;
                $application->get($name,[$this->controller,$fn]);
            }
            public function post(string $name, string $fn){
                $application = Application::$app->router;
                $application->post($name,[$this->controller,$fn]);
            }
        };

        $app->controller = $controller;
        return $callable($app);
    }

    /**
     *Cette fonction permet de d'utiliser et verifier les routes par rapport à la requête en cours.
     *
     *
     */
    public function resolve()
    {

        //Chemin de la requête
        $path = $this->request->getPath();
        //Méthode de la requête
        $method = $this->request->method();
        //Route avec connexion
        $callback = $this->routes[$method][$path] ?? null;
        if($callback !== null){
            //Pour retourner une page
            if(is_string($callback)){
                return $this->renderView($callback);
            }
            //Pour récupérer la fonction du controller de la route
            if(is_array($callback)){
                $controller    = new $callback[0];
                Application::$app->controller = $controller;
                $callback[0] = $controller;
            }
            //Pour retourner une page not found si aucune route existe.
            //Pour utiliser la fonction du controller de la route
            return call_user_func($callback, $this->request);
        }
        return authMiddleware::verifyRoute();
    }
    /**
     *Cette fonction permet de retourner une page avec un layout.
     * */
    public function renderView($view, $params = [])
    {
        $layoutContent = $this->layoutContent();
        $viewContent = $this->renderOnlyView($view, $params);
        //pour ajouter le contenu d'une page dans le layout
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    /**
     *Cette fonction permet de changer le layout par rapport au layout stocker.
     * */
    protected function layoutContent()
    {
        $layout = $this->layout;
        ob_start();
        require_once Application::$ROUTE_DIR."/ressources/views/layouts/$layout.php";
        return ob_get_clean();
    }
    /**
     *Cette fonction permet de retourner une page sans layout.
     * */
    public function renderOnlyView($view, $params)
    {
        //pour créer des variables pour la page avec les params
        foreach ($params as $key => $value){
            $$key =($value !== '') ? $value : null;
        }
        ob_start();
        require_once Application::$ROUTE_DIR."/ressources/views/$view.php";
        return ob_get_clean();
    }
    public function setLayout($value = "app")
    {
        $this->layout = $value;
    }
    public function routeExist($route,$method): bool
    {
        return  ($this->routes[$method][$route] ?? null) !== null;
    }
    public function setAuthRoutes($r)
    {
        $this->Auth = $r;
    }
}