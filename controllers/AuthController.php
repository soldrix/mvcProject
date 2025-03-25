<?php

namespace App\controllers;


use App\lscore\Application;
use App\lscore\Request;
use App\lscore\Validation;

class AuthController extends Controller
{
    public function redirectLogin()
    {
        $this->setLayout('auth');
        return $this->render('login');
    }
    public function login()
    {
        if($this->isAuth()){
            $this->redirect('/home');
            exit("Already authenticated.");
        }
        //pour changer le layout exemple auth pour le layout auth.php
        $this->setLayout('auth');
        return $this->render('login');
    }
    public function sendFormLogin(Request $request)
    {
        //pour valider les données par rapport aux règles,
        // Nous pouvons changer le message d'erreur par défaut pour une règle ou pour une valeur d'une règle, exemple :  email.required => "champs vide"
        $validation = Validation::validate($request->getBody(),[
            "password" => ["required","password_confirmation"],
            "email" => ["required", "email"]
        ]);
        if($validation->getErrors()) return $this->render('login',['errors' => $validation->getErrors(), 'data' => $request->getBody()]);
        $token = "sdfgsdfgsdg";
        Application::$app->login($token);
        $this->redirect('/home');
        die();
    }
    public function registerForm()
    {
        if($this->isAuth()){
            $this->redirect('/home');
            exit("Already authenticated.");
        }
        //pour changer le layout exemple auth pour le layout auth.php
        $this->setLayout('auth');
        return $this->render('register');
    }
    public function forgot_password(Request $request)
    {
        if($this->isAuth()){
            $this->redirect('/home');
            exit("Already authenticated.");
        }
//        if($this->getAuth()){
//            $this->redirect('/home');
//            exit("Already authenticated.");
//        }
        $this->setLayout('auth');
//        if($request->isPost()){
//            $validation = new Validation();
//            $validation->validate($request->getBody(),[
//                "email" => ["required", "email"]
//            ]);
//            if($validation->getErrors()) return $this->render('forgot_password',['errors' => $validation->getErrors(), 'data' => $request->getBody()]);
//            //todo apres verification ...
//        }
        return $this->render('register');
    }

    public function logout():void
    {
        Application::$app->logout();
        $this->redirect('/login');
        die();
    }
}