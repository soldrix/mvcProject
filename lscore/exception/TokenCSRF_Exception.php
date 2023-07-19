<?php

namespace App\lscore\exception;

class TokenCSRF_Exception extends \Exception
{
    protected $message = 'Invalid or missing CSRF token !';
    protected $code = 403;
}