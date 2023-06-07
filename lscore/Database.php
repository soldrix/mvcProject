<?php

namespace App\lscore;

use PDO;

class Database
{
    public \PDO $pdo;
    public static string $db_name;



    public function __construct(array $config)
    {
        $name = $config['DB_NAME'];
        $host = $config['DB_HOST'];
        self::$db_name = $name;
        $port = $config['DB_PORT'];
        $user = $config['DB_USER'];
        $password = $config['DB_PASSWORD'];
        $this->pdo = new PDO("mysql:host=$host:$port;dbname=$name", $user, $password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    }

    public function applyMigrations()
    {




        $this->createMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations();
        $oldMigrations = [];
        if(count($appliedMigrations) > 0){
            $oldMigrations = $appliedMigrations;
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
//            if(in_array($migration, $oldMigrations)){
//                $this->log("Deleting migration $migration");
//                $instance->down();
//                $this->log("Deleted migration $migration");
//            }

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


    /**
     * @param string $name
     * name of table
     * @param array $array
     * value of column
     * example : ["id" => ["INT","AI", "PRIMARY"]]
    */
    public function table(string $name,array $array)
    {
        $tempArray = [];
        $columnName = [];
        $nullable = false;
        foreach ($array as $column => $options){
            array_push($columnName, $column);
            $tempArray[$column] = [];
            foreach ($options as $key => $data){
                if(strtoupper($key) === "DEFAULT"){
                    if(strtoupper($data) === "CURRENT_TIMESTAMP"){
                        array_push($tempArray[$column], strtoupper($key." ".$data));
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
        $separation = [];




        foreach ($columnName as $item){
            if(!in_array("DEFAULT",$tempArray[$item])){
                //not default
                array_push($tempArray[$item], ($nullable === false) ? ' NOT NULL' : ' NULL');
            }
            array_push($separation,$item." ".implode(' ', $tempArray[$item]));
        }
        $statement = $this->pdo->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = N'Users' AND TABLE_SCHEMA = 'mvcPHP';");
        $statement->execute();
        $statement = $statement->fetchAll(\PDO::FETCH_COLUMN);
        if(count($statement) > 0){
            // get what to add
            $newValue =  array_diff($columnName,$statement);

//            // get what to remove
            $delCol =  array_diff($statement,$columnName);

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
}