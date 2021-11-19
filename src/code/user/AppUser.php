<?php

namespace code\user;

abstract class AppUser {
    
    public abstract function getHash() : string;
    
    public function passwordHash(string $value){
        return password_hash($value,PASSWORD_DEFAULT);
    }
    
    public function verifyPassword(string $value){
        $ret = false;
        $pwdHash = $this->passwordHash($value);
        if($pwdHash === $this->getHash()){
            $ret = true;
        }
        return $ret;
    }
}
