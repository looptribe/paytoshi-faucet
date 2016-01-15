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
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $resultRepository;
    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $rewardProvider;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $api;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $intervalEnforcer;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $captchaProvider;
    /** @var string */
    private $apikey;

    public function setUp()
    {
        $this->connection = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();
        $this->recipientRepository = $this->getMockBuilder('Looptribe\Paytoshi\Model\RecipientRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $this->payoutRepository = $this->getMockBuilder('Looptribe\Paytoshi\Model\PayoutRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $this->rewardProvider = $this->getMock('Looptribe\Paytoshi\Logic\RewardProviderInterface');
        $this->api = $this->getMock('Looptribe\Paytoshi\Api\PaytoshiApiInterface');
        $this->intervalEnforcer = $this->getMock('Looptribe\Paytoshi\Logic\IntervalEnforcerInterface');
        $this->captchaProvider = $this->getMock('Looptribe\Paytoshi\Captcha\CaptchaProviderInterface');
        $this->apikey = 'apikey';
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

        $sut = new RewardLogic($this->connection, $this->recipientRepository, $this->payoutRepository, $this->rewardProvider, $this->api, $this->intervalEnforcer, $this->captchaProvider, $this->apikey);
        $result = $sut->create($address, $ip, $challenge, $response);
        $this->assertInstanceOf('Looptribe\Paytoshi\Logic\RewardLogicResult', $result);
        $this->assertSame(false, $result->isSuccessful());
        $this->assertSame('danger', $result->getSeverity());
        $this->assertSame('Missing captcha response', $result->getError());
        $this->assertNull($result->getResponse());
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

        $sut = new RewardLogic($this->connection, $this->recipientRepository, $this->payoutRepository, $this->rewardProvider, $this->api, $this->intervalEnforcer, $this->captchaProvider, $this->apikey);
        $result = $sut->create($address, $ip, $challenge, $response);$this->assertInstanceOf('Looptribe\Paytoshi\Logic\RewardLogicResult', $result);
        $this->assertSame(false, $result->isSuccessful());
        $this->assertSame('warning', $result->getSeverity());
        $this->assertSame('Invalid Captcha', $result->getError());
        $this->assertNull($result->getResponse());
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

        $sut = new RewardLogic($this->connection, $this->recipientRepository, $this->payoutRepository, $this->rewardProvider, $this->api, $this->intervalEnforcer, $this->captchaProvider, $this->apikey);
        $result = $sut->create($address, $ip, $challenge, $response);
        $this->assertSame(false, $result->isSuccessful());
        $this->assertSame('warning', $result->getSeverity());
        $this->assertSame('You can get a reward again in 00h, 00m, 60s.', $result->getError());
        $this->assertNull($result->getResponse());
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

        $sut = new RewardLogic($this->connection, $this->recipientRepository, $this->payoutRepository, $this->rewardProvider, $this->api, $this->intervalEnforcer, $this->captchaProvider, $this->apikey);
        $result = $sut->create($address, $ip, $challenge, $response);
        $this->assertSame(false, $result->isSuccessful());
        $this->assertSame('danger', $result->getSeverity());
        $this->assertSame('Invalid waiting interval', $result->getError());
        $this->assertNull($result->getResponse());
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

        $this->api->expects($this->once())
            ->method('send')
            ->with($this->apikey, $address, 10, $ip)
            ->willThrowException(new \Exception('message'));

        $this->connection
            ->expects($this->once())
            ->method('rollBack');

        $this->connection
            ->expects($this->never())
            ->method('commit');

        $sut = new RewardLogic($this->connection, $this->recipientRepository, $this->payoutRepository, $this->rewardProvider, $this->api, $this->intervalEnforcer, $this->captchaProvider, $this->apikey);
        $result = $sut->create($address, $ip, $challenge, $response);
        $this->assertSame(false, $result->isSuccessful());
        $this->assertSame('danger', $result->getSeverity());
        $this->assertSame('Unable to create reward: message', $result->getError());
        $this->assertNull($result->getResponse());
    }

    public function testCreate7()
    {
        $address = 'addr1';
        $ip = '10.10.10.10';
        $challenge = 'challenge';
        $response = 'response';

        $captchaResponse = $this->getMockBuilder('Looptribe\Paytoshi\Captcha\CaptchaProviderResponse')
            ->disableOriginalConstructor()
            ->getMock();

        $response = $this->getMockBuilder('Looptribe\Paytoshi\Api\Response\FaucetSendResponse')
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

        $this->api->expects($this->once())
            ->method('send')
            ->with($this->apikey, $address, 10, $ip)
            ->willReturn($response);

        $response->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(false);

        $response->expects($this->once())
            ->method('getError')
            ->willReturn('Timeout');

        $this->connection
            ->expects($this->once())
            ->method('rollBack');

        $this->connection
            ->expects($this->never())
            ->method('commit');

        $sut = new RewardLogic($this->connection, $this->recipientRepository, $this->payoutRepository, $this->rewardProvider, $this->api, $this->intervalEnforcer, $this->captchaProvider, $this->apikey);
        $result = $sut->create($address, $ip, $challenge, $response);
        $this->assertSame(false, $result->isSuccessful());
        $this->assertSame('danger', $result->getSeverity());
        $this->assertSame('Unable to create reward: Timeout', $result->getError());
        $this->assertNull($result->getResponse());
    }

    public function testCreate8()
    {
        $address = 'addr1';
        $ip = '10.10.10.10';
        $challenge = 'challenge';
        $response = 'response';

        $captchaResponse = $this->getMockBuilder('Looptribe\Paytoshi\Captcha\CaptchaProviderResponse')
            ->disableOriginalConstructor()
            ->getMock();

        $response = $this->getMockBuilder('Looptribe\Paytoshi\Api\Response\FaucetSendResponse')
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

        $this->api->expects($this->once())
            ->method('send')
            ->with($this->apikey, $address, 10, $ip)
            ->willReturn($response);

        $response->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(true);

        $this->payoutRepository->expects($this->once())
            ->method('insert');

        $this->connection
            ->expects($this->never())
            ->method('rollBack');

        $this->connection
            ->expects($this->once())
            ->method('commit');

        $sut = new RewardLogic($this->connection, $this->recipientRepository, $this->payoutRepository, $this->rewardProvider, $this->api, $this->intervalEnforcer, $this->captchaProvider, $this->apikey);
        $result = $sut->create($address, $ip, $challenge, $response);
        $this->assertSame(true, $result->isSuccessful());
        $this->assertSame(null, $result->getSeverity());
        $this->assertSame(null, $result->getError());
        $this->assertInstanceOf('Looptribe\Paytoshi\Api\Response\FaucetSendResponse', $result->getResponse());
    }
}
