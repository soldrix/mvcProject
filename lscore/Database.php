<?php

namespace App\lscore;

use PDO;

class Database
{
    public \PDO $pdo;

    public function __construct(array $config)
    {
        $name = $config['DB_NAME'];
        $host = $config['DB_HOST'];
        $port = $config['DB_PORT'];
        $user = $config['DB_USER'];
        $password = $config['DB_PASSWORD'];
        $this->pdo = new PDO("mysql:host=$host:$port;dbname=$name", $user, $password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    }

    public function applyMigrations()
    {
        $this->createMigrationsTable();
        $this->deleteMigrations();
        $this->log("All migrations are deleted");
        $appliedMigrations = $this->getAppliedMigrations();
        $newMigrations = [];
        $files = scandir(Application::$ROUTE_DIR.'/migrations');
        $toApllyMigrations = array_diff($files, $appliedMigrations);
        foreach ($toApllyMigrations as $migration)
        {
            if($migration === "." || $migration === ".."){
                continue;
            }
            require_once Application::$ROUTE_DIR.'/migrations/'.$migration;
            $classname = pathinfo($migration, PATHINFO_FILENAME);
            $instance = new $classname();
                $this->log("Deleting migration $migration");
                $instance->down();
                $this->log("Deleted migration $migration");
                $this->log("Applying migration $migration");
                $instance->up();
                $this->log("Applied migration $migration");

            $newMigrations[] = $migration;
        }

        if(!empty($newMigrations)){
            $this->saveMigrations($newMigrations);
        }else{
            $this->log("All migrations are applied");
        }

    }

    public function createMigrationsTable()
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255),
                create_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=INNODB;");
    }

    public function getAppliedMigrations()
    {
       $statement =  $this->pdo->prepare("SELECT migration FROM migrations");
       $statement->execute();
       return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }
    public function deleteMigrations(){
        $statement = $this->pdo->prepare("DELETE FROM migrations;");
        $statement->execute();
    }
    public function saveMigrations(array $migrations)
    {

        $str = implode(",", array_map(fn($m) => "('$m')", $migrations));

        $statement = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES
                $str
        ");
        $statement->execute();
    }
    protected function log($message)
    {
        echo "[" . date("Y-m-d H:i:s") . "] - " . $message.PHP_EOL;
    }

}