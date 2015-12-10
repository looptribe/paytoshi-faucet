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

namespace Tests\Looptribe\Paytoshi\Servicec;

use Looptribe\Paytoshi\Service\Ip\IpMatcherService;
use Looptribe\Paytoshi\Service\Ip\IpService;
use Looptribe\Paytoshi\Service\Ip\IpValidatorService;
use PHPUnit_Framework_TestCase;
use Slim\Environment;

class IpServiceTest extends PHPUnit_Framework_TestCase
{
    public function testCustomHeader()
    {
        $headers = array(
            'CUSTOM_HEADER'
        );
        $ipValidator = new IpValidatorService();
        $ipMatcher = new IpMatcherService();
        $sut = new IpService($ipValidator, $ipMatcher, true, array(), $headers);
        $serverParams = array(
            'REMOTE_ADDR' => '192.168.1.1',
            'CUSTOM_HEADER' => '192.168.1.3'
        );
        $ipAddress = $sut->determineClientIpAddress($serverParams);
        $this->assertSame('192.168.1.3', $ipAddress);
    }

    public function testIpSetByRemoteAddr()
    {
        $ipValidator = new IpValidatorService();
        $ipMatcher = new IpMatcherService();
        $sut = new IpService($ipValidator, $ipMatcher);
        $serverParams = array(
            'REMOTE_ADDR' => '192.168.1.1',
        );
        $ipAddress = $sut->determineClientIpAddress($serverParams);
        $this->assertSame('192.168.1.1', $ipAddress);
    }

    public function testIpIsNullIfMissing()
    {
        $ipValidator = new IpValidatorService();
        $ipMatcher = new IpMatcherService();
        $sut = new IpService($ipValidator, $ipMatcher);
        $serverParams = array();
        $ipAddress = $sut->determineClientIpAddress($serverParams);
        $this->assertNull($ipAddress);
    }

    public function testXForwardedForIp()
    {
        $ipValidator = new IpValidatorService();
        $ipMatcher = new IpMatcherService();
        $sut = new IpService($ipValidator, $ipMatcher, true);
        $serverParams = array(
            'REMOTE_ADDR' => '192.168.1.1',
            'HTTP_X_FORWARDED_FOR' => '192.168.1.3, 192.168.1.2, 192.168.1.1'
        );
        $ipAddress = $sut->determineClientIpAddress($serverParams);
        $this->assertSame('192.168.1.3', $ipAddress);
    }

    public function testXForwardedForIgnored()
    {
        $ipValidator = new IpValidatorService();
        $ipMatcher = new IpMatcherService();
        $sut = new IpService($ipValidator, $ipMatcher);
        $serverParams = array(
            'REMOTE_ADDR' => '192.168.0.1',
            'HTTP_X_FORWARDED_FOR' => '192.168.1.3, 192.168.1.2, 192.168.1.1'
        );
        $ipAddress = $sut->determineClientIpAddress($serverParams);
        $this->assertSame('192.168.0.1', $ipAddress);
    }

    public function testXForwardedForWithInvalidIp()
    {
        $ipValidator = new IpValidatorService();
        $ipMatcher = new IpMatcherService();
        $sut = new IpService($ipValidator, $ipMatcher, true);
        $serverParams = array(
            'REMOTE_ADDR' => '192.168.1.1',
            'HTTP_X_FORWARDED_FOR' => 'invalid'
        );
        $ipAddress = $sut->determineClientIpAddress($serverParams);
        $this->assertSame('192.168.1.1', $ipAddress);
    }

    public function testXForwardedForWithTrustedProxy()
    {
        $ipValidator = new IpValidatorService();
        $ipMatcher = new IpMatcherService();
        $sut = new IpService($ipValidator, $ipMatcher, true, array('192.168.0.1', '192.168.0.2'));
        $serverParams = array(
            'REMOTE_ADDR' => '192.168.0.2',
            'HTTP_X_FORWARDED_FOR' => '192.168.1.3, 192.168.1.2, 192.168.1.1'
        );
        $ipAddress = $sut->determineClientIpAddress($serverParams);
        $this->assertSame('192.168.1.3', $ipAddress);
    }

    public function testXForwardedForWithTrustedProxyWithRange()
    {
        $ipValidator = new IpValidatorService();
        $ipMatcher = new IpMatcherService();
        $sut = new IpService($ipValidator, $ipMatcher, true, array('192.168.0.0/24'));
        $serverParams = array(
            'REMOTE_ADDR' => '192.168.0.2',
            'HTTP_X_FORWARDED_FOR' => '192.168.1.3, 192.168.1.2, 192.168.1.1'
        );
        $ipAddress = $sut->determineClientIpAddress($serverParams);
        $this->assertSame('192.168.1.3', $ipAddress);
    }

    public function testXForwardedForWithUntrustedProxy()
    {
        $ipValidator = new IpValidatorService();
        $ipMatcher = new IpMatcherService();
        $sut = new IpService($ipValidator, $ipMatcher, true, array('192.168.0.1'));
        $serverParams = array(
            'REMOTE_ADDR' => '192.168.0.2',
            'HTTP_X_FORWARDED_FOR' => '192.168.1.3, 192.168.1.2, 192.168.1.1'
        );
        $ipAddress = $sut->determineClientIpAddress($serverParams);
        $this->assertSame('192.168.0.2', $ipAddress);
    }

    public function testXForwardedForWithUntrustedProxyWithRange()
    {
        $ipValidator = new IpValidatorService();
        $ipMatcher = new IpMatcherService();
        $sut = new IpService($ipValidator, $ipMatcher, true, array('192.168.1.0/24'));
        $serverParams = array(
            'REMOTE_ADDR' => '192.168.0.2',
            'HTTP_X_FORWARDED_FOR' => '192.168.1.3, 192.168.1.2, 192.168.1.1'
        );
        $ipAddress = $sut->determineClientIpAddress($serverParams);
        $this->assertSame('192.168.0.2', $ipAddress);
    }

    public function testHttpClientIp()
    {
        $ipValidator = new IpValidatorService();
        $ipMatcher = new IpMatcherService();
        $sut = new IpService($ipValidator, $ipMatcher, true);
        $serverParams = array(
            'REMOTE_ADDR' => '192.168.1.1',
            'HTTP_CLIENT_IP' => '192.168.1.3'
        );
        $ipAddress = $sut->determineClientIpAddress($serverParams);
        $this->assertSame('192.168.1.3', $ipAddress);
    }

    public function testNullForIpv6()
    {
        $ipValidator = $this->getMockBuilder('Looptribe\Paytoshi\Service\Ip\IpValidatorService')
            ->getMock();
        $ipValidator->expects($this->once())
            ->method('validate')
            ->willReturn(false);
        $ipMatcher = new IpMatcherService();
        $sut = new IpService($ipValidator, $ipMatcher);
        $serverParams = array(
            'REMOTE_ADDR' => '001:DB8::21f:5bff:febf:ce22:8a2e',
        );
        $ipAddress = $sut->determineClientIpAddress($serverParams);
        $this->assertNull($ipAddress);
    }

}