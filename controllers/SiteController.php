<?php

namespace App\controllers;

use App\lscore\Application;
use App\lscore\Request;

class SiteController extends Controller
{
    public  function index()
    {
        $params = [
            "name" => "jean"
        ];
        return $this->render('home',$params);
    }
    public  function contact()
    {
        return $this->render('contact');
    }

    public  function HandleContact(Request $request)
    {
        $body = $request->getBody();
        echo $body->subject;
    }
}