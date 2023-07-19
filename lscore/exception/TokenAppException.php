<?php

namespace App\lscore\exception;

class TokenAppException extends \Exception
{
    protected $message = 'Invalid or missing token App !';
    protected $code = 403;
}