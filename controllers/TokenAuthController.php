<?php

namespace App\controllers;

use App\lscore\Application;
use App\Models\TokenAuth;

class TokenAuthController extends Controller
{
    public static function createToken($id_user)
    {
        $token = Application::$app->csrfToken->generateToken(255);
        $tokenAuth = TokenAuth::find(['id_user' => $id_user]);
        if (count((array)$tokenAuth) > 0){
            error_log(json_encode($tokenAuth));
        }else{
            TokenAuth::Create([
                "id_user" => $id_user,
                "token" => $token
            ]);
        }

        return $token;
    }
    public static function getIdUser($token)
    {
        $tokenAuth = TokenAuth::find([
            "token" => $token
        ]);
        return $tokenAuth->id_user ?? null;
    }
    public static function checkToken($token)
    {
        $tokenAuth = TokenAuth::find(['token' => $token]);
        if (count((array) $tokenAuth) > 0){
            return true;
        }
        return false;
    }
}