<?php

namespace code\user;

abstract class AppUser implements AppUserInterface {

    public abstract function getPassword(): string;

    public function passwordHash(string $value) {
        return password_hash($value, PASSWORD_DEFAULT);
    }

    public function passwordVerify(string $value) {

        return password_verify($value, $this->getPassword());
    }

}
