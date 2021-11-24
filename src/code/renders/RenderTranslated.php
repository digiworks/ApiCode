<?php

namespace code\renders;

abstract class RenderTranslated extends RenderTypes {

    protected function getTRanslationFiles($viewFile) {
        $baseDir = $this->fileSystem->dirname($viewFile);
        $localeFile = $baseDir . DIRECTORY_SEPARATOR . "locale" . DIRECTORY_SEPARATOR . "locale.js";
        if ($this->fileSystem->fileExists($localeFile)) {
            $this->addPart($localeFile);
        }
    }

}
