<?php

namespace App\lscore\exception;

class NotFoundException extends \Exception
{
    protected $message = "Route not found.";
    protected $code = 404;
}