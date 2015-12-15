<?php

namespace Looptribe\Paytoshi\Security;

interface PasswordHasherInterface
{
    /**
     * @param string $password
     * @param string $salt
     * @return string
     */
    public function hash($password, $salt);
}
