<?php
namespace App\controllers;

use App\lscore\Application;

/**
 * Class controller
 *
 * @package App\controllers
 * */
class Controller
{
    public function setLayout($layout = "app")
    {
        Application::$app->router->setLayout($layout);
    }
    /**
     * ⚠ Astuce : Vous pouvez modifier le titre de l'onglet du navigateur en passant une clé `"onglet_title"` dans `$params`.
     *
     *  Exemple :
     *  `$this->render('home', ["onglet_title" => "Ma page d'accueil"]);`
     *
     * @param string $view Le nom de la vue à charger.
     * @param array $params Un tableau associatif [nom_de_variable => valeur].
     *                      - Clé (`nom_de_variable`) : doit être une chaîne de caractères (`string`).
     *                      - Valeur (`valeur`) : peut être de n'importe quel type (`mixed`).
     */
    public function render($view, $params = [])
    {
        return Application::$app->router->renderView($view,$params);
    }
    public function redirect($url)
    {
        Application::$app->response->redirect($url);
    }
    public function isAuth(){
        return Application::$app->isGuest() === false;
    }
    public function response(int $code, $data)
    {
        Application::$app->response->setStatusCode($code);
        return $data;
    }
}