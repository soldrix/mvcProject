<?php

namespace App\lscore;

class Session
{

    public function __construct()
    {
        session_start();
    }
    /**
     *Cette fonction permet créer une variable de session avec une clé et une valeur.
     * */
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }
    /**
     *Cette fonction permet récupérer une variable de session avec sa clé.
     * */
    public function get($key)
    {
        return $_SESSION[$key] ?? null;
    }
    /**
     *Cette fonction permet supprimer une variable de session avec sa clé.
     * */
    public function remove($key)
    {
        unset($_SESSION[$key]);
    }



}