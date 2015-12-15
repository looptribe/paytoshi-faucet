<?php

namespace Looptribe\Paytoshi\Security;

class CryptPasswordHasher implements PasswordHasherInterface
{
    public function hash($password, $salt)
    {
        return crypt($password, $salt);
    }
}
