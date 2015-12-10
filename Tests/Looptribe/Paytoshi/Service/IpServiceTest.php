<?php

namespace Tests\Looptribe\Paytoshi\Service;

use Looptribe\Paytoshi\Service\IpService;
use PHPUnit_Framework_TestCase;

class IpServiceTest extends PHPUnit_Framework_TestCase
{
    public function testCustomHeader()
    {
        $headers = array(
            'CUSTOM_HEADER'
        );
        $sut = new IpService(true, array(), $headers);
        $serverParams = array(
            'REMOTE_ADDR' => '192.168.1.1',
            'CUSTOM_HEADER' => '192.168.1.3'
        );
        $ipAddress = $sut->determineClientIpAddress($serverParams);
        $this->assertSame('192.168.1.3', $ipAddress);
    }

    public function testIpSetByRemoteAddr()
    {
        $sut = new IpService();
        $serverParams = array(
            'REMOTE_ADDR' => '192.168.1.1',
        );
        $ipAddress = $sut->determineClientIpAddress($serverParams);
        $this->assertSame('192.168.1.1', $ipAddress);
    }

    public function testIpIsNullIfMissing()
    {
        $sut = new IpService();
        $serverParams = array();
        $ipAddress = $sut->determineClientIpAddress($serverParams);
        $this->assertNull($ipAddress);
    }

    public function testXForwardedForIp()
    {
        $sut = new IpService(true);
        $serverParams = array(
            'REMOTE_ADDR' => '192.168.1.1',
            'HTTP_X_FORWARDED_FOR' => '192.168.1.3, 192.168.1.2, 192.168.1.1'
        );
        $ipAddress = $sut->determineClientIpAddress($serverParams);
        $this->assertSame('192.168.1.3', $ipAddress);
    }

    public function testXForwardedForIgnored()
    {
        $sut = new IpService();
        $serverParams = array(
            'REMOTE_ADDR' => '192.168.0.1',
            'HTTP_X_FORWARDED_FOR' => '192.168.1.3, 192.168.1.2, 192.168.1.1'
        );
        $ipAddress = $sut->determineClientIpAddress($serverParams);
        $this->assertSame('192.168.0.1', $ipAddress);
    }

    public function testXForwardedForWithInvalidIp()
    {
        $sut = new IpService(true);
        $serverParams = array(
            'REMOTE_ADDR' => '192.168.1.1',
            'HTTP_X_FORWARDED_FOR' => 'invalid'
        );
        $ipAddress = $sut->determineClientIpAddress($serverParams);
        $this->assertSame('192.168.1.1', $ipAddress);
    }

    public function testXForwardedForWithTrustedProxy()
    {
        $sut = new IpService(true, array('192.168.0.1', '192.168.0.2'));
        $serverParams = array(
            'REMOTE_ADDR' => '192.168.0.2',
            'HTTP_X_FORWARDED_FOR' => '192.168.1.3, 192.168.1.2, 192.168.1.1'
        );
        $ipAddress = $sut->determineClientIpAddress($serverParams);
        $this->assertSame('192.168.1.3', $ipAddress);
    }

    public function testXForwardedForWithUntrustedProxy()
    {
        $sut = new IpService(true, array('192.168.0.1'));
        $serverParams = array(
            'REMOTE_ADDR' => '192.168.0.2',
            'HTTP_X_FORWARDED_FOR' => '192.168.1.3, 192.168.1.2, 192.168.1.1'
        );
        $ipAddress = $sut->determineClientIpAddress($serverParams);
        $this->assertSame('192.168.0.2', $ipAddress);
    }

    public function testHttpClientIp()
    {
        $sut = new IpService(true);
        $serverParams = array(
            'REMOTE_ADDR' => '192.168.1.1',
            'HTTP_CLIENT_IP' => '192.168.1.3'
        );
        $ipAddress = $sut->determineClientIpAddress($serverParams);
        $this->assertSame('192.168.1.3', $ipAddress);
    }

    public function testIpv6SetByRemoteAddr()
    {
        $sut = new IpService();
        $serverParams = array(
            'REMOTE_ADDR' => '001:DB8::21f:5bff:febf:ce22:8a2e',
        );
        $ipAddress = $sut->determineClientIpAddress($serverParams);
        $this->assertNull($ipAddress);
    }

}