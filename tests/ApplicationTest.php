<?php

namespace Looptribe\Paytoshi\Tests;

use Looptribe\Paytoshi\Application;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    public function testApiUrl()
    {
        $sut = new Application();

        $this->assertEquals('http://paytoshi.org/api/v1/', $sut['apiUrl']);
    }

    public function testConfig()
    {
        $sut = new Application();

        $this->assertEquals('localhost', $sut['db.options']['host']);
        $this->assertEquals('root', $sut['db.options']['user']);
        $this->assertEquals('root', $sut['db.options']['password']);
        $this->assertEquals('paytoshi_faucet', $sut['db.options']['dbname']);
    }
}
