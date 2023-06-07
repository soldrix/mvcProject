<?php

namespace App\controllers;


use App\lscore\Application;
use App\lscore\Request;
use App\lscore\Validation;
use App\Models\Users;

class AuthControllers extends Controller
{
    public function redirectLogin()
    {
        $this->setLayout('auth');
        return $this->render('login');
    }
    public function login()
    {
        if($this->getAuth()){
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
        $validation = Validation::validate($request->only(["password","email"]),[
            "password" => "required",
            "email" => ["required", "email"]
        ]);
        if($validation->getErrors()) return $this->render('login',['errors' => $validation->getErrors(), 'data' => $request->getBody()]);
        Application::$app->login();
        $this->redirect('/home');
        die();
    }
    public function registerForm()
    {
        if($this->getAuth()){
            $this->redirect('/home');
            exit("Already authenticated.");
        }
        //pour changer le layout exemple auth pour le layout auth.php
        $this->setLayout('auth');
        return $this->render('register');
    }
    public function register(Request $request)
    {
        $validation = Validation::validate($request->getBody(),[
            "password" => "required",
            "email" => ["required", "email"],
            "first_name" => "required",
            "last_name" => "required"
        ]);
        if($validation->getErrors()) return $this->render('register',['errors' => $validation->getErrors(), 'data' => $request->getBody()]);
        $datas = $request->getBody();
        $datas->password = password_hash($datas->password, PASSWORD_DEFAULT);
        USers::Create($datas);
        return $this->render('register', ["message" => "utilisateur créer avec succès !"]);
    }
    public function forgot_password(Request $request)
    {
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

    public function getUsers(){
      return Users::get();
    }
    public function deleteUser(Request $request)
    {
        Users::delete($request->id);
    }

    public function findUser(){
        return Users::find([
            "first_name" => "toto"
        ]);
    }
    public function userUpdate()
    {
        Users::update([
            "first_name" => "jeanMark",
            "last_name" => "aze"
        ],
        [
            "id" => 5
        ]);
    }
    public function testJoin()
    {
        $users = new Users();
        return $users->join("left", "voitures", "id", "id_users", "=", [
            "users.first_name as toto",
            "voitures.* as voiture"
        ]);
    }
    public function logout():void
    {
        Application::$app->logout();
        $this->redirect('/login');
        die();
    }
}