<?php

namespace App\lscore;


/**
 * Class Request
 *
 * @package App\lscore
 * */
class Request
{
    /**
     * Cette fonction permet de récupérer le chemin de la requête en cours.
     * */
    public function getPath()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $positions = strpos($path, '?');
        if($positions === false){
            return $path;
        }
        return substr($path, 0, $positions);
    }
    /**
     * Cette fonction permet de récupérer la méthode de la requête en cours.
     * */
    public function method()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    /**
     *Cette fonction permet de vérifier le type de la requête en cours.
     * */
    public function isGet()
    {
        return $this->method() === 'get';
    }

    /**
     *Cette fonction permet de vérifier le type de la requête en cours.
     * */
    public function isPost()
    {
        return $this->method() === 'post';
    }

    public function only(string|array $data): \stdClass
    {
        $body  = new \stdClass();
        $data = (gettype($data) === "string") ? [$data] : $data;
        foreach ($data as $key){
                $body->$key = $this->getBody()->$key;
        }
        return $body;

    }

    public function getBody()
    {
        $body = new \stdClass();
       foreach (($this->method() === "post") ? $_POST : $_GET as $key => $value) {
            $body->$key = $value;
       }
        return $body;
    }
    public function __get($key)
    {
        return $this->getBody()->$key;
    }
}