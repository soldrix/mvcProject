<?php

namespace App\Models;

use App\lscore\DBModel;

class TokenAuth extends DBModel
{
    protected  $stockable  = [
        "id" => ["int","ai", "primary"],
        "token" => ["text"],
        "id_user" => ["int"],
        "created_at" => ["timestamp", "default" => "current_timestamp"],
        "expired_at" => ["timestamp", "null"]
    ];
    protected  string $table = "tokenAuth";
    protected $foreignKey = [
        "id_user" => ["REFERENCES" => "users", "on" => "id", "delete" => "cascade", "update" => "cascade"]
    ];
}