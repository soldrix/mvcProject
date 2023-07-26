<?php

namespace App\lscore;

abstract class DBModel extends Model
{
    protected  string $table;

    /**
     * Add sign to array/object value
    */
    private function addSign(array $datas): array
    {
        foreach ($datas as $key => $item){
            $datas[$key] = (count(explode(" ", $key)) < 2) ? "$key='$item'" : "$key'$item'";
        }
        return $datas;
    }
    public static function Create($datas):void
    {
       $model = new static();
       $model->attributes = [];
        $model->stock($datas);
        $table = $model->table;
        $attributes = $model->attributes;
        $params = array_map(fn($attr) => ":$attr", array_keys($attributes));
        $statement = self::prepare("INSERT INTO $table (".implode(",",array_keys($attributes)).")
    VALUES (".implode(",",$params).");");
        foreach ($attributes as $key => $attribute){
            $statement->bindParam(":$key", $model->attributes[$key]);
        }
        $statement->execute();
    }
    public static function get(array $params = ["*"], $DISTINCT = false): bool|array
    {
        $table = (new static())->table;
        $DISTINCT = ($DISTINCT === false) ? "": "DISTINCT ";
        $statement = self::prepare("SELECT $DISTINCT" . implode(',',$params) . " FROM $table;");
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
    public static function find(array $search,array $params = ["*"])
    {
        $model = new static();
        $table = $model->table;
        $search = $model->addSign($search);
        $statement = self::prepare("SELECT " . implode(',',$params) . " FROM $table WHERE "
            . implode(" AND ", $search) . ";");
        $statement->execute();
        $result =  $statement->fetchObject();
        return $result;
    }
    public static function delete($id)
    {
        $table = (new static())->table;
        $statement = self::prepare("DELETE FROM $table WHERE id = $id");
        $statement->execute();
    }
    public static function update($datas,$where)
    {
        $model = new static();
        $model->stock($datas);
        $table = $model->table;
        $user = $model->find($where,array_keys($model->attributes));
        $where = $model->addSign($where);
        if(count($user) > 0){
            $diff_value = array_diff_assoc($model->attributes,$user[0]);
            if(count($diff_value) > 0){
                $values = $model->addSign($diff_value);
                $statement = self::prepare("UPDATE $table set ".  implode(",", $values) . " WHERE "
                    . implode(" AND ", $where) . ";");
                $statement->execute();
            }
        }else{
            trigger_error("Row not found !");
        }

    }
    public static function join($type, $joinTable, $currentTableKey , $joinTableKey, $sign,array $get = ["*"], array $where = [])
    {
        //$model same as $this if function was not static
        $model  = new static();
        $table = $model->table;
        $where = (count($where) > 0) ? " WHERE ". implode(" AND ", $model->addSign($where)) . ";" : ";";
        $type = strtoupper($type);
        $condition = ($type !== "CROSS" && $type!== "NATURAL") ? " ON $table.$currentTableKey $sign $joinTable.$joinTableKey" : "";
        foreach ($get as $key =>  $data){
            //if get contains alias
            if(str_contains($data," as ")){
                //explode search in get
                $tempArray = explode(" as ", $data);
                $alias = $tempArray[1] . '_';
                //first part of search from get
                $columnName = explode(".",$tempArray[0]);
                $tableName = $columnName[0];
                if($columnName[1] === "*"){
                    unset($get[$key]);
                    $statement = self::prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = N'$tableName' AND TABLE_SCHEMA = '".Database::$db_name."';");
                    $statement->execute();
                    $statement = $statement->fetchAll(\PDO::FETCH_COLUMN);
                    //For each column in table,add alias
                    foreach ($statement as $column){
                        array_push($get,   $tableName.'.'.$column. ' as '.$alias.$column);
                    }
                }
            }
        }
        $statement =self::prepare("SELECT ". implode(',', $get) ." FROM $table $type JOIN $joinTable".$condition.$where);
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
    protected static function prepare($sql)
    {
        return Application::$app->database->pdo->prepare($sql);
    }
    public function up()
    {
        $db = \App\lscore\Application::$app->database;
        $db->table($this->table,$this->stockable);
    }
    public function down()
    {
        $db = \App\lscore\Application::$app->database;
        $table = $this->table;
        $db->pdo->exec("DROP TABLE $table;");
    }
    public function verifyForeignKeyArray()
    {
        if(count($this->foreignKey) > 0){
            return true;
        }
        return false;
    }
    public function addForeignKey()
    {
        $db = \App\lscore\Application::$app->database;
        $table = $this->table;
        foreach($this->foreignKey as $columnName => $datas){
            foreach ($datas as $key => $value){
                if(strtoupper($key) === "REFERENCES"){
                    $foreignTable = $value;
                }elseif (strtoupper($key) === "ON"){
                    $foreignId = $value;
                }elseif (strtoupper($key) === "DELETE"){
                    $onDelete = strtoupper($value);
                }elseif (strtoupper($key) === "UPDATE"){
                    $onUpdate = strtoupper($value);
                }
            }
            if (isset($foreignTable,$foreignId,$onDelete,$onUpdate)){
                $keyName = $table."_".$columnName."_foreign";
                $db->pdo->exec("ALTER TABLE $table ADD CONSTRAINT $keyName FOREIGN KEY ($columnName) REFERENCES $foreignTable($foreignId) ON DELETE $onDelete ON UPDATE $onUpdate;");
            }
        }
    }
    public function removeForeignKey()
    {
        $db = \App\lscore\Application::$app->database;
        $table = $this->table;
        $dbName = Database::$db_name;
        $statement = $db->pdo->prepare("SELECT * FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS AS C WHERE C.TABLE_NAME = '$table' AND C.TABLE_SCHEMA = '$dbName';");
        $statement->execute();
        $statement = $statement->fetchAll(\PDO::FETCH_OBJ);
        foreach ($statement as $data){
            if ($data->CONSTRAINT_NAME !== "PRIMARY"){
                $keyName = $data->CONSTRAINT_NAME;
                $db->pdo->exec("ALTER TABLE ".$dbName.'.'.$table." DROP FOREIGN KEY $keyName");
            }
        }
    }
}