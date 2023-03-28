#Fonction de validation
Cette fonction permet de verifier des données par rapport à des règles pour ensuite stocker les messages d'erreur.
###Le validate contiens 3 params qui sont : <br>
1. **data** 
    - Doit contenir toutes les valeurs sous forme de tableau ou d'objet
2. **rules**
    - Doit contenir un tableau comme dans l'exemple ci-dessous.
3. **errorsMessage**
    - Peut contenir un tableau avec les messages d'erreur personnaliser comme dans l'exemple ci-dessous

###Exemple :    
```php
    $validation = new Validation();
    //pour valider les données par rapport aux règles,
    $validation->validate($request->getBody(),[
        "password" => "required",
        "email" => ["required", "email"]
    ],
    [
        //pour changer le message d'erreur pour une règle  
        "required" => "message",
        //pour changer le message d'erreur d'une règle par rapport a un champ
        "email.required" => "autre message"
    ]);
```
###Pour récupérer les messages d'erreur :

La fonction getErrors permet de récupérer les erreurs sous forme de tableau associatif
```php
$errors = $validation->getErrors()
```
Pour récupérer une erreur du tableau : 
```php
$errors['email']
```
