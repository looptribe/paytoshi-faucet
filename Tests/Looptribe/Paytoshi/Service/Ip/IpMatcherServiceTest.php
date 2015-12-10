<?php

/**
 * Paytoshi Faucet Script
 *
 * Contact: info@paytoshi.org
 *
 * @author: Looptribe
 * @link: https://paytoshi.org
 * @package: Looptribe\Paytoshi
 */

namespace Tests\Looptribe\Paytoshi\Service;

use Looptribe\Paytoshi\Service\Ip\IpMatcherService;
use PHPUnit_Framework_TestCase;

class IpMatcherServiceTest extends PHPUnit_Framework_TestCase
{
    public function testOkInRange24()
    {
        $sut = new IpMatcherService();
        $result = $sut->match('192.168.0.1', '192.168.0.0/24');
        $this->assertTrue($result);
    }

    public function testOkInRange16()
    {
        $sut = new IpMatcherService();
        $result = $sut->match('192.168.0.1', '192.168.0.0/16');
        $this->assertTrue($result);
    }

    public function testOkInRange8()
    {
        $sut = new IpMatcherService();
        $result = $sut->match('192.168.0.1', '192.0.0.0/8');
        $this->assertTrue($result);
    }

    public function testOkinSingleIp()
    {
        $sut = new IpMatcherService();
        $result = $sut->match('192.168.0.0', '192.168.0.0');
        $this->assertTrue($result);
    }

    public function testKoInRange24()
    {
        $sut = new IpMatcherService();
        $result = $sut->match('192.168.1.0', '192.168.0.0/24');
        $this->assertFalse($result);
    }

    public function testKoInRange16()
    {
        $sut = new IpMatcherService();
        $result = $sut->match('192.169.0.0', '192.168.0.0/16');
        $this->assertFalse($result);
    }

    public function testKoInRange8()
    {
        $sut = new IpMatcherService();
        $result = $sut->match('193.0.0.0', '192.0.0.0/8');
        $this->assertFalse($result);
    }

    public function testKoinSingleIp()
    {
        $sut = new IpMatcherService();
        $result = $sut->match('192.168.0.0', '192.168.1.0');
        $this->assertFalse($result);
    }

    public function testKoInvalidIp()
    {
        $sut = new IpMatcherService();
        $result = $sut->match('invalid', '192.168.0.0/24');
        $this->assertFalse($result);
    }

    public function testKoInvalidRange()
    {
        $sut = new IpMatcherService();
        $result = $sut->match('192.168.0.1', 'invalid');
        $this->assertFalse($result);
    }

    public function testKoNullIp()
    {
        $sut = new IpMatcherService();
        $result = $sut->match(null, '192.168.0.0/24');
        $this->assertFalse($result);
    }

    public function testKoNullRange()
    {
        $sut = new IpMatcherService();
        $result = $sut->match('192.168.0.0', null);
        $this->assertFalse($result);
    }
}