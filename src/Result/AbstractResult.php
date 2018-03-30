<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 18-3-29
 */

namespace fk\openweixin\Result;

abstract class AbstractResult
{
    public function __construct($properties)
    {
        if (is_string($properties)) $properties = json_decode($properties, true) ?: [];
        foreach ($properties as $name => $value) {
            if (property_exists($this, $name)) $this->$name = $value;
        }
    }
}