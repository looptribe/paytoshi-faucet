<?php

namespace Looptribe\Paytoshi\Tests\Api;

use Looptribe\Paytoshi\Api\PaytoshiApi;

class PaytoshiApiTest extends \PHPUnit_Framework_TestCase
{
    public function testSend()
    {
        $response = $this->getMock('Buzz\Message\Response');

        $response
            ->expects($this->any())
            ->method('isSuccessful')
            ->willReturn(true);

        $response
            ->expects($this->any())
            ->method('getContent')
            ->willReturn('{"recipient":"mipcBbFg9gMiCh81Kj8tqqdgoZub1ZJRfn","amount":1000}');

        $buzz = $this->getMockBuilder('Buzz\Browser')
            ->disableOriginalConstructor()
            ->getMock();

        $buzz
            ->expects($this->once())
            ->method('post')
            ->with('http://www.example.net/api/faucet/send?apikey=myapikey')
            ->willReturn($response);

        $sut = new PaytoshiApi($buzz, 'http://www.example.net/api/');

        $result = $sut->send('myapikey', 'mipcBbFg9gMiCh81Kj8tqqdgoZub1ZJRfn', 1000, '1.2.3.4');

        $this->assertTrue($result->isSuccessful());
        $this->assertSame(1000, $result->getAmount());
        $this->assertSame('mipcBbFg9gMiCh81Kj8tqqdgoZub1ZJRfn', $result->getRecipient());
    }
}
