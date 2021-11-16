<?php
namespace code\renders;

interface RenderEngineInterface 
{
    public function run(string $script): string;

    public function getDispatchHandler(): string;
}
