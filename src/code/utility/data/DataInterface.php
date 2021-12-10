<?php

namespace code\utility\data;

interface DataInterface {

    /**
     * Bind the data into this object.
     *
     * @param   mixed   $values       The data array or object.
     * @param   boolean $replaceNulls Replace null or not.
     *
     * @return  static Return self to support chaining.
     */
    public function bind($values, $replaceNulls = false);

    /**
     * Is this object empty?
     *
     * @return  boolean
     */
    public function isNull();

    /**
     * Is this object has properties?
     *
     * @return  boolean
     */
    public function notNull();

    /**
     * Dump all data as array
     *
     * @return  array
     */
    public function dump();

    /**
     * __get
     *
     * @param   string $name
     *
     * @return  mixed
     */
    public function __get($name);
}
