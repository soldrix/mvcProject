<?php

namespace App\lscore\exception;

class RedirectionExecption extends \Exception
{
    protected $code = 301;
    protected $message = "Already authenticated.";
}