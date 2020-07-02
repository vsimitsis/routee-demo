<?php

namespace Routee\Models;

abstract class Model
{
    public function __construct($args = null)
    {
        if ($args) {
            $this->populate($args);
        }
    }

    /**
     * Populates model properties from data
     *
     * @param array|\stdClass $data
     * @return object   Representing an instance of the child class
     */
    public function populate($data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
        return $this;
    }
}
