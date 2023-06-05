<?php

namespace App\lscore;

abstract class DBModel extends Model
{
    public string $table;

    /**
     * Add sign to array/object value
    */
    private function addSign($datas)
    {
        foreach ($datas as $key => $item){
            $datas[$key] = (count(explode(" ", $key)) < 2) ? "$key='$item'" : "$key'$item'";
        }
        return $datas;
    }
    public function Create($datas)
    {
        if($this->stock($datas)){
            $table = $this->table;
            $attributes = $this->stockable;
            $params = array_map(fn($attr) => ":$attr", $attributes);
            $statement = self::prepare("INSERT INTO $table (".implode(",",$attributes).")
            VALUES (".implode(",",$params).");");
            foreach ($attributes as $attribute){
                $statement->bindParam(":$attribute", $this->attributes[$attribute]);
            }
            $statement->execute();
        }
    }
    public function get(array $params = ["*"], $DISTINCT = false)
    {
        $table = $this->table;
        $DISTINCT = ($DISTINCT === false) ? "": "DISTINCT ";
        $statement = self::prepare("SELECT $DISTINCT" . implode(',',$params) . " FROM $table;");
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function find(array $search,array $params = ["*"])
    {
        $table = $this->table;
        $search = $this->addSign($search);
        $statement = self::prepare("SELECT " . implode(',',$params) . " FROM $table WHERE "
            . implode(" AND ", $search) . ";");
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function delete($id)
    {
        $table = $this->table;
        $statement = self::prepare("DELETE FROM $table WHERE id = $id");
        $statement->execute();
    }
    public function update($datas,$where)
    {
        if($this->stock($datas)){
            $table = $this->table;
            $user = $this->find($where,array_keys($this->attributes));
            $where = $this->addSign($where);
            $diff_value = array_diff_assoc($this->attributes,$user[0]);
            if(count($diff_value) > 0){
                $values = $this->addSign($diff_value);
                $statement = self::prepare("UPDATE $table set ". implode(" AND ", $values) . " WHERE "
                    . implode(" AND ", $where) . ";");
                $statement->execute();
            }

        }

    }
    public function join($type, $joinTable, $currentTableKey , $joinTableKey, $sign,array $get = ["*"], array $where = [])
    {
        $table = $this->table;
        $where = (count($where) > 0) ? " WHERE ". implode(" AND ", $this->addSign($where)) . ";" : ";";
        $type = strtoupper($type);
        $condition = ($type !== "CROSS" && $type!== "NATURAL") ? " ON $table.$currentTableKey $sign $joinTable.$joinTableKey" : "";
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
        $table = $this->table;
        $array  = $this->stockable;
        $db->table($table,$array);
    }
    public function down()
    {
        $db = \App\lscore\Application::$app->database;
        $table = $this->table;
        $db->pdo->exec("DROP TABLE $table;");
    }
}