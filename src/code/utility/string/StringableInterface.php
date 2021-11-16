<?php

namespace code\utility\string;

interface StringableInterface {

    /**
     * Magic method to convert this object to string.
     *
     * @return  string
     */
    public function __toString();
}
