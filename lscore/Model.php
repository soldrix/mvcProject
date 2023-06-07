<?php

namespace App\lscore;

abstract class Model
{
    protected  $stockable = [];
    protected  $attributes = [];

    private function isFillable($key)
    {
        return array_key_exists($key, $this->stockable);
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
            if ($this->isFillable($key)) {
                $this->attributes[$key] = $value;
            }
        }
        return true;
    }
}