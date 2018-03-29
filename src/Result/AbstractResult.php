<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 18-3-29
 */

abstract class AbstractResult
{
    protected $attributes;

    public function __construct($properties)
    {
        foreach ($properties as $name => $value) {
            if (method_exists($this, $name)) $this->$name = $value;
        }
    }
}