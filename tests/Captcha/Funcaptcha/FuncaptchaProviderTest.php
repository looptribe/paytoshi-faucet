<?php

namespace Looptribe\Paytoshi\Tests\Captcha\Funcaptcha;

use Looptribe\Paytoshi\Captcha\CaptchaProviderException;
use Looptribe\Paytoshi\Captcha\Funcaptcha\FuncaptchaProvider;

class FuncaptchaProviderTest extends \PHPUnit_Framework_TestCase
{
    private $buzz;

    public function setUp()
    {
        $this->buzz = $this->getMockBuilder('Buzz\Browser')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testCheckAnswer1()
    {
        $sut = new FuncaptchaProvider($this->buzz, 'pubkey', 'privkey');

        $this->setExpectedException('Looptribe\Paytoshi\Captcha\CaptchaProviderException', 'Missing captcha response');
        $sut->checkAnswer(array());
    }

    public function testCheckAnswer2()
    {
        $sut = new FuncaptchaProvider($this->buzz, 'pubkey', 'privkey');

        $this->setExpectedException('Looptribe\Paytoshi\Captcha\CaptchaProviderException', 'Missing captcha response');
        $sut->checkAnswer(array('response'));
    }

    public function testCheckAnswer3()
    {
        $headers = array(
            'Connection' => 'Close',
        );
        $privkey = 'privkey';
        $pubkey = 'pubkey';
        $resp = 'resp';
        $data = sprintf('private_key=%s&session_token=%s&simple_mode=1', $privkey, $resp);

        $this->buzz->method('post')
            ->with(
                'https://funcaptcha.com/fc/v/',
                $headers,
                $data
            )
            ->willThrowException(new CaptchaProviderException('message'));

        $sut = new FuncaptchaProvider($this->buzz, $pubkey, $privkey);

        $this->setExpectedException('Looptribe\Paytoshi\Captcha\CaptchaProviderException', 'Failed to send captcha: message');
        $sut->checkAnswer(array('response' => $resp));
    }

    public function testCheckAnswer4()
    {
        $headers = array(
            'Connection' => 'Close',
        );
        $privkey = 'privkey';
        $pubkey = 'pubkey';
        $resp = 'resp';
        $data = sprintf('private_key=%s&session_token=%s&simple_mode=1', $privkey, $resp);

        $response = $this->getMock('Buzz\Message\Response');

        $response->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(false);

        $this->buzz->method('post')
            ->with(
                'https://funcaptcha.com/fc/v/',
                $headers,
                $data
            )
            ->willReturn($response);

        $sut = new FuncaptchaProvider($this->buzz, $pubkey, $privkey);

        $this->setExpectedException('Looptribe\Paytoshi\Captcha\CaptchaProviderException', 'Captcha response error:');
        $sut->checkAnswer(array('response' => $resp));
    }

    public function testCheckAnswer5()
    {
        $headers = array(
            'Connection' => 'Close',
        );
        $privkey = 'privkey';
        $pubkey = 'pubkey';
        $resp = 'resp';
        $data = sprintf('private_key=%s&session_token=%s&simple_mode=1', $privkey, $resp);

        $response = $this->getMock('Buzz\Message\Response');

        $response->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(true);

        $response->expects($this->once())
            ->method('getContent')
            ->willReturn('');

        $this->buzz->method('post')
            ->with(
                'https://funcaptcha.com/fc/v/',
                $headers,
                $data
            )
            ->willReturn($response);

        $sut = new FuncaptchaProvider($this->buzz, $pubkey, $privkey);

        $this->setExpectedException('Looptribe\Paytoshi\Captcha\CaptchaProviderException', 'Invalid captcha response error');
        $sut->checkAnswer(array('response' => $resp));
    }

    public function testCheckAnswer6()
    {
        $headers = array(
            'Connection' => 'Close',
        );
        $privkey = 'privkey';
        $pubkey = 'pubkey';
        $resp = 'resp';
        $data = sprintf('private_key=%s&session_token=%s&simple_mode=1', $privkey, $resp);

        $response = $this->getMock('Buzz\Message\Response');

        $response->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(true);

        $response->expects($this->once())
            ->method('getContent')
            ->willReturn('false');

        $this->buzz->method('post')
            ->with(
                'https://funcaptcha.com/fc/v/',
                $headers,
                $data
            )
            ->willReturn($response);

        $sut = new FuncaptchaProvider($this->buzz, $pubkey, $privkey);

        $answer = $sut->checkAnswer(array('response' => $resp));

        $this->assertInstanceOf('Looptribe\Paytoshi\Captcha\CaptchaProviderResponse', $answer);
        $this->assertFalse($answer->isSuccessful());
        $this->assertEquals('Invalid Captcha', $answer->getMessage());
    }

    public function testCheckAnswer7()
    {
        $headers = array(
            'Connection' => 'Close',
        );
        $privkey = 'privkey';
        $pubkey = 'pubkey';
        $resp = 'resp';
        $data = sprintf('private_key=%s&session_token=%s&simple_mode=1', $privkey, $resp);

        $response = $this->getMock('Buzz\Message\Response');

        $response->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(true);

        $response->expects($this->once())
            ->method('getContent')
            ->willReturn('1');

        $this->buzz->method('post')
            ->with(
                'https://funcaptcha.com/fc/v/',
                $headers,
                $data
            )
            ->willReturn($response);

        $sut = new FuncaptchaProvider($this->buzz, $pubkey, $privkey);

        $answer = $sut->checkAnswer(array('response' => $resp));

        $this->assertInstanceOf('Looptribe\Paytoshi\Captcha\CaptchaProviderResponse', $answer);
        $this->assertTrue($answer->isSuccessful());
        $this->assertNull($answer->getMessage());
    }

    public function testGetChallengeName()
    {
        $sut = new FuncaptchaProvider($this->buzz, 'pubkey', 'privkey');
        $challenge = $sut->getChallengeName();
        $this->assertEquals('', $challenge);
    }

    public function testGetResponseName()
    {
        $sut = new FuncaptchaProvider($this->buzz, 'pubkey', 'privkey');
        $response = $sut->getResponseName();
        $this->assertEquals('fc-token', $response);
    }
}
