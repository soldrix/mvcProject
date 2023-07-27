<?php

namespace App\lscore;

class Validation
{
    protected  static array $errorsBags = [];
    private static array $rulesKey = [];
    /**
     * Pour valider les valeurs par rapport aux règles du tableau rules
     * @param $data Object|array
     * Exemple : ["email" => "email@gmail.com"]
     * @param $rules array
     * Exemple : ["email" => ["required","email"]
     * @param array $errorsMessage change errors message.
     * Exemple : ["email" => ["email.required" => "email requis"]
     */
    public static function validate(object|array $data, array $rules, array $errorsMessage = [])
    {
        self::$errorsBags = [];
        self::$rulesKey = array_keys($rules);
        if(gettype($data) == "object"){
            //pour changer le format
            $data = json_decode(json_encode($data), true) ;
        }
        //Remplace la valeur de data par null si elle est vide
        $data = ((gettype($data) !== "array" ) ? isset($data) : count($data) > 0) ? $data : null;
        //Remplace la valeur de data par la différence entre le tableau data et rules ou par null si data est null
        $data = (gettype($data) != 'string' && $data !== null) ? array_diff_key($data,array_diff_key($data, $rules)) : null;
        if (isset($data)){
            foreach ($data as $key => $value)
            {
                if(isset($rules[$key])){
                    foreach ((!is_array($rules[$key])) ? [$rules[$key]] : $rules[$key] as $rule)
                    {
                        if ($rule !== ""){
                            self::rules($data, $key, $rule, $errorsMessage);
                        }
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
                    self::rules("",$key,$value,$errorsMessage);
                }
            }
        }
        return new Validation();
    }
    /**
     * Pour récupérer les erreurs
     */
    public function getErrors(): bool|array
    {
        return (count(self::$errorsBags) <= 0) ? false : self::$errorsBags;
    }
    /**
     *Pour vérifier les valeurs
     * @param $data array|string valeurs a verifier
     * @param $field string Champs à verifier dans data
     * @param $key string key de rules
     * Example : "required"
     * @param array $customMessage  Message personnaliser pour les erreurs
     */
    private static function rules(array|string $data, string $field, string $key, array $customMessage = [])
    {
        //regex email
        $email = '/(?:(?:\r\n)?[ \t])*(?:(?:(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|"(?:[^\"\r\\\\]|\\\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|"(?:[^\"\r\\\\]|\\\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*))*@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|\[([^\[\]\r\\\\]|\\\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|\[([^\[\]\r\\\\]|\\\\.)*\](?:(?:\r\n)?[ \t])*))*|(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|"(?:[^\"\r\\\\]|\\\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)*\<(?:(?:\r\n)?[ \t])*(?:@(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|\[([^\[\]\r\\\\]|\\\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|\[([^\[\]\r\\\\]|\\\\.)*\](?:(?:\r\n)?[ \t])*))*(?:,@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|\[([^\[\]\r\\\\]|\\\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|\[([^\[\]\r\\\\]|\\\\.)*\](?:(?:\r\n)?[ \t])*))*)*:(?:(?:\r\n)?[ \t])*)?(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|"(?:[^\"\r\\\\]|\\\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|"(?:[^\"\r\\\\]|\\\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*))*@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|\[([^\[\]\r\\\\]|\\\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|\[([^\[\]\r\\\\]|\\\\.)*\](?:(?:\r\n)?[ \t])*))*\>(?:(?:\r\n)?[ \t])*)|(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|"(?:[^\"\r\\\\]|\\\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)*:(?:(?:\r\n)?[ \t])*(?:(?:(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|"(?:[^\"\r\\\\]|\\\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|"(?:[^\"\r\\\\]|\\\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*))*@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|\[([^\[\]\r\\\\]|\\\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|\[([^\[\]\r\\\\]|\\\\.)*\](?:(?:\r\n)?[ \t])*))*|(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|"(?:[^\"\r\\\\]|\\\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)*\<(?:(?:\r\n)?[ \t])*(?:@(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|\[([^\[\]\r\\\\]|\\\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|\[([^\[\]\r\\\\]|\\\\.)*\](?:(?:\r\n)?[ \t])*))*(?:,@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|\[([^\[\]\r\\\\]|\\\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|\[([^\[\]\r\\\\]|\\\\.)*\](?:(?:\r\n)?[ \t])*))*)*:(?:(?:\r\n)?[ \t])*)?(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|"(?:[^\"\r\\\\]|\\\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|"(?:[^\"\r\\\\]|\\\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*))*@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|\[([^\[\]\r\\\\]|\\\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|\[([^\[\]\r\\\\]|\\\\.)*\](?:(?:\r\n)?[ \t])*))*\>(?:(?:\r\n)?[ \t])*)(?:,\s*(?:(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|"(?:[^\"\r\\\\]|\\\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|"(?:[^\"\r\\\\]|\\\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*))*@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|\[([^\[\]\r\\\\]|\\\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|\[([^\[\]\r\\\\]|\\\\.)*\](?:(?:\r\n)?[ \t])*))*|(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|"(?:[^\"\r\\\\]|\\\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)*\<(?:(?:\r\n)?[ \t])*(?:@(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|\[([^\[\]\r\\\\]|\\\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|\[([^\[\]\r\\\\]|\\\\.)*\](?:(?:\r\n)?[ \t])*))*(?:,@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|\[([^\[\]\r\\\\]|\\\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|\[([^\[\]\r\\\\]|\\\\.)*\](?:(?:\r\n)?[ \t])*))*)*:(?:(?:\r\n)?[ \t])*)?(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|"(?:[^\"\r\\\\]|\\\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|"(?:[^\"\r\\\\]|\\\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*))*@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|\[([^\[\]\r\\\\]|\\\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\\\".\[\]]))|\[([^\[\]\r\\\\]|\\\\.)*\](?:(?:\r\n)?[ \t])*))*\>(?:(?:\r\n)?[ \t])*))*)?;\s*)/';

        $password_confirmation = (isset($data[$field."_confirmation"])) ?  $data[$field."_confirmation"] : "";

        //test par type
        $rules = [
            "required" => ($data[$field] === null || $data[$field] === ''),
            "email" => (preg_match($email, $data[$field] ?? "", $matches, PREG_OFFSET_CAPTURE, 0) === 0),
            "password_confirmation" => ($data[$field] !== $password_confirmation)
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
            if ($key === "password_confirmation"){
                self::$errorsBags[$field][] = (isset($message)) ? $message : self::errorsMessage()[$key];
                self::$errorsBags[$field."_confirmation"][] = (isset($message)) ? $message : self::errorsMessage()[$key];
            }else{
                self::$errorsBags[$field][] = (isset($message)) ? $message : self::errorsMessage()[$key];
            }
        }
    }
    /**
     *Pour retourner les messages d'erreurs par défaut par rapport aux règles
     */
    private static function errorsMessage():array
    {
        return [
            "required" => "Ce champs est requis",
            "email" => "L'email doit être valide",
            "password_confirmation" => "Les mot de passe ne corresponde pas."
        ];
    }
    /**
     * @param array $fields Tableau avec champs et erreurs.
     * Exemple : ["*" => "Données invalides.","email" => "email invalides."] * pour touts les champs
     *
     */
    public function addErrors(array $fields = ["*" => "Données invalides."])
    {
        foreach ($fields as $field => $value){
            if($field === "*"){
                foreach (self::$rulesKey as $rule){
                    self::$errorsBags[$rule][] = $value;
                }
            }else{
                self::$errorsBags[$field][] = $value;
            }
        }
    }
}
