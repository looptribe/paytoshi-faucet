<?php

namespace Looptribe\Paytoshi\Tests\Captcha\Recaptcha;

use Looptribe\Paytoshi\Captcha\Recaptcha\RecaptchaProvider;

class RecaptchaProviderTest extends \PHPUnit_Framework_TestCase
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
        $sut = new RecaptchaProvider($this->buzz, 'pubkey', 'privkey');

        $this->setExpectedException('Looptribe\Paytoshi\Captcha\CaptchaProviderException', 'Missing captcha ip');
        $sut->checkAnswer(array());
    }

    public function testCheckAnswer2()
    {
        $sut = new RecaptchaProvider($this->buzz, 'pubkey', 'privkey');

        $this->setExpectedException('Looptribe\Paytoshi\Captcha\CaptchaProviderException', 'Missing captcha ip');
        $sut->checkAnswer(array('ip'));
    }

    public function testCheckAnswer3()
    {
        $sut = new RecaptchaProvider($this->buzz, 'pubkey', 'privkey');

        $this->setExpectedException('Looptribe\Paytoshi\Captcha\CaptchaProviderException', 'Missing captcha response');
        $sut->checkAnswer(array('ip' => '10.10.10.10'));
    }

    public function testCheckAnswer4()
    {
        $sut = new RecaptchaProvider($this->buzz, 'pubkey', 'privkey');

        $this->setExpectedException('Looptribe\Paytoshi\Captcha\CaptchaProviderException', 'Missing captcha response');
        $sut->checkAnswer(array('ip' => '10.10.10.10', 'response'));
    }

    public function testCheckAnswer5()
    {
        $headers = array(
            'Connection' => 'Close',
        );
        $privkey = 'privkey';
        $pubkey = 'pubkey';
        $resp = 'resp';
        $ip = '10.10.10.10';
        $data = sprintf('secret=%s&response=%s&remoteip=%s', $privkey, $resp, $ip);

        $this->buzz->method('post')
            ->with(
                'https://www.google.com/recaptcha/api/siteverify',
                $headers,
                $data
            )
            ->willThrowException(new \Exception('message'));

        $sut = new RecaptchaProvider($this->buzz, $pubkey, $privkey);

        $this->setExpectedException('Looptribe\Paytoshi\Captcha\CaptchaProviderException', 'Failed to send captcha: message');
        $sut->checkAnswer(array('ip' => $ip, 'response' => $resp));
    }

    public function testCheckAnswer6()
    {
        $headers = array(
            'Connection' => 'Close',
        );
        $privkey = 'privkey';
        $pubkey = 'pubkey';
        $resp = 'resp';
        $ip = '10.10.10.10';
        $data = sprintf('secret=%s&response=%s&remoteip=%s', $privkey, $resp, $ip);

        $response = $this->getMock('Buzz\Message\Response');

        $response->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(false);

        $this->buzz->method('post')
            ->with(
                'https://www.google.com/recaptcha/api/siteverify',
                $headers,
                $data
            )
            ->willReturn($response);

        $sut = new RecaptchaProvider($this->buzz, $pubkey, $privkey);

        $this->setExpectedException('Looptribe\Paytoshi\Captcha\CaptchaProviderException', 'Captcha response error:');
        $sut->checkAnswer(array('ip' => $ip, 'response' => $resp));
    }

    public function testCheckAnswer7()
    {
        $headers = array(
            'Connection' => 'Close',
        );
        $privkey = 'privkey';
        $pubkey = 'pubkey';
        $resp = 'resp';
        $ip = '10.10.10.10';
        $data = sprintf('secret=%s&response=%s&remoteip=%s', $privkey, $resp, $ip);

        $response = $this->getMock('Buzz\Message\Response');

        $response->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(true);

        $response->expects($this->once())
            ->method('getContent')
            ->willReturn(null);

        $this->buzz->method('post')
            ->with(
                'https://www.google.com/recaptcha/api/siteverify',
                $headers,
                $data
            )
            ->willReturn($response);

        $sut = new RecaptchaProvider($this->buzz, $pubkey, $privkey);

        $this->setExpectedException('Looptribe\Paytoshi\Captcha\CaptchaProviderException', 'Invalid captcha response error');
        $sut->checkAnswer(array('ip' => $ip, 'response' => $resp));
    }

    public function testCheckAnswer8()
    {
        $headers = array(
            'Connection' => 'Close',
        );
        $privkey = 'privkey';
        $pubkey = 'pubkey';
        $resp = 'resp';
        $ip = '10.10.10.10';
        $data = sprintf('secret=%s&response=%s&remoteip=%s', $privkey, $resp, $ip);

        $response = $this->getMock('Buzz\Message\Response');

        $response->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(true);

        $response->expects($this->once())
            ->method('getContent')
            ->willReturn('{}');

        $this->buzz->method('post')
            ->with(
                'https://www.google.com/recaptcha/api/siteverify',
                $headers,
                $data
            )
            ->willReturn($response);

        $sut = new RecaptchaProvider($this->buzz, $pubkey, $privkey);

        $answer = $sut->checkAnswer(array('ip' => $ip, 'response' => $resp));

        $this->assertInstanceOf('Looptribe\Paytoshi\Captcha\CaptchaProviderResponse', $answer);
        $this->assertFalse($answer->isSuccessful());
        $this->assertEquals('Invalid Captcha', $answer->getMessage());
    }

    public function testCheckAnswer9()
    {
        $headers = array(
            'Connection' => 'Close',
        );
        $privkey = 'privkey';
        $pubkey = 'pubkey';
        $resp = 'resp';
        $ip = '10.10.10.10';
        $data = sprintf('secret=%s&response=%s&remoteip=%s', $privkey, $resp, $ip);

        $response = $this->getMock('Buzz\Message\Response');

        $response->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(true);

        $response->expects($this->once())
            ->method('getContent')
            ->willReturn('{ "success": false}');

        $this->buzz->method('post')
            ->with(
                'https://www.google.com/recaptcha/api/siteverify',
                $headers,
                $data
            )
            ->willReturn($response);

        $sut = new RecaptchaProvider($this->buzz, $pubkey, $privkey);

        $answer = $sut->checkAnswer(array('ip' => $ip, 'response' => $resp));

        $this->assertInstanceOf('Looptribe\Paytoshi\Captcha\CaptchaProviderResponse', $answer);
        $this->assertFalse($answer->isSuccessful());
        $this->assertEquals('Invalid Captcha', $answer->getMessage());
    }
    public function testCheckAnswer10()
    {
        $headers = array(
            'Connection' => 'Close',
        );
        $privkey = 'privkey';
        $pubkey = 'pubkey';
        $resp = 'resp';
        $ip = '10.10.10.10';
        $data = sprintf('secret=%s&response=%s&remoteip=%s', $privkey, $resp, $ip);

        $response = $this->getMock('Buzz\Message\Response');

        $response->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(true);

        $response->expects($this->once())
            ->method('getContent')
            ->willReturn('{ "success": true}');

        $this->buzz->method('post')
            ->with(
                'https://www.google.com/recaptcha/api/siteverify',
                $headers,
                $data
            )
            ->willReturn($response);

        $sut = new RecaptchaProvider($this->buzz, $pubkey, $privkey);

        $answer = $sut->checkAnswer(array('ip' => $ip, 'response' => $resp));

        $this->assertInstanceOf('Looptribe\Paytoshi\Captcha\CaptchaProviderResponse', $answer);
        $this->assertTrue($answer->isSuccessful());
        $this->assertNull($answer->getMessage());
    }

    public function testGetChallengeName()
    {
        $sut = new RecaptchaProvider($this->buzz, 'pubkey', 'privkey');
        $challenge = $sut->getChallengeName();
        $this->assertEquals('', $challenge);
    }

    public function testGetResponseName()
    {
        $sut = new RecaptchaProvider($this->buzz, 'pubkey', 'privkey');
        $response = $sut->getResponseName();
        $this->assertEquals('g-recaptcha-response', $response);
    }
}
