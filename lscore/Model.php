<?php

namespace App\lscore;

abstract class Model
{
    protected $stockable = [];
    protected $attributes = [];

    public function __construct(array $attributes = [])
    {
        $this->stock($attributes);
    }

    public function isFillable($key)
    {

        // If the key is in the "fillable" array, we can of course assume that it's
        // a fillable attribute. Otherwise, we will check the guarded array when
        // we need to determine if the attribute is black-listed on the model.
        if (in_array($key, $this->stockable)) {
            return true;
        }

        return empty($this->stockable) &&
            ! str_contains($key, '.') &&
            ! str_starts_with($key, '_');
    }

    protected function stockableFromArray(array $attributes)
    {
        if (count($this->stockable) > 0) {
            return array_intersect_key($attributes, array_flip($this->stockable));
        }

        return $attributes;
    }

    public function stock(array $attributes)
    {

        $fillable = $this->stockableFromArray($attributes);

        foreach ($fillable as $key => $value) {
            if ($this->isFillable($key)) {
                $this->attributes[$key] = $value;
            }
        }
        return $this;
    }
}