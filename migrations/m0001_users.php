<?php

class m0001_users
{
    public function up()
    {
        $db = \App\lscore\Application::$app->database;
       $sql = "CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            first_name VARCHAR(255) NOT NULL,
            last_name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            password TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
       ) ENGINE=INNODB;";
       $db->pdo->exec($sql);
    }
    public function down()
    {
        $db = \App\lscore\Application::$app->database;
        $sql = "DROP TABLE users;";
        $db->pdo->exec($sql);
    }
}