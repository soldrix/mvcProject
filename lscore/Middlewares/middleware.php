<?php

namespace App\lscore\Middlewares;

use App\lscore\Application;

class middleware
{
    public function middlewares(string $name,$callable){

        $splitText = explode("::",$name) ?? null;
        if (is_array($splitText)){
            $class = "App\lscore\Middlewares\\".$splitText[0];
            if(class_exists($class)){
                $toto = new $class();
                call_user_func(array($toto, $splitText[1]),$callable);
            }
        }
    }
}