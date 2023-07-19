<?php

namespace App\lscore;

class Env
{
    public function loadEnv()
    {
        $contents = file_get_contents(dirname(__DIR__). '/.env');
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