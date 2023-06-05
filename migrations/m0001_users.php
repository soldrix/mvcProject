<?php

class m0001_users
{
    public function up()
    {
        $db = \App\lscore\Application::$app->database;
        $db->table("users",[
            "id" => ["int","ai", "primary"],
            "first_name" => ["varchar" => 255],
            "last_name" => ["varchar" => 255],
            "email" => ["varchar" => 255],
            "password" => ["text"],
            "created_at" => ["timestamp", "default" => "current_timestamp"]
        ]);
    }
    public function down()
    {
        $db = \App\lscore\Application::$app->database;
        $db->pdo->exec("DROP TABLE users;");
    }
}