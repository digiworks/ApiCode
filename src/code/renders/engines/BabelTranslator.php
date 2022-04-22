<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace code\renders\engines;

use code\exceptions\EngineError;
use code\renders\RenderEngineInterface;
use code\utility\Curl;

/**
 * Description of BabelTranslator
 *
 * @author digiw
 */
class BabelTranslator {

    const command = "(Babel.transform('%s', {presets:['es2017', 'react', 'stage-3'],plugins: ['transform-react-jsx', 'syntax-async-functions']}).code);";
    const type = "text/babel";

    protected $lib = "js/engines/react/babel/6.26.0/babel.js"; //"https://cdnjs.cloudflare.com/ajax/libs/babel-standalone/6.26.0/babel.js";
    protected $engine;

    public function __construct(?RenderEngineInterface $engine) {
        $this->engine = $engine;
        $this->loadLib();
    }

    public function setLib($lib) {
        $this->lib = $lib;
    }

    public function getLib() {
        return $this->lib;
    }

    protected function loadLib() {
        if (!is_null($this->engine)) {
            $script = Curl::get($this->lib);
            try {

                $result = $this->engine->run($script);
            } catch (EngineError $exception) {
                throw $exception->getException();
            }
        }
    }

    /**
     * 
     * @param string $script
     * @return string
     */
    public function transform($script) {
        $ret = $script;
        if (!is_null($this->engine)) {
            $babel_text = $this->comporess($script);
            $ret = $this->engine->run(sprintf(static::command, $babel_text));
        }
        return $ret;
    }

    /**
     * 
     * @param string $script
     * @return string
     */
    private function comporess($script) {
        $script = str_replace("\\", "\\\\", $script);
        $script = str_replace("'", "\'", $script);
        //$this->sinlgeLineComments($script);
        $script = trim(preg_replace('/[\t\n\r\s]+/', ' ', $script));
        return $script;
    }

    /**
     * 
     * @param string $output
     */
    private function sinlgeLineComments(&$script) {
        $script = preg_replace_callback('#^((?:(?!/\*).)*?)//(.*)#m',
                create_function(
                        '$match',
                        'return "/* " . trim(mb_substr($match[1], 0)) . " */";'
                ), $script
        );
    }

    /**
     * 
     * @param type $type
     * @return type
     */
    public function isType($type) {
        return !strcmp(static::type, $type);
    }

    public function getTypeString() {
        return static::type;
    }

}
