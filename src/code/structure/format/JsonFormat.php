<?php

namespace code\structure\format;

use code\structure\format\FormatInterface;
use code\structure\StructureHelper;


class JsonFormat implements FormatInterface{

    /**
     * Converts an object into a JSON formatted string.
     *
     * @param   object $struct  Data source object.
     * @param   array  $options Options used by the formatter.
     *
     * @return  string
     */
    public static function structToString($struct, array $options = []) {
        $depth = StructureHelper::getValue($options, 'depth');
        $option = StructureHelper::getValue($options, 'options', 0);

        $depth = $depth ?: 512;

        return json_encode($struct, $option, $depth);
    }

    /**
     * Parse a JSON formatted string and convert it into an object.
     *
     * @param   string $data    JSON formatted string to convert.
     * @param   array  $options Options used by the formatter.
     *
     * @return  object   Data object.
     */
    public static function stringToStruct($data, array $options = []) {
        $assoc = StructureHelper::getValue($options, 'assoc', false);
        $depth = StructureHelper::getValue($options, 'depth', 512);
        $option = StructureHelper::getValue($options, 'options', 0);

        return json_decode(trim($data), $assoc, $depth, $option);
    }

    /**
     * prettyPrint
     *
     * @return  bool|int
     */
    public static function prettyPrint() {
        return defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : false;
    }

}
