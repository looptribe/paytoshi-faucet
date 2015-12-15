<?php

namespace Looptribe\Paytoshi\Security;

interface SaltGeneratorInterface
{
    /**
     * @return string
     */
    public function generate();
}
