<?php

namespace App\lscore;

use PDO;

class Database
{
    public \PDO $pdo;
    public static string $db_name;



    public function __construct()
    {
        $name = $_ENV['DB_NAME'] ?? "";
        $host = $_ENV['DB_HOST'] ?? "";
        self::$db_name = $name;
        $port = $_ENV['DB_PORT'] ?? "";
        $user = $_ENV['DB_USER'] ?? "";
        $password = $_ENV['DB_PASSWORD'] ?? "";
        $this->pdo = new PDO("mysql:host=$host:$port;dbname=$name", $user, $password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    }

    public function applyMigrations()
    {
        $this->createMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations();
        if(count($appliedMigrations) > 0){
            $this->deleteMigrations();
            $appliedMigrations = [];
            $this->log("All migrations are deleted");
        }
        $files = scandir(Application::$ROUTE_DIR.'/Models');
        $toApllyMigrations = array_diff($files, $appliedMigrations);
        foreach ($toApllyMigrations as $migration)
        {
            if($migration === "." || $migration === ".."){
                continue;
            }
            $classname = "\App\\Models\\".pathinfo($migration, PATHINFO_FILENAME);
            $instance = new $classname();
            $this->log("Deleting Foreign Key $migration");
            $instance->removeForeignKey();
            $this->log("Deleted Foreign Key $migration");
            $this->log("Applying migration $migration");
            $instance->up();
            $this->log("Applied migration $migration");

            $appliedMigrations[] = $migration;
        }

        if(!empty($appliedMigrations)){
            $this->saveMigrations($appliedMigrations);
        }else{
            $this->log("All migrations are applied");
        }
        foreach ($toApllyMigrations as $migration)
        {
            if($migration === "." || $migration === ".."){
                continue;
            }
            $classname = "\App\\Models\\".pathinfo($migration, PATHINFO_FILENAME);
            $instance = new $classname();
            if($instance->verifyForeignKeyArray()){
                $this->log("Applying Foreign Key $migration");
                $instance->addForeignKey();
                $this->log("Applied Foreign Key $migration");
            }
        }
    }

    public function createMigrationsTable(): void
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255),
                create_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=INNODB;");
    }

    public function getAppliedMigrations(): bool|array
    {
       $statement =  $this->pdo->prepare("SELECT migration FROM migrations");
       $statement->execute();
       return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }
    public function deleteMigrations(): void
    {
        $statement = $this->pdo->prepare("DELETE FROM migrations;");
        $statement->execute();
    }
    public function saveMigrations(array $migrations): void
    {

        $str = implode(",", array_map(fn($m) => "('$m')", $migrations));

        $statement = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES
                $str
        ");
        $statement->execute();
    }
    protected function log($message): void
    {
        echo "[" . date("Y-m-d H:i:s") . "] - " . $message.PHP_EOL;
    }


    /**
     * @param string $name
     * name of table
     * @param array $array
     * value of column
     * example : ["id" => ["INT","AI", "PRIMARY"]]
    */
    public function table(string $name,array $array): void
    {
        $tempArray = [];
        $columnName = [];
        $separation = [];
        foreach ($array as $column => $options){
            $nullable = false;
            $columnName[] = $column;
            $tempArray[$column] = [];
            foreach ($options as $key => $data){
                if(strtoupper($key) === "DEFAULT"){
                    if(strtoupper($data) === "CURRENT_TIMESTAMP"){
                        $tempArray[$column][] = strtoupper($key . " " . $data);
                    }else{
                        if (!str_contains($data, "'")){
                            $data = "'" .$data."'";
                        }
                        $tempArray[$column][] = strtoupper($key) . ' ' . $data ;
                    }
                }else{
                    $data = strtoupper($data);
                    if (strtoupper($key) === "VARCHAR" || strtoupper($key) === "CHAR"){
                        $tempArray[$column][] = $key . "(" . $data . ")";
                    }elseif($data === "PRIMARY"){
                        $tempArray[$column][] = "PRIMARY KEY";
                    }elseif($data === "AI"){
                        $tempArray[$column][] = "AUTO_INCREMENT";
                    }elseif($data === "ID"){
                        $tempArray[$column][] = "INT";
                        $tempArray[$column][] = "PRIMARY KEY";
                    }elseif($data === "NULL"){
                        $nullable = true;
                    }else{
                        $tempArray[$column][] = $data;
                    }
                }

            }
            $default = false;
            foreach ($tempArray[$column] as $value){
                if(str_contains($value, "DEFAULT")){
                    $default = true;
                    break;
                }
            }
            if(!$default){
                //not default
                $tempArray[$column][] = ($nullable === false) ? ' NOT NULL' : ' NULL';
            }
            $separation[] = $column . " " . implode(' ', $tempArray[$column]);
        }
//EXTRA,COLUMN_KEY
        $dbname = Database::$db_name;
        $statement = $this->pdo->prepare("SELECT COLUMN_NAME,COLUMN_TYPE,IS_NULLABLE,COLUMN_DEFAULT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = N'$name' AND TABLE_SCHEMA = '$dbname';");
        $statement->execute();
        $statement = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $arrayStatement = [];
        if (count($statement) > 0){
            foreach ($statement as $data){
                foreach($data as $key => $column){
                    if($key === "COLUMN_NAME"){
                        $arrayType = [];
                        if (str_contains(strtoupper($data["COLUMN_TYPE"]), "INT")){
                            $intType = explode('(',$data["COLUMN_TYPE"]);
                            $arrayType[] = strtoupper($intType[0]);
                        }else{
                            $arrayType[] = strtoupper($data["COLUMN_TYPE"]);
                        }
                        $arrayType[] = (strtoupper($data["IS_NULLABLE"]) === "YES") ? " NULL" : " NOT NULL";
                        $diffType = array_diff($arrayType,array_map('strtoupper', $tempArray[$column] ?? []));
                        if (isset($data["COLUMN_DEFAULT"])){
                            $defaultVal[] = "DEFAULT ".$data["COLUMN_DEFAULT"];
                            $diffDefault = array_diff($defaultVal,array_map('strtoupper', $tempArray[$column] ?? []));
                        }
                        if(count($diffType) > 0 || isset($diffDefault)){
                            $this->alterTable($name, "DROP COLUMN" ,$column);
                        }else{
                            $arrayStatement[] = $column;
                        }

                    }
                }
            }
        }
        if(count($arrayStatement) > 0){
            // get what to add
            $newValue =  array_diff($columnName,$arrayStatement);
            // get what to remove
            $delCol =  array_diff($arrayStatement,$columnName);

            if(count($newValue) > 0){
                foreach ($separation as $modelValue){
                    foreach ($newValue as $filterData){
                        if(str_contains($modelValue, $filterData)){
                            $sql = "ALTER TABLE $name ADD $modelValue;";
                            $this->pdo->exec($sql);
                        }
                    }
                }
            }
            if(count($delCol) > 0){
                foreach ($delCol as $col)
                {
                    $sql = "ALTER TABLE $name DROP COLUMN $col;";
                    $this->pdo->exec($sql);
                }
            }
        }else{
            $sql = "CREATE TABLE IF NOT EXISTS $name (
                        ".implode(",",$separation)."
                   ) ENGINE=INNODB;";
            $this->pdo->exec($sql);
        }
    }
    private function alterTable(string $table, string $type, string $value){
        $sql = "ALTER TABLE $table $type $value;";
        $this->pdo->exec($sql);
    }
}
