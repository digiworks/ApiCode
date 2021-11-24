<?php
namespace code\renders;

use code\exceptions\ServerScriptDoesNotExist;
use code\utility\Curl;


abstract class Loader 
{
    private $parts = [];
    
    
    public function addPart($viewPart)
    {
        $this->parts[] = $viewPart;
        return $this;
    }
    
    public function load()
    {
        $fileSystem = ApiAppFactory::getApp()->getService(ServiceTypes::FILESYSTEM);
        $view = "";
        foreach($this->parts as $part)
        {
            if (!$fileSystem->fileExists($part)) {
               throw ServerScriptDoesNotExist::atPath($part);
            }
            $script = Curl::get($part);
            $view .= $script ."\n;";
        }
        return $view;
    }
    
    /**
     * 
     * @param string $script
     * @return string
     */
    protected function compress($script)
    {
       $script = str_replace("'", "\x27", $script);
       //$this->sinlgeLineComments($script);
       $script = trim(preg_replace('/[\t\n\r\s]+/', ' ', $script));
       $script = trim(preg_replace( '/>(\s)+</m', '><', $script));
       return $script;
    }
    
    /**
     * 
     * @param string $output
     */
    protected function sinlgeLineComments(&$script) 
    {
        $script = preg_replace_callback('#^((?:(?!/\*).)*?)//(.*)#m',
        create_function(
          '$match',
          'return "/* " . trim(mb_substr($match[1], 0)) . " */";'
        ), $script
       );
    }
    
    public abstract function render();
}
