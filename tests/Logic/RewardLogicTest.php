<?php

namespace Looptribe\Paytoshi\Tests\Logic;

use Looptribe\Paytoshi\Captcha\CaptchaProviderException;
use Looptribe\Paytoshi\Logic\RewardLogic;
use Looptribe\Paytoshi\Model\Recipient;

class RewardLogicTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $connection;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $recipientRepository;
    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $rewardProvider;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $api;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $intervalEnforcer;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $captchaProvider;

    public function setUp()
    {
        $this->connection = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();
        $this->recipientRepository = $this->getMockBuilder('Looptribe\Paytoshi\Model\RecipientRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $this->rewardProvider = $this->getMock('Looptribe\Paytoshi\Logic\RewardProviderInterface');
        $this->api = $this->getMock('Looptribe\Paytoshi\Api\PaytoshiApiInterface');
        $this->intervalEnforcer = $this->getMock('Looptribe\Paytoshi\Logic\IntervalEnforcerInterface');
        $this->captchaProvider = $this->getMock('Looptribe\Paytoshi\Captcha\CaptchaProviderInterface');
    }

    public function testCreate1()
    {
        $address = 'addr1';
        $ip = '10.10.10.10';
        $challenge = 'challenge';
        $response = 'response';

        $this->captchaProvider->expects($this->once())
            ->method('checkAnswer')
            ->with(array(
                'challenge' => $challenge,
                'response' => $response,
                'ip' => $ip
            ))
            ->willThrowException(new CaptchaProviderException('Missing captcha response'));

        $this->connection
            ->expects($this->never())
            ->method('commit');

        $sut = new RewardLogic($this->connection, $this->recipientRepository, $this->rewardProvider, $this->api, $this->intervalEnforcer, $this->captchaProvider);
        $this->setExpectedException('\Exception', 'Captcha error: Missing captcha response');
        $payout = $sut->create($address, $ip, $challenge, $response);
    }

    public function testCreate2()
    {
        $address = 'addr1';
        $ip = '10.10.10.10';
        $challenge = 'challenge';
        $response = 'response';

        $this->captchaProvider->expects($this->once())
            ->method('checkAnswer')
            ->with(array(
                'challenge' => $challenge,
                'response' => $response,
                'ip' => $ip
            ))
            ->willThrowException(new CaptchaProviderException('Failed to send captcha'));

        $this->connection
            ->expects($this->never())
            ->method('commit');

        $sut = new RewardLogic($this->connection, $this->recipientRepository, $this->rewardProvider, $this->api, $this->intervalEnforcer, $this->captchaProvider);
        $this->setExpectedException('\Exception', 'Captcha error: Failed to send captcha');
        $payout = $sut->create($address, $ip, $challenge, $response);
    }

    public function testCreate3()
    {
        $address = 'addr1';
        $ip = '10.10.10.10';
        $challenge = 'challenge';
        $response = 'response';

        $captchaResponse = $this->getMockBuilder('Looptribe\Paytoshi\Captcha\CaptchaProviderResponse')
            ->disableOriginalConstructor()
            ->getMock();

        $this->captchaProvider->expects($this->once())
            ->method('checkAnswer')
            ->with(array(
                'challenge' => $challenge,
                'response' => $response,
                'ip' => $ip
            ))
            ->willReturn($captchaResponse);

        $captchaResponse->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(false);

        $captchaResponse->expects($this->once())
            ->method('getMessage')
            ->willReturn('Invalid Captcha');

        $this->connection
            ->expects($this->never())
            ->method('commit');

        $sut = new RewardLogic($this->connection, $this->recipientRepository, $this->rewardProvider, $this->api, $this->intervalEnforcer, $this->captchaProvider);
        $this->setExpectedException('\Exception', 'Invalid Captcha');
        $payout = $sut->create($address, $ip, $challenge, $response);
    }

    public function testCreate4()
    {
        $address = 'addr1';
        $ip = '10.10.10.10';
        $challenge = 'challenge';
        $response = 'response';

        $captchaResponse = $this->getMockBuilder('Looptribe\Paytoshi\Captcha\CaptchaProviderResponse')
            ->disableOriginalConstructor()
            ->getMock();

        $this->captchaProvider->expects($this->once())
            ->method('checkAnswer')
            ->with(array(
                'challenge' => $challenge,
                'response' => $response,
                'ip' => $ip
            ))
            ->willReturn($captchaResponse);

        $captchaResponse->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(true);

        $this->connection
            ->expects($this->once())
            ->method('beginTransaction');

        $this->recipientRepository->expects($this->once())
            ->method('findOneByAddress')
            ->with($address)
            ->willReturn(null);

        $recipient = new Recipient();
        $recipient->setAddress($address);

        $this->intervalEnforcer->expects($this->once())
            ->method('check')
            ->with($ip, $recipient)
            ->willReturn(new \DateInterval('PT60S'));

        $this->connection
            ->expects($this->once())
            ->method('rollBack');

        $this->connection
            ->expects($this->never())
            ->method('commit');

        $sut = new RewardLogic($this->connection, $this->recipientRepository, $this->rewardProvider, $this->api, $this->intervalEnforcer, $this->captchaProvider);
        $this->setExpectedException('\Exception', 'You can get a reward again in');
        $payout = $sut->create($address, $ip, $challenge, $response);
    }

    public function testCreate5()
    {
        $address = 'addr1';
        $ip = '10.10.10.10';
        $challenge = 'challenge';
        $response = 'response';

        $captchaResponse = $this->getMockBuilder('Looptribe\Paytoshi\Captcha\CaptchaProviderResponse')
            ->disableOriginalConstructor()
            ->getMock();

        $this->captchaProvider->expects($this->once())
            ->method('checkAnswer')
            ->with(array(
                'challenge' => $challenge,
                'response' => $response,
                'ip' => $ip
            ))
            ->willReturn($captchaResponse);

        $captchaResponse->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(true);

        $this->connection
            ->expects($this->once())
            ->method('beginTransaction');

        $recipient = new Recipient();
        $recipient->setId('1');
        $recipient->setAddress($address);

        $this->recipientRepository->expects($this->once())
            ->method('findOneByAddress')
            ->with($address)
            ->willReturn($recipient);

        $this->intervalEnforcer->expects($this->once())
            ->method('check')
            ->with($ip, $recipient)
            ->willThrowException(new \Exception('Invalid waiting interval'));

        $this->connection
            ->expects($this->once())
            ->method('rollBack');

        $this->connection
            ->expects($this->never())
            ->method('commit');

        $sut = new RewardLogic($this->connection, $this->recipientRepository, $this->rewardProvider, $this->api, $this->intervalEnforcer, $this->captchaProvider);
        $this->setExpectedException('\Exception', 'Invalid waiting interval');
        $payout = $sut->create($address, $ip, $challenge, $response);
    }

    public function testCreate6()
    {
        $address = 'addr1';
        $ip = '10.10.10.10';
        $challenge = 'challenge';
        $response = 'response';

        $captchaResponse = $this->getMockBuilder('Looptribe\Paytoshi\Captcha\CaptchaProviderResponse')
            ->disableOriginalConstructor()
            ->getMock();

        $this->captchaProvider->expects($this->once())
            ->method('checkAnswer')
            ->with(array(
                'challenge' => $challenge,
                'response' => $response,
                'ip' => $ip
            ))
            ->willReturn($captchaResponse);

        $captchaResponse->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(true);

        $this->connection
            ->expects($this->once())
            ->method('beginTransaction');

        $recipient = new Recipient();
        $recipient->setId('1');
        $recipient->setAddress($address);

        $this->recipientRepository->expects($this->once())
            ->method('findOneByAddress')
            ->with($address)
            ->willReturn($recipient);

        $this->intervalEnforcer->expects($this->once())
            ->method('check')
            ->with($ip, $recipient)
            ->willReturn(null);

        $this->rewardProvider->expects($this->once())
            ->method('getReward')
            ->willReturn(10);

        $this->connection
            ->expects($this->never())
            ->method('rollBack');

        $this->connection
            ->expects($this->once())
            ->method('commit');

        $sut = new RewardLogic($this->connection, $this->recipientRepository, $this->rewardProvider, $this->api, $this->intervalEnforcer, $this->captchaProvider);
        $payout = $sut->create($address, $ip, $challenge, $response);
        
        $this->assertSame($ip, $payout->getIp());
        $this->assertSame($address, $payout->getRecipientAddress());
        $this->assertSame(10, $payout->getEarning());
    }
}
