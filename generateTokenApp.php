<?php
$filename = ".env";
function generateToken(int $length = 39)
{
    $length = ($length < 39) ? 39 : $length;
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+/';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}
$tokenApp =  generateToken(255);
$contents = file_get_contents($filename);
$searchContents = explode("\n", $contents);
$searchContents = array_filter($searchContents);
foreach ($searchContents as $key => $value){
    $oldVal = $value;
    $value  = str_replace(" ", "", $value);
    $value = explode("=", $value);
    if (str_contains("TOKEN_APP", $value[0])){
        $newValue  = $value[0] . " = ". $tokenApp."\n";
        $contents = str_replace($oldVal, $newValue, $contents);
        file_put_contents($filename, $contents);
        echo "TOKEN_APP generated !";
    }
}