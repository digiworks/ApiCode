<?php
namespace code\structure\format;

use code\structure\StructureHelper;
use Symfony\Component\Yaml\Dumper as SymfonyYamlDumper;
use Symfony\Component\Yaml\Parser as SymfonyYamlParser;


/**
 * Description of YamlFormat
 *
 * @author digiw
 */
class YamlFormat implements FormatInterface {

    /**
     * The YAML parser class.
     *
     * @var  \Symfony\Component\Yaml\Parser;
     */
    protected static $parser;

    /**
     * The YAML dumper class.
     *
     * @var  \Symfony\Component\Yaml\Dumper;
     */
    protected static $dumper;

    /**
     * Converts an object into a YAML formatted string.
     * We use json_* to convert the passed object to an array.
     *
     * @param   object $struct  Data source object.
     * @param   array  $options Options used by the formatter.
     *
     * @return  string  YAML formatted string.
     *
     * @since   2.0
     */
    public static function structToString($struct, array $options = []) {
        $inline = StructureHelper::getValue($options, 'inline', 2);
        $indent = StructureHelper::getValue($options, 'indent', 0);

        return static::getDumper()->dump($struct, $inline, $indent);
    }

    /**
     * Parse a YAML formatted string and convert it into an object.
     * We use the json_* methods to convert the parsed YAML array to an object.
     *
     * @param   string $data    YAML formatted string to convert.
     * @param   array  $options Options used by the formatter.
     *
     * @return  object  Data object.
     *
     * @since   2.0
     */
    public static function stringToStruct($data, array $options = []) {
        return static::getParser()->parse(trim($data));
    }

    /**
     * getParser
     *
     * @return  Parser
     */
    public static function getParser() {
        if (!static::$parser) {
            static::$parser = new SymfonyYamlParser();
        }

        return static::$parser;
    }

    /**
     * setParser
     *
     * @param   Parser $parser
     *
     * @return  YamlFormat  Return self to support chaining.
     */
    public static function setParser($parser) {
        static::$parser = $parser;
    }

    /**
     * getDumper
     *
     * @return  Dumper
     */
    public static function getDumper() {
        if (!static::$dumper) {
            static::$dumper = new SymfonyYamlDumper();
        }

        return static::$dumper;
    }

    /**
     * setDumper
     *
     * @param   Dumper $dumper
     *
     * @return  YamlFormat  Return self to support chaining.
     */
    public static function setDumper($dumper) {
        static::$dumper = $dumper;
    }

}
