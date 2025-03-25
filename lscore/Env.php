<?php

namespace App\lscore;

use App\lscore\exception\CustomException;

class Env
{
    private $fileName = "";
    public function __construct()
    {
        $this->fileName = dirname(__DIR__). '/.env';
    }
    public function loadEnv(): void
    {
        if (!file_exists($this->fileName)) {
            throw new CustomException("Env file '.env' not found", 404);
        }else{
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
    }
}