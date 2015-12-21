<?php

namespace Looptribe\Paytoshi\Model;

use Doctrine\DBAL\DriverManager;

class ConnectionFactory
{
    public function create($connectionParams)
    {
        return DriverManager::getConnection($connectionParams);
    }
}
