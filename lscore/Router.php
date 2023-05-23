<?php
namespace App\lscore;


use App\lscore\exception\NotFoundException;
use App\lscore\exception\RedirectionExecption;


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
    protected  $routesNotAuth = [];
    /**
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response )
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     *Cette fonction permet récupérer les routes de type GET et les ajoutes dans un tableau.
     * */
    public function get($path, $callback, $notAuth = false){
        if ($notAuth !== false){
            //ajoute la route dans le tableau routesNotAuth car la route n'a pas besoin de connexion.
            $this->routesNotAuth['get'][$path] = $callback;
        }else{
            //ajoute la route dans le tableau routes car la route a besoin d'une connexion.
            $this->routes['get'][$path] = $callback;
        }
    }
    /**
     *Cette fonction permet récupérer les routes de type POST et les ajoutes dans un tableau.
     * */
    public function post($path, $callback, $notAuth = false){
        if ($notAuth !== false){
            //ajoute la route dans le tableau routesNotAuth car la route n'a pas besoin de connexion.
            $this->routesNotAuth['post'][$path] = $callback;
        }else{
            //ajoute la route dans le tableau routes car la route a besoin d'une connexion.
            $this->routes['post'][$path] = $callback;
        }
    }
    public function GroupController($controller,$callable){
        $app = new class {
            public string $controller = "";
            public function get(string $name, string $fn, $auth = false){
                Application::$app->router->get($name,[$this->controller,$fn],$auth);
            }
            public function post(string $name, string $fn, $auth = false){
                Application::$app->router->post($name,[$this->controller,$fn],$auth);
            }
        };

        $app->controller = $controller;
        return $callable($app);
    }

    /**
     *Cette fonction permet de d'utiliser et verifier les routes par rapport à la requête en cours.
     * */
    public function resolve()
    {
        //Chemin de la requête
        $path = $this->request->getPath();
        //Méthode de la requête
        $method = $this->request->method();
        //Route avec connexion
        $callback = $this->routes[$method][$path] ?? null;
        //Route sans connexion
        $callbackNotAuth = $this->routesNotAuth[$method][$path] ?? null ;
        //Vérifie si une connexion n'existe pas.
        if(Application::$app->session->get('authStatus') === null){
            //change de callback si le callback sans connexion est vide par la page de connexion.
            $callback =  $callbackNotAuth ?? $this->routesNotAuth['get']['/login'];
        }
        //Pour rediriger a la page par défaut si une connexion existe.
        else if($callback === null && $callbackNotAuth !== null){
            throw new RedirectionExecption();
        }
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
        if($callback === null && $callbackNotAuth === null){
            throw new NotFoundException();
        }
        //Pour utiliser la fonction du controller de la route
        return call_user_func($callback, $this->request);
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
        $layout = Application::$app->controller->layout ?? 'app';
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
}