<?php

namespace code\security\permissions;

abstract class PermissionProvider {

    private $user_empty = false;

    public function setUserEmpty($val) {
        $this->userEmpty = $val;
    }

    public function isUserEmpty() {
        return $this->userEmpty;
    }

    public abstract function check($permission, $userid);
}
