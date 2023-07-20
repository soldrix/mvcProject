<?php

namespace App\controllers\API;

use App\controllers\Controller;
use App\controllers\TokenAuthController;
use App\lscore\Application;
use App\lscore\Request;
use App\lscore\Validation;
use App\Models\Users;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validation = Validation::validate($request->only(["email", "password"]),[
            "email" => ["required", "email"],
            "password" => "required"
        ]);
        if ($validation->getErrors())return $this->response(401,$validation->getErrors());
        $user = Users::find(["email" => $request->email]);
        if (password_verify($request->password, $user->password) && $request->email === $user->email){
            $token = TokenAuthController::createToken($user->id);
            return $this->response(200, ["tokenAuth" => $token]);
        }else{
            $validation->addErrors(["*" => "You have entered an invalid username or password."]);
            return $this->response(401,$validation->getErrors());
        }
    }
    public function register(Request $request)
    {
        $validation = Validation::validate($request->only(["email", "password"]),[
            "email" => ["required", "email"],
            "password" => "required"
        ]);
        if ($validation->getErrors())return $this->response(400,$validation->getErrors());
        $datas = $request->only(['password', 'email', 'first_name', 'last_name']);
        $datas->password = password_hash($datas->password, PASSWORD_DEFAULT);
        Users::Create($datas);
        return $this->response(200,['message' => "User created successfully."]);
    }
    public function testAuth()
    {
        return $this->response(200, ["message" => "auth working"]);
    }
}