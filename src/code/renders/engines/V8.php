<?php

namespace code\renders\engines;

use code\applications\ApiAppFactory;
use code\exceptions\EngineError;
use code\renders\RenderEngineInterface;
use code\service\ServiceTypes;
use V8JsException;

class V8 implements RenderEngineInterface {

    /** @var V8JsExt */
    protected $v8;

    public function __construct() {
        $this->v8 = new V8JsExt();
        $this->v8->setModuleLoader(function ($path) {
            return file_get_contents($path);
        });

        $this->v8->SetHostValue('queryStringValues', $_GET);
        $this->v8->SetHostValue('envConf', ApiAppFactory::getApp()->getService(ServiceTypes::CONFIGURATIONS)->createJSEnvinroment());
    }

    public function run(string $script, $first = true): string {
        try {
            if ($first) {
                ob_start();
            }
            $ret = $this->v8->executeString($script);
            if (is_null($ret)) {
                $ret = ob_get_contents();
            }
            if (is_object($ret)) {
                $ret = json_encode($ret);
            }
            return html_entity_decode($ret);
        } catch (V8JsException $exception) {
            var_dump("
                File: {$exception->getJsFileName()} \n
                Line Number: {$exception->getJsLineNumber()} \n
                Source Line: {$exception->getJsSourceLine()} \n
                Trace: {$exception->getJsTrace()}
              ");
            var_dump($exception->getMessage());
            throw EngineError::withException($exception);
        } catch (\Exception $exception) {
            var_dump($exception->getMessage());
            throw EngineError::withException($exception);
        } finally {
            //ob_end_clean();
        }
    }

    public function getDispatchHandler(): string {
        return 'print';
    }

}
