<?php

namespace App\lscore;

class Validation
{
    private array $errorsBags = [];
    /**
     * Pour valider les valeurs par rapport aux règles du tableau rules
     */
    public static function validate(array|object $data,array $rules, array $errorsMessage = [])
    {
        $validation = new Validation();
        $validation->errorsBags = [];
        if(gettype($data) === "object"){
            //pour changer le format
            $data = json_decode(json_encode($data), true) ;
        }
        if(gettype($data) === "array"){
            //Remplace la valeur de data par null si elle est vide
            $data = (count($data) > 0 ) ? $data : null;
        }
        //Remplace la valeur de data par la différence entre le tableau data et rules ou par null si data est null
        $data = ($data !== null) ? array_diff_key($data,array_diff_key($data, $rules)) : null;

        if (isset($data)){
            foreach ($data as $key => $value)
            {
                if(isset($rules[$key])){
                    foreach ((!is_array($rules[$key])) ? [$rules[$key]] : $rules[$key] as $rule)
                    {
                        $validation->rules($value, $key, $rule, $errorsMessage);
                    }
                }
            }
        }else{
            //Pour retourner les erreurs par rapport au tableau rules
            foreach ($rules as $key => $values)
            {
                $values = (gettype($values) !== "array") ? [$values] : $values;
                foreach ($values as $value)
                {
                    $validation->rules("",$key,$value,$errorsMessage);
                }
            }
        }
        return $validation;
    }
    /**
     * Pour récupérer les erreurs
    */
    public function getErrors(): bool|array
    {
        return (count($this->errorsBags) <= 0) ? false : $this->errorsBags;
    }
    /**
    *Pour vérifier les valeurs
    */
    private function rules($data, $field, $key, array $customMessage)
    {
        //regex email
        $email = "/^(([a-zA-Z\d]+)([\-_.][a-zA-Z\d])*)+@([a-zA-Z\d]?[\-_.]?[a-zA-Z\d]){2,253}[.]([a-zA-Z]{2,})$/";
        //test par type
        $rules = [
            "required" => ($data === null || $data === ''),
            "email" => (preg_match($email, $data, $matches, PREG_OFFSET_CAPTURE, 0) === 0),
            "model"
        ];
        //pour changer le message d'erreur par défaut par le message personnalisé.
        if(count($customMessage) > 0){
            foreach ($customMessage as $msg => $value){
                if(str_contains($msg, ".")){
                    $test = explode('.',$msg);
                    if($field === $test[0] && $key === $test[1]){
                        $message = $value;
                    }
                }else if ($msg === $key){
                    $message = $value;
                }
            }
        }
        //pour vérifier les valeurs par rapport au type (required)
        if($rules[$key]){
            //pour ajouter une erreur avec un message au tableau contenant toutes les erreurs.
            $this->errorsBags[$field][] = (isset($message)) ? $message : $this->errorsMessage()[$key];
        }
    }
    /**
    *Pour retourner les messages d'erreurs par défaut
    */
    private function errorsMessage()
    {
        return [
            "required" => "Ce champs est requis",
            "email" => "l'email doit être valide"
        ];
    }
}
