<?php

namespace Looptribe\Paytoshi\Security;

class BCryptSaltGenerator implements SaltGeneratorInterface
{
    public function generate()
    {
        return '$2a$13$' . substr(strtr(base64_encode(mcrypt_create_iv(22)), '+', '.'), 0, 22);
    }
}
