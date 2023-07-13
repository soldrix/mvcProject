<?php

namespace App\lscore;

abstract class Model
{
    protected  $stockable = [];
    protected  $attributes = [];

    public function isFillable($key)
    {
        return in_array($key, $this->stockable);
    }


    private function stockableFromArray($attributes)
    {
        if (count($this->stockable) > 0) {
            $body = [];
            foreach ($this->stockable as $key => $value){
                foreach ($attributes as $attrKey => $attribute){
                    if($key === $attrKey){
                        $body[$key] =  $attributes[$key];
                    }
                }
            }

          return $body;
        }

        return $attributes;
    }

    public function stock($attributes): bool
    {
        if(gettype($attributes) == "object"){
            //pour changer le format
            $attributes = json_decode(json_encode($attributes), true) ;
        }
        $fillable = $this->stockableFromArray($attributes);

        foreach ($fillable as $key => $value) {
            if (array_key_exists($key, $this->stockable)) {
                $this->attributes[$key] = $value;
            }
        }
        return true;
    }
}