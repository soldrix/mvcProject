<?php
namespace App\Models;
use App\lscore\DBModel;

class Users extends DBModel
{
    protected  $stockable  = [
        "id" => ["int","ai", "primary"],
        "first_name" => ["varchar" => 255],
        "last_name" => ["varchar" => 255],
        "email" => ["varchar" => 255],
        "password" => ["text"],
        "created_at" => ["timestamp", "default" => "current_timestamp"]
    ];
    protected  string $table = "users";
    //example of foreign key :
//    protected $foreignKey = [
//        "id_user" => ["REFERENCES" => "users", "on" => "id", "delete" => "cascade", "update" => "cascade"]
//    ];
}