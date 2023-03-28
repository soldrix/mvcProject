<?php

namespace App\controllers;


use App\lscore\Request;
use App\lscore\Validation;

class AuthControllers extends Controller
{
    public function login(Request $request)
    {
        //pour changer le layout exemple auth pour le layout auth.php
        $this->setLayout('auth');
        if($request->isPost()){
            $validation = new Validation();
            //pour valider les données par rapport aux règles,
            // Nous pouvons changer le message d'erreur par défaut pour une règle ou pour une valeur d'une règle, exemple :  email.required => "champs vide"
            $validation->validate($request->getBody(),[
                "password" => "required",
                "email" => ["required", "email"]
            ],
            [
                "required" => "toto"
            ]);
//            if($validation->getErrors()) return $this->render('login',['errors' => $validation->getErrors()]);
            if($validation->getErrors()) return ['errors' => $validation->getErrors()];
        }
        return $this->render('login');
    }
    public function register()
    {

        $this->setLayout('auth');
        return $this->render('register');
    }
}