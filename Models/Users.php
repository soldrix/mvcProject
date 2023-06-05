<?php
namespace App\Models;
use App\lscore\DBModel;

class Users extends DBModel
{
    protected $stockable  = [
        "id" => ["int","ai", "primary"],
        "first_name" => ["varchar" => 255],
        "last_name" => ["varchar" => 255],
        "email" => ["varchar" => 255],
        "password" => ["text"],
        "created_at" => ["timestamp", "default" => "current_timestamp"]
    ];
    public string $table = "users";
}