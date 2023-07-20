<?php

namespace App\lscore\exception;

class TokenAuthException extends \Exception
{
    protected $message = 'Invalid or missing token Auth !';
    protected $code = 401;
}