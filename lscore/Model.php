<?php

namespace App\lscore;

abstract class Model
{
    protected $stockable = [];
    protected $attributes = [];

    public function isFillable($key)
    {
        return array_key_exists($key, $this->stockable);
    }

    protected function stockableFromArray($attributes)
    {
        if (count($this->stockable) > 0) {
            return array_intersect_key($attributes, array_flip($this->stockable));
        }

        return $attributes;
    }

    public function stock($attributes)
    {
        if(gettype($attributes) == "object"){
            //pour changer le format
            $attributes = json_decode(json_encode($attributes), true) ;
        }
        $fillable = $this->stockableFromArray($attributes);

        foreach ($fillable as $key => $value) {
            if ($this->isFillable($key)) {
                $this->attributes[$key] = $value;
            }else {
                error_log("An item is not stockable");
                return false;
            }
        }
        return true;
    }
}