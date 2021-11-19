<?php

namespace code\user;

abstract class AppUser {
    
    public function passwordHash(string $value){
        return password_hash($value,PASSWORD_DEFAULT);
    }
}
