<?php

namespace code\utility;

use Laminas\Escaper\Escaper as LaminasEscaper;
use Laminas\Filter\StripTags;

class Escaper {

    private $escaper;

    public function __construct() {
        $this->escaper = new LaminasEscaper('utf-8');
    }

    /**
     * 
     * @param string $input
     * @return string
     */
    public function escapeHTML(string $input): string {
        return $this->escaper->escapeHtml($input);
    }

    /**
     * 
     * @param string $input
     * @return string
     */
    public function escapeHtmlAttr(string $input): string {
        return $this->escaper->escapeHtmlAttr($input);
    }

    /**
     * 
     * @param array $values
     * @return array
     */
    public function escapeArrayHTML(array $values): array {
        $escaped_values = [];
        foreach ($values as $key => $value) {
            $escaped_values[$key] = $this->escapeHTML($value);
        }
        return $escaped_values;
    }

    public function escapeJs(string $input) {
        return $this->escaper->escapeJs($input);
    }

    public function escapeArray(array $values) {
        $escaped_values = [];
        foreach ($values as $key => $value) {
            $escaped_values[$key] = $this->escapeJs($this->escapeHTML($value));
        }
        return $escaped_values;
    }

    /**
     * 
     * @param string $input
     * @return string
     */
    public function stripTags(string $input) {
        $filter = new StripTags();
        return $filter->filter($input);
    }

    /**
     * 
     * @param array $values
     * @return array
     */
    public function filterArray(array $values) {
        $escaped_values = [];
        foreach ($values as $key => $value) {
            if (is_string($value)) {
                $escaped_values[$key] = $this->stripTags($value);
            } else {
                $escaped_values[$key] = $value;
            }
        }
        return $escaped_values;
    }

}
