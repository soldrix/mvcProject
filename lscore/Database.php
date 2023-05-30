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
        trigger_error(json_encode(count($this->getAppliedMigrations())));
        if(count($this->getAppliedMigrations()) > 0){
            $this->deleteMigrations();
            $this->log("All migrations are deleted");
        }
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
            if(isset($appliedMigrations[$migration])){
                $this->log("Deleting migration $migration");
                $instance->down();
                $this->log("Deleted migration $migration");
            }

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
    public function testCreateTable(string $name,array $array)
    {
        $tempArray = [];
        $columnName = [];
        $nullable = false;
        foreach ($array as $column => $options){
            array_push($columnName, $column);
            foreach ($options as $key => $data){
                if(strtoupper($key) === "DEFAULT"){
                    if(strtoupper($data) === "CURRENT_TIMESTAMP"){
                         array_push($tempArray[$column],$data);
                    }else{
                        array_push($tempArray[$column],"'" .str_replace("'","\'",str_replace('"','\"',$data))."'");
                    }
                }else{
                    $data = strtoupper($data);
                    if (strtoupper($key) === "VARCHAR" || strtoupper($key) === "CHAR"){
                        array_push($tempArray[$column],$key."(".$data.")");
                    }elseif($data === "PRIMARY"){
                        array_push($tempArray[$column],"PRIMARY KEY");
                    }elseif($data === "AI"){
                        array_push($tempArray[$column],"AUTO_INCREMENT");
                    }elseif($data === "ID"){
                       array_push($tempArray[$column],"INT PRIMARY KEY");
                    }elseif($data === "NULL"){
                        $nullable = true;
                    }else{
                        array_push($tempArray[$column], $data);
                    }
                }

            }
        }
        trigger_error(json_encode($tempArray));
        array_push($tempArray,($nullable === false) ? ' NOT NULL,' : ' NULL,');
        $separation = implode(' ', $tempArray);
        trigger_error(json_encode($separation));
        $sql = "CREATE TABLE $name (
                        ".implode(" ".$separation,$columnName)."
                   ) ENGINE=INNODB;";
        trigger_error($sql);
    }
}