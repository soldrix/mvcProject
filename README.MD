<!-- POUR COMMENCER -->
#Pour commencer

1. Ajouter le fichier .npmrc
   - Ce fichier doit contenir les informations suivantes : 
   - Ajouter le token du compte avec fontawesome pro.
   - exemple : (  //npm.fontawesome.com/:_authToken=11111AZE-A11A-1111-AZ11-AZ1AZE1A11A1 )
   ```text
      @fortawesome:registry=https://npm.fontawesome.com/
      //npm.fontawesome.com/:_authToken=  token 
   ``` 
2. Installation NPM packages
   ```sh
   npm install
   ```
3. Installation composer packages
   ```sh
   composer install
   ```
4. Installation des fichiers css, less et js
   ```sh
   gulp build
   ```
<!-- Commandes et installation de GULP -->   
#Commandes et installation de [gulp](https://gulpjs.com/docs/en/getting-started/quick-start)

1. Installation de gulp
   ```sh
   npm install --global gulp-cli   
   ```
2. Installation des packages gulp dans le devDependencies
   ```sh
   npm install --save-dev gulp
   ```
4. Pour installer les fichiers css, less et js
   ```sh
   gulp build
   ```
5. Pour mettre à jours les fichiers css et js automatiquement
   ```sh
   gulp watch    
   ```
6. Pour nettoyer le dossier public des fichiers css et js
   ```sh
   gulp clean
   ```



<!-- FONCTION VALIDATE -->
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
<!-- REQUEST -->
#Request

##Fonction getBody
La fonction getBody sert à récupérer les données des requêtes POST

Exemple dans une fonction d'un controller : 
```php
public function exemple(\App\lscore\Request $request)
{
  $datas = $request->getBody();
  //datas retourne par exemple :
  [
      "email" => "exemple@gmail.com",
      "password" => "azerty"
  ];
  $email = $datas->email;
  //ou 
  $email = $request->getBody()->email;

  
}
```
<!-- Routes -->
#Routes

###Les routes peuvent être ajoutées au fichier web.php ou api.php dans le répertoire routes.

##Routes GET

```php
$app->router->get('/login', [\App\controllers\AuthController::class, 'login'], true);
```

##Routes POST

```php
$app->router->POST('/login', [\App\controllers\AuthController::class, 'login'], true);
```
##Option des routes pour GET et POST

###L'option path est l'URL de la route
###L'option callback peut contenir deux différentes valeurs 
Exemple : 

Pour relier une fonction d'un controller à une route (exemple avec la fonction login provenant du controller AuthControllers).

```php
$app->router->get('/login', [\App\controllers\AuthController::class, 'login'], true);
```
Ou pour afficher une page (login pour login.php qui est situé dans le répertoire views)
```php
$app->router->get('/login', "login", true);
```
###L'option notAuth qui peut prendre deux différentes valeurs
Exemple :
La valeur true signifie que la route n'a pas besoin de connexion pour être accédé.

```php
$app->router->get('/login', [\App\controllers\AuthController::class, 'login'], true);
```
Ou la valeur false qui est la valeur par défaut pour obliger une connexion avant de pouvoir y accéder.

```php
$app->router->get('/login', [\App\controllers\AuthController::class, 'login'], false);
$app->router->get('/login', [\App\controllers\AuthController::class, 'login']);
```

<!-- Mises en page -->
#Mises en page

##Les mises en page peuvent être ajoutées dans le dossier views/layouts.

Pour ajouter le contenu d'une page dans le layout, il faut ajouter : 

```text
{{content}}
```
Exemple :

Le text ( {{content}} ) sera remplacé par le contenu d'une page.
```html
<div class="container-fluid d-flex justify-content-end p-0 vh-100">
     {{content}}
 </div>
```
<!-- Exception -->
#Exception

Les exceptions peuvent être ajoutées dans le répertoire lscore/exception.

Exemple d'exception :
```php

namespace App\lscore\exception;

class NotFoundException extends \Exception
{
    protected $message = "Page not found.";
    protected $code = 404;
}
```

