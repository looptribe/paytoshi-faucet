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

use Looptribe\Paytoshi\Service\Ip\IpValidatorService;

class IpValidatorServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testValidIpv4Address()
    {
        $sut = new IpValidatorService();
        $result = $sut->validate('192.168.0.1');
        $this->assertTrue($result);
    }

    public function testInvalidStringIpv4Address()
    {
        $sut = new IpValidatorService();
        $result = $sut->validate('invalid');
        $this->assertFalse($result);
    }

    public function testInvalidNumberIpv4Address()
    {
        $sut = new IpValidatorService();
        $result = $sut->validate('123');
        $this->assertFalse($result);
    }

    public function testValidIpv6Address()
    {
        $sut = new IpValidatorService();
        $result = $sut->validate('001:DB8::21f:5bff:febf:ce22:8a2e');
        $this->assertFalse($result);
    }
}