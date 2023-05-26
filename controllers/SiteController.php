<?php

namespace App\controllers;

use App\lscore\Request;

class SiteController extends Controller
{
    public function redirectHome():void
    {
        $this->redirect('/home');
    }
    public  function index()
    {
        return $this->render('home');
    }
    public  function contacts()
    {
        return $this->render('contacts');
    }

    public  function HandleContact(Request $request)
    {
        $body = $request->getBody();
        return $body;
    }
    public function profil()
    {
        return $this->render('profil');
    }
    public function actualites()
    {
        return $this->render('actualites');
    }
    public function infos_administratives()
    {
        return $this->render('infos_administratives');
    }
    public function ventes_traductions()
    {
        return $this->render('ventes_traductions');
    }
    public function catalogues_sites()
    {
        return $this->render('catalogues_sites');
    }
    public function FAQ()
    {
        return $this->render('FAQ');
    }
    public function test(){
        return $this->render('contact');
    }
    public function testApi()
    {
        return [
          "email" => "jean@gmail.com"
        ];
    }
}