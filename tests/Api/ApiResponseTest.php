<?php

namespace Looptribe\Paytoshi\Tests\Api;

class ApiResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testParseSuccessful()
    {
        $response = $this->getMock('Buzz\Message\Response');

        $response
            ->expects($this->any())
            ->method('isSuccessful')
            ->willReturn(true);

        $response
            ->expects($this->any())
            ->method('getContent')
            ->willReturn('{"test":"abcdef","number":100}');

        $sut = $this->getMockForAbstractClass('Looptribe\Paytoshi\Api\ApiResponse', array($response));

        $this->assertTrue($sut->isSuccessful());
        $this->assertNull($sut->getError());
        $this->assertNull($sut->getErrorCode());
        $content = $sut->getContent();
        $this->assertSame('abcdef', $content['test']);
        $this->assertSame(100, $content['number']);
    }

    public function testParseError1()
    {
        $response = $this->getMock('Buzz\Message\Response');

        $response
            ->expects($this->any())
            ->method('isSuccessful')
            ->willReturn(false);

        $response
            ->expects($this->any())
            ->method('getContent')
            ->willReturn('{"error":true,"code":"NOT_ENOUGH_FUNDS"}');

        $sut = $this->getMockForAbstractClass('Looptribe\Paytoshi\Api\ApiResponse', array($response));

        $this->assertFalse($sut->isSuccessful());
        $this->assertNotEmpty($sut->getError());
        $this->assertSame('NOT_ENOUGH_FUNDS', $sut->getErrorCode());
    }

    public function testParseError2()
    {
        $response = $this->getMock('Buzz\Message\Response');

        $response
            ->expects($this->any())
            ->method('isSuccessful')
            ->willReturn(false);

        $response
            ->expects($this->any())
            ->method('getContent')
            ->willReturn('{"error":true,"code":"INVALID_ADDRESS"}');

        $sut = $this->getMockForAbstractClass('Looptribe\Paytoshi\Api\ApiResponse', array($response));

        $this->assertFalse($sut->isSuccessful());
        $this->assertNotEmpty($sut->getError());
        $this->assertSame('INVALID_ADDRESS', $sut->getErrorCode());
    }

    public function testParseError3()
    {
        $response = $this->getMock('Buzz\Message\Response');

        $response
            ->expects($this->any())
            ->method('isSuccessful')
            ->willReturn(false);

        $response
            ->expects($this->any())
            ->method('getContent')
            ->willReturn('{"error":true,"code":"FAUCET_DISABLED"}');

        $sut = $this->getMockForAbstractClass('Looptribe\Paytoshi\Api\ApiResponse', array($response));

        $this->assertFalse($sut->isSuccessful());
        $this->assertNotEmpty($sut->getError());
        $this->assertSame('FAUCET_DISABLED', $sut->getErrorCode());
    }

    public function testParseError4()
    {
        $response = $this->getMock('Buzz\Message\Response');

        $response
            ->expects($this->any())
            ->method('isSuccessful')
            ->willReturn(false);

        $response
            ->expects($this->any())
            ->method('getContent')
            ->willReturn('{"error":true,"code":"ACCESS_DENIED"}');

        $sut = $this->getMockForAbstractClass('Looptribe\Paytoshi\Api\ApiResponse', array($response));

        $this->assertFalse($sut->isSuccessful());
        $this->assertNotEmpty($sut->getError());
        $this->assertSame('ACCESS_DENIED', $sut->getErrorCode());
    }

    public function testParseError5()
    {
        $response = $this->getMock('Buzz\Message\Response');

        $response
            ->expects($this->any())
            ->method('isSuccessful')
            ->willReturn(false);

        $response
            ->expects($this->any())
            ->method('getContent')
            ->willReturn('{"error":true,"code":"INTERNAL_ERROR"}');

        $sut = $this->getMockForAbstractClass('Looptribe\Paytoshi\Api\ApiResponse', array($response));

        $this->assertFalse($sut->isSuccessful());
        $this->assertNotEmpty($sut->getError());
        $this->assertSame('INTERNAL_ERROR', $sut->getErrorCode());
    }

    public function testParseError6()
    {
        $response = $this->getMock('Buzz\Message\Response');

        $response
            ->expects($this->any())
            ->method('isSuccessful')
            ->willReturn(false);

        $response
            ->expects($this->any())
            ->method('getContent')
            ->willReturn('{"error":true,"code":"BAD_REQUEST"}');

        $sut = $this->getMockForAbstractClass('Looptribe\Paytoshi\Api\ApiResponse', array($response));

        $this->assertFalse($sut->isSuccessful());
        $this->assertNotEmpty($sut->getError());
        $this->assertSame('BAD_REQUEST', $sut->getErrorCode());
    }

    public function testParseError7()
    {
        $response = $this->getMock('Buzz\Message\Response');

        $response
            ->expects($this->any())
            ->method('isSuccessful')
            ->willReturn(false);

        $response
            ->expects($this->any())
            ->method('getContent')
            ->willReturn('{"error":true,"code":"MISSING_CODE"}');

        $sut = $this->getMockForAbstractClass('Looptribe\Paytoshi\Api\ApiResponse', array($response));

        $this->assertFalse($sut->isSuccessful());
        $this->assertContains('MISSING_CODE', $sut->getError());
        $this->assertSame('MISSING_CODE', $sut->getErrorCode());
    }

    public function testParseError8()
    {
        $response = $this->getMock('Buzz\Message\Response');

        $response
            ->expects($this->any())
            ->method('isSuccessful')
            ->willReturn(false);

        $response
            ->expects($this->any())
            ->method('getContent')
            ->willReturn('invalid json here');

        $sut = $this->getMockForAbstractClass('Looptribe\Paytoshi\Api\ApiResponse', array($response));

        $this->assertFalse($sut->isSuccessful());
        $this->assertNotEmpty($sut->getError());
        $this->assertSame('UNKNOWN', $sut->getErrorCode());
    }
}
