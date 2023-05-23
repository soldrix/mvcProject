<?php

namespace App\lscore\exception;

class ForbiddenExecption extends \Exception
{
    protected $message = 'You are not authorized to access this page.';
    protected $code = 401;
}