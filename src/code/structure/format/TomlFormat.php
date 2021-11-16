<?php

namespace code\structure\format;

use code\structure\StructureHelper;
use Traversable;
use Yosymfony\Toml\Toml;
use Yosymfony\Toml\TomlBuilder;

/**
 * Description of TomlFormat
 *
 * @author digiw
 */
class TomlFormat implements FormatInterface {

    /**
     * Converts an object into a formatted string.
     *
     * @param object $struct  Data Source Object.
     * @param array  $options An array of options for the formatter.
     *
     * @return  string  Formatted string.
     *
     * @since   2.0
     */
    public static function structToString($struct, array $options = []) {
        $tb = new TomlBuilder();

        if ($struct instanceof Traversable) {
            $struct = iterator_to_array($struct);
        } elseif (is_object($struct)) {
            $struct = get_object_vars($struct);
        } else {
            $struct = (array) $struct;
        }

        static::addValues($tb, $struct);

        return $tb->getTomlString();
    }

    /**
     * addValues
     *
     * @param TomlBuilder $tb
     * @param mixed       $struct
     * @param string|null $prefix
     *
     * @return  void
     *
     * @since  3.5.4
     */
    protected static function addValues(TomlBuilder $tb, $struct, ?string $prefix = null): void {
        foreach ($struct as $key => $value) {
            if (is_array($value) && StructureHelper::isAssociativeArray($value)) {
                $tb->addTable($key = trim($prefix . '.' . $key, '.'));

                static::addValues($tb, $value, $key);
            } else {
                $tb->addValue($key, $value);
            }
        }
    }

    /**
     * Converts a formatted string into an object.
     *
     * @param string $data    Formatted string
     * @param array  $options An array of options for the formatter.
     *
     * @return  object  Data Object
     *
     * @since   2.0
     */
    public static function stringToStruct($data, array $options = []) {
        return Toml::parse($data, (bool) ($options['resultAsObject'] ?? false));
    }

}
