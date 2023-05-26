<?php

namespace App\lscore;

class Response
{
    public function setStatusCode(int $code)
    {
        http_response_code($code);
    }
    public function redirect($url):void
    {
        Header('Location: '.$url);
        die();
    }
}