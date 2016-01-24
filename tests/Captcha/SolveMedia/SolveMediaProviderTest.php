<?php

namespace Looptribe\Paytoshi\Tests\Captcha\SolveMedia;

use Looptribe\Paytoshi\Captcha\CaptchaProviderException;
use Looptribe\Paytoshi\Captcha\SolveMedia\SolveMediaProvider;

class SolveMediaProviderTest extends \PHPUnit_Framework_TestCase
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
        $sut = new SolveMediaProvider($this->buzz, 'pubkey', 'privkey', 'verkey');

        $this->setExpectedException('Looptribe\Paytoshi\Captcha\CaptchaProviderException', 'Missing captcha ip');
        $sut->checkAnswer(array());
    }

    public function testCheckAnswer2()
    {
        $sut = new SolveMediaProvider($this->buzz, 'pubkey', 'privkey', 'verkey');

        $this->setExpectedException('Looptribe\Paytoshi\Captcha\CaptchaProviderException', 'Missing captcha ip');
        $sut->checkAnswer(array('ip'));
    }

    public function testCheckAnswer3()
    {
        $sut = new SolveMediaProvider($this->buzz, 'pubkey', 'privkey', 'verkey');

        $this->setExpectedException('Looptribe\Paytoshi\Captcha\CaptchaProviderException', 'Missing captcha challenge');
        $sut->checkAnswer(array('ip' => '10.10.10.10'));
    }

    public function testCheckAnswer4()
    {
        $sut = new SolveMediaProvider($this->buzz, 'pubkey', 'privkey', 'verkey');

        $this->setExpectedException('Looptribe\Paytoshi\Captcha\CaptchaProviderException', 'Missing captcha challenge');
        $sut->checkAnswer(array('ip' => '10.10.10.10', 'challenge'));
    }

    public function testCheckAnswer5()
    {
        $sut = new SolveMediaProvider($this->buzz, 'pubkey', 'privkey', 'verkey');

        $this->setExpectedException('Looptribe\Paytoshi\Captcha\CaptchaProviderException', 'Missing captcha response');
        $sut->checkAnswer(array('ip' => '10.10.10.10', 'challenge' => 'challenge'));
    }

    public function testCheckAnswer6()
    {
        $sut = new SolveMediaProvider($this->buzz, 'pubkey', 'privkey', 'verkey');

        $this->setExpectedException('Looptribe\Paytoshi\Captcha\CaptchaProviderException', 'Missing captcha response');
        $sut->checkAnswer(array('ip' => '10.10.10.10', 'challenge' => 'challenge', 'response'));
    }

    public function testCheckAnswer7()
    {
        $headers = array(
            'User-Agent' => 'solvemedia/PHP',
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Connection' => 'Close',
        );
        $privkey = 'privkey';
        $pubkey = 'pubkey';
        $verkey = 'verkey';
        $challenge = 'challenge';
        $resp = 'resp';
        $ip = '10.10.10.10';
        $data = sprintf('privatekey=%s&remoteip=%s&challenge=%s&response=%s', $privkey, $ip, $challenge, $resp);

        $this->buzz->method('post')
            ->with(
                'http://verify.solvemedia.com/papi/verify',
                $headers,
                $data
            )
            ->willThrowException(new CaptchaProviderException('message'));

        $sut = new SolveMediaProvider($this->buzz, $pubkey, $privkey, $verkey);

        $this->setExpectedException('Looptribe\Paytoshi\Captcha\CaptchaProviderException', 'Failed to send captcha: message');
        $sut->checkAnswer(array('ip' => $ip, 'challenge' => $challenge, 'response' => $resp));
    }

    public function testCheckAnswer8()
    {
        $headers = array(
            'User-Agent' => 'solvemedia/PHP',
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Connection' => 'Close',
        );
        $privkey = 'privkey';
        $pubkey = 'pubkey';
        $verkey = 'verkey';
        $challenge = 'challenge';
        $resp = 'resp';
        $ip = '10.10.10.10';
        $data = sprintf('privatekey=%s&remoteip=%s&challenge=%s&response=%s', $privkey, $ip, $challenge, $resp);

        $response = $this->getMock('Buzz\Message\Response');

        $response->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(false);

        $this->buzz->method('post')
            ->with(
                'http://verify.solvemedia.com/papi/verify',
                $headers,
                $data
            )
            ->willReturn($response);

        $sut = new SolveMediaProvider($this->buzz, $pubkey, $privkey, $verkey);

        $this->setExpectedException('Looptribe\Paytoshi\Captcha\CaptchaProviderException', 'Captcha response error:');
        $sut->checkAnswer(array('ip' => $ip, 'challenge' => $challenge, 'response' => $resp));
    }

    public function testCheckAnswer9()
    {
        $headers = array(
            'User-Agent' => 'solvemedia/PHP',
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Connection' => 'Close',
        );
        $privkey = 'privkey';
        $pubkey = 'pubkey';
        $verkey = 'verkey';
        $challenge = 'challenge';
        $resp = 'resp';
        $ip = '10.10.10.10';
        $data = sprintf('privatekey=%s&remoteip=%s&challenge=%s&response=%s', $privkey, $ip, $challenge, $resp);

        $response = $this->getMock('Buzz\Message\Response');

        $response->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(true);

        $response->expects($this->once())
            ->method('getContent')
            ->willReturn(null);

        $this->buzz->method('post')
            ->with(
                'http://verify.solvemedia.com/papi/verify',
                $headers,
                $data
            )
            ->willReturn($response);

        $sut = new SolveMediaProvider($this->buzz, $pubkey, $privkey, $verkey);

        $this->setExpectedException('Looptribe\Paytoshi\Captcha\CaptchaProviderException', 'Invalid captcha response error');
        $sut->checkAnswer(array('ip' => $ip, 'challenge' => $challenge, 'response' => $resp));
    }

    public function testCheckAnswer10()
    {
        $headers = array(
            'User-Agent' => 'solvemedia/PHP',
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Connection' => 'Close',
        );
        $privkey = 'privkey';
        $pubkey = 'pubkey';
        $verkey = 'verkey';
        $challenge = 'challenge';
        $resp = 'resp';
        $ip = '10.10.10.10';
        $data = sprintf('privatekey=%s&remoteip=%s&challenge=%s&response=%s', $privkey, $ip, $challenge, $resp);

        $response = $this->getMock('Buzz\Message\Response');

        $response->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(true);

        $response->expects($this->once())
            ->method('getContent')
            ->willReturn('content');

        $this->buzz->method('post')
            ->with(
                'http://verify.solvemedia.com/papi/verify',
                $headers,
                $data
            )
            ->willReturn($response);

        $sut = new SolveMediaProvider($this->buzz, $pubkey, $privkey, $verkey);

        $answer = $sut->checkAnswer(array('ip' => $ip, 'challenge' => $challenge, 'response' => $resp));
        $this->assertInstanceOf('Looptribe\Paytoshi\Captcha\CaptchaProviderResponse', $answer);
        $this->assertFalse($answer->isSuccessful());
        $this->assertSame('Invalid Captcha', $answer->getMessage());
    }

    public function testCheckAnswer11()
    {
        $headers = array(
            'User-Agent' => 'solvemedia/PHP',
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Connection' => 'Close',
        );
        $privkey = 'privkey';
        $pubkey = 'pubkey';
        $verkey = 'verkey';
        $challenge = 'challenge';
        $resp = 'resp';
        $ip = '10.10.10.10';
        $data = sprintf('privatekey=%s&remoteip=%s&challenge=%s&response=%s', $privkey, $ip, $challenge, $resp);

        $response = $this->getMock('Buzz\Message\Response');

        $response->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(true);

        $response->expects($this->once())
            ->method('getContent')
            ->willReturn('false');

        $this->buzz->method('post')
            ->with(
                'http://verify.solvemedia.com/papi/verify',
                $headers,
                $data
            )
            ->willReturn($response);

        $sut = new SolveMediaProvider($this->buzz, $pubkey, $privkey, $verkey);

        $answer = $sut->checkAnswer(array('ip' => $ip, 'challenge' => $challenge, 'response' => $resp));
        $this->assertInstanceOf('Looptribe\Paytoshi\Captcha\CaptchaProviderResponse', $answer);
        $this->assertFalse($answer->isSuccessful());
        $this->assertSame('Invalid Captcha', $answer->getMessage());
    }

    public function testCheckAnswer12()
    {
        $headers = array(
            'User-Agent' => 'solvemedia/PHP',
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Connection' => 'Close',
        );
        $privkey = 'privkey';
        $pubkey = 'pubkey';
        $verkey = 'verkey';
        $challenge = 'challenge';
        $resp = 'resp';
        $ip = '10.10.10.10';
        $data = sprintf('privatekey=%s&remoteip=%s&challenge=%s&response=%s', $privkey, $ip, $challenge, $resp);

        $response = $this->getMock('Buzz\Message\Response');

        $response->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(true);

        $hash = 'badhash';

        $response->expects($this->once())
            ->method('getContent')
            ->willReturn('true' . PHP_EOL . ' ' . PHP_EOL . $hash);

        $this->buzz->method('post')
            ->with(
                'http://verify.solvemedia.com/papi/verify',
                $headers,
                $data
            )
            ->willReturn($response);

        $sut = new SolveMediaProvider($this->buzz, $pubkey, $privkey, $verkey);

        $answer = $sut->checkAnswer(array('ip' => $ip, 'challenge' => $challenge, 'response' => $resp));
        $this->assertInstanceOf('Looptribe\Paytoshi\Captcha\CaptchaProviderResponse', $answer);
        $this->assertFalse($answer->isSuccessful());
        $this->assertSame('Invalid Captcha verification', $answer->getMessage());
    }

    public function testCheckAnswer13()
    {
        $headers = array(
            'User-Agent' => 'solvemedia/PHP',
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Connection' => 'Close',
        );
        $privkey = 'privkey';
        $pubkey = 'pubkey';
        $verkey = 'verkey';
        $challenge = 'challenge';
        $resp = 'resp';
        $ip = '10.10.10.10';
        $data = sprintf('privatekey=%s&remoteip=%s&challenge=%s&response=%s', $privkey, $ip, $challenge, $resp);

        $response = $this->getMock('Buzz\Message\Response');

        $response->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(true);

        $hash = sha1('true' . $challenge . $verkey);

        $response->expects($this->once())
            ->method('getContent')
            ->willReturn('true' . PHP_EOL . ' ' . PHP_EOL . $hash);

        $this->buzz->method('post')
            ->with(
                'http://verify.solvemedia.com/papi/verify',
                $headers,
                $data
            )
            ->willReturn($response);

        $sut = new SolveMediaProvider($this->buzz, $pubkey, $privkey, $verkey);

        $answer = $sut->checkAnswer(array('ip' => $ip, 'challenge' => $challenge, 'response' => $resp));
        $this->assertInstanceOf('Looptribe\Paytoshi\Captcha\CaptchaProviderResponse', $answer);
        $this->assertTrue($answer->isSuccessful());
        $this->assertNull($answer->getMessage());
    }

    /*
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

        $sut = new SolveMediaProvider($this->buzz, 'pubkey', 'privkey', 'verkey');

        $this->setExpectedException('Looptribe\Paytoshi\Captcha\CaptchaProviderException', 'Invalid captcha response error');
        $sut->checkAnswer(array('ip' => '10.10.10.10', 'response' => $resp));
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

        $sut = new SolveMediaProvider($this->buzz, 'pubkey', 'privkey', 'verkey');

        $answer = $sut->checkAnswer(array('ip' => '10.10.10.10', 'response' => $resp));

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

        $sut = new SolveMediaProvider($this->buzz, 'pubkey', 'privkey', 'verkey');

        $answer = $sut->checkAnswer(array('ip' => '10.10.10.10', 'response' => $resp));

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

        $sut = new SolveMediaProvider($this->buzz, 'pubkey', 'privkey', 'verkey');

        $answer = $sut->checkAnswer(array('ip' => '10.10.10.10', 'response' => $resp));

        $this->assertInstanceOf('Looptribe\Paytoshi\Captcha\CaptchaProviderResponse', $answer);
        $this->assertTrue($answer->isSuccessful());
        $this->assertNull($answer->getMessage());
    }*/

    public function testGetChallengeName()
    {
        $sut = new SolveMediaProvider($this->buzz, 'pubkey', 'privkey', 'verkey');
        $challenge = $sut->getChallengeName();
        $this->assertEquals('adcopy_challenge', $challenge);
    }

    public function testGetResponseName()
    {
        $sut = new SolveMediaProvider($this->buzz, 'pubkey', 'privkey', 'verkey');
        $response = $sut->getResponseName();
        $this->assertEquals('adcopy_response', $response);
    }
}
