<?php

namespace App\lscore\exception;

class NotAuthenticatedException extends \Exception
{
    protected $message = 'You are not authenticated.';
    protected $code = 401;
}