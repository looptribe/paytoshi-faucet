<?php

namespace Looptribe\Paytoshi\Security;

interface PasswordGeneratorInterface
{
    /**
     * @return string The newly generated password.
     */
    public function generate();
}
