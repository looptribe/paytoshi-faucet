<?php

namespace Looptribe\Paytoshi\Tests\Model;

use Looptribe\Paytoshi\Model\ConnectionFactory;

class ConnectionFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateShouldReturnConnection()
    {
        $sut = new ConnectionFactory();
        $result = $sut->create(array(
            'dbname' => 'test',
            'user' => 'user',
            'host' => 'localhost',
            'driver' => 'pdo_mysql',
        ));

        $this->assertInstanceOf('Doctrine\DBAL\Connection', $result);
    }
}
