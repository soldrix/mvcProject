<?php
namespace App\Models;
use App\lscore\DBModel;

class Users extends DBModel
{
    protected $stockable  = [
        "first_name",
        "last_name",
        "email",
        "password"
    ];
    public string $table = "users";
}