<?php

namespace Looptribe\Paytoshi\Security;

use Symfony\Component\Security\Core\Encoder\BasePasswordEncoder;

class CryptPasswordEncoder extends BasePasswordEncoder
{
    public function encodePassword($raw, $salt)
    {
        return crypt($raw, $salt);
    }

    public function isPasswordValid($encoded, $raw, $salt)
    {
        return $encoded === $this->encodePassword($raw, $encoded);
    }
}
