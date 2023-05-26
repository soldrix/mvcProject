<?php

namespace App\lscore;

abstract class DBModel extends Model
{
    public string $table;

    public function Create($datas)
    {
        if($this->stock($datas)){
            $table = $this->table;
            $attributes = $this->stockable;
            $params = array_map(fn($attr) => ":$attr", $attributes);
            $statement = self::prepare("INSERT INTO $table (".implode(",",$attributes).")
            VALUES (".implode(",",$params).")
        ");
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
        foreach ($search as $key => $item){
            if (count(explode(" ", $key)) < 2){
                $search[$key] = $key . "='$item'";
            }else{
                $search[$key] = $key . "'$item'";
            }
        }
        $statement = self::prepare("SELECT " . implode(',',$params) . " FROM $table WHERE " . implode(" AND ", $search) . ";");
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_OBJ);
    }
    public function delete($id)
    {
        $table = $this->table;
        $statement = self::prepare("DELETE FROM $table WHERE id = $id");
        $statement->execute();
    }
    protected static function prepare($sql)
    {
        return Application::$app->database->pdo->prepare($sql);
    }
}