<?php

namespace code\user;

interface AppUserInterface {

    public function getPassword(): ?string;

    public function passwordHash(string $value);

    public function passwordVerify(string $value);
}
