<?php

namespace App\lscore;

class Env
{
    private $fileName = "";
    public function __construct()
    {
        $this->fileName = dirname(__DIR__). '/.env';
    }
    public function loadEnv()
    {
        $contents = file_get_contents($this->fileName);
        $searchContents = explode("\n", $contents);
        $searchContents = array_filter($searchContents);
        foreach ($searchContents as $value) {
            $value = explode("=", str_replace(" ", "", $value));
            $name = $value[0];
            $data = $value[1];
            $_ENV[$name] = $data;
        }
    }
    public function checkTokenAppExist()
    {
        $contents = file_get_contents($this->fileName);
        $searchContents = explode("\n", $contents);
        $searchContents = array_filter($searchContents);
        foreach ($searchContents as $value){
            $valueEnv = explode("=", str_replace(" ", "", $value));
            if (str_contains("TOKEN_APP", $valueEnv[0])){
                return $value;
            }
        }
        return false;
    }
    public function addTokenApp()
    {
        if(file_exists($this->fileName)){
            $tokenApp = Application::$app->csrfToken->generateToken(255);
            $contents = file_get_contents($this->fileName);
            $oldVal = $this->checkTokenAppExist();
            $newValue  =  "TOKEN_APP = ". $tokenApp;
            if ($oldVal !== false){
                $contents = str_replace($oldVal, $newValue, $contents);
                file_put_contents($this->fileName, $contents);
            }else{
                file_put_contents($this->fileName, $contents.$newValue);
            }
            echo "\033[0;32m TOKEN_APP generated !\n";
            exit();
        }
        echo "\033[0;31m .env does not exist !";
    }
}