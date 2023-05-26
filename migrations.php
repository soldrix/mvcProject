<?php

require_once __DIR__ . '/vendor/autoload.php';
use App\lscore\Application;

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$config = [
    "db" => [
        "DB_HOST" => $_ENV["DB_HOST"] ?? '',
        "DB_PORT" => $_ENV["DB_PORT"] ?? '',
        "DB_USER" => $_ENV["DB_USER"] ?? '',
        "DB_PASSWORD" => $_ENV["DB_PASSWORD"] ?? '',
        "DB_NAME" => $_ENV["DB_NAME"] ?? ''
    ]
];
$app = new Application(__DIR__,$config);

$app->database->applyMigrations();