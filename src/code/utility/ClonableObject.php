<?php
namespace code\utility;


class ClonableObject {

    /**
     * getReturnInstance
     *
     * @param callable $callback
     *
     * @return static
     */
    protected function cloneInstance(callable $callback = null) {
        $new = clone $this;

        if ($callback === null) {
            return $new;
        }

        $callback($new);

        return $new;
    }

}
