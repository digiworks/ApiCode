<?php

namespace code\user;

abstract class AppUser {
    
    public function passwordHash(string $value){
        return password_hast($value,PASSWORD_DEFAULT);
    }
}
