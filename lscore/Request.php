<?php

namespace App\lscore;


/**
 * Class Request
 *
 * @package App\lscore
 * */
class Request
{
    public function getHeaders($headerName)
    {
        $headers = apache_request_headers();
        return $headers[$headerName] ?? null;
    }
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
                $body->$key = $this->getBody()->$key ?? null;
        }
        return $body;

    }

    public function getBody()
    {
        $body = new \stdClass();
        $json = file_get_contents('php://input');
        $json = json_decode($json);
        $json = (array) $json;
        if(!empty($json)){
            foreach ($json as $key => $value){
                $body->$key = $value;
            }
        }
        if(isset($_FILES)){
            foreach ($_FILES as $key => $value){
                $body->$key = $value;
            }
        }
        $requestData = ($this->isPost()) ? $_POST : $_GET;
        foreach ($requestData as $key => $value) {
            $body->$key = $value;
        }
        return $body;
    }
    public function __get($key)
    {
        return $this->getBody()->$key ?? null;
    }
}