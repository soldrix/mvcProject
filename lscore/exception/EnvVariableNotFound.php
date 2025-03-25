<?php
namespace App\lscore\exception;

class EnvVariableNotFound extends \Exception
{
    protected $message = "La variable d'environment n'existe pas.";
    protected $code = 500;
}