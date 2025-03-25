<?php
namespace App\lscore\exception;

class CustomException extends \Exception
{
    protected $message = "";
    protected $code = 0;
}