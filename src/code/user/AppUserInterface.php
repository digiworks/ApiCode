<?php

namespace code\user;

interface AppUserInterface {

    public abstract function getPassword(): string;

    public function passwordHash(string $value);

    public function passwordVerify(string $value);
}
