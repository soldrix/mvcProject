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
        if (is_object($data)) {
            $data = get_object_vars($data);
        }
        $data = (!is_array($data) || empty($data))
            ? array_map(fn($value) => "", $rules)
            : array_intersect_key($data, $rules);

        foreach ($rules as $key => $ruleSet) {
            $ruleSet = is_array($ruleSet) ? $ruleSet : [$ruleSet];
            foreach ($ruleSet as $rule) {
                self::rules($data, $key, $rule, $errorsMessage);
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
     * @param $rule_name string key de rules
     * Example : "required"
     * @param array $customMessage  Message personnaliser pour les erreurs
     */
    private static function rules(array|string $data, string $field, string $rule_name, array $customMessage = [])
    {
        // Validation simplifiée de l'email
        $isEmailInvalid = isset($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL);
        // Vérification de la confirmation du mot de passe
        $password_confirmation = $data[$field."_confirmation"] ?? "";
        $isPasswordMismatch = isset($data[$field]) && $data[$field] !== $password_confirmation;
        // Règles de validation
        $rules = [
            "required" => empty($data[$field]),
            "email" => $isEmailInvalid,
            "password_confirmation" => $isPasswordMismatch
        ];
        // Vérification si la règle est violée
        if (!$rules[$rule_name]) {
            return;
        }
        // Définition du message d'erreur personnalisé ou par défaut
        $message = $customMessage["$field.$rule_name"]
            ?? $customMessage[$rule_name]
            ?? self::errorsMessage()[$rule_name];
        // Ajout de l'erreur dans le tableau des erreurs
        self::$errorsBags[$field][] = $message;
        if ($rule_name === "password_confirmation") {
            self::$errorsBags[$field."_confirmation"][] = $message;
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
