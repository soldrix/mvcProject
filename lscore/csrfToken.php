<?php

namespace App\lscore;

class csrfToken
{
    public function generateToken(int $length = 39)
    {
        $length = ($length < 39) ? 39 : $length;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    /**
     * @return string
     */
    public function getToken()
    {
       return Application::$app->session->get('CSRF_token');
    }
    public function resetToken():void{
        Application::$app->session->remove('CSRF_token');
    }
    public function loadToken(){
        $token = $this->getToken();
        return "<input type='HIDDEN' name='csrf-token' value='$token'/>";
    }

}