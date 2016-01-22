<?php

namespace Looptribe\Paytoshi\Tests\Logic;

use Looptribe\Paytoshi\Captcha\CaptchaProviderException;
use Looptribe\Paytoshi\Logic\RewardLogic;

class RewardLogicTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $connection;
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
    /** @var int|float */
    private $referralPercentage;

    public function setUp()
    {
        $this->connection = $this->getMockBuilder('Doctrine\DBAL\Connection')
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
        $this->referralPercentage = '30';
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

        $sut = new RewardLogic($this->connection, $this->payoutRepository, $this->rewardProvider, $this->api, $this->intervalEnforcer, $this->captchaProvider, $this->apikey, $this->referralPercentage);
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

        $sut = new RewardLogic($this->connection, $this->payoutRepository, $this->rewardProvider, $this->api, $this->intervalEnforcer, $this->captchaProvider, $this->apikey, $this->referralPercentage);
        $result = $sut->create($address, $ip, $challenge, $response)
        ;$this->assertInstanceOf('Looptribe\Paytoshi\Logic\RewardLogicResult', $result);
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

        $this->intervalEnforcer->expects($this->once())
            ->method('check')
            ->with($ip, $address)
            ->willReturn(new \DateInterval('PT60S'));

        $this->connection
            ->expects($this->once())
            ->method('rollBack');

        $this->connection
            ->expects($this->never())
            ->method('commit');

        $sut = new RewardLogic($this->connection, $this->payoutRepository, $this->rewardProvider, $this->api, $this->intervalEnforcer, $this->captchaProvider, $this->apikey, $this->referralPercentage);
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

        $this->intervalEnforcer->expects($this->once())
            ->method('check')
            ->with($ip, $address)
            ->willThrowException(new \Exception('Invalid waiting interval'));

        $this->connection
            ->expects($this->once())
            ->method('rollBack');

        $this->connection
            ->expects($this->never())
            ->method('commit');

        $sut = new RewardLogic($this->connection, $this->payoutRepository, $this->rewardProvider, $this->api, $this->intervalEnforcer, $this->captchaProvider, $this->apikey, $this->referralPercentage);
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

        $this->intervalEnforcer->expects($this->once())
            ->method('check')
            ->with($ip, $address)
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

        $sut = new RewardLogic($this->connection, $this->payoutRepository, $this->rewardProvider, $this->api, $this->intervalEnforcer, $this->captchaProvider, $this->apikey, $this->referralPercentage);
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

        $apiResponse = $this->getMockBuilder('Looptribe\Paytoshi\Api\Response\FaucetSendResponse')
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

        $this->intervalEnforcer->expects($this->once())
            ->method('check')
            ->with($ip, $address)
            ->willReturn(null);

        $this->rewardProvider->expects($this->once())
            ->method('getReward')
            ->willReturn(10);

        $this->api->expects($this->once())
            ->method('send')
            ->with($this->apikey, $address, 10, $ip)
            ->willReturn($apiResponse);

        $apiResponse->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(false);

        $apiResponse->expects($this->once())
            ->method('getError')
            ->willReturn('Timeout');

        $this->connection
            ->expects($this->once())
            ->method('rollBack');

        $this->connection
            ->expects($this->never())
            ->method('commit');

        $sut = new RewardLogic($this->connection, $this->payoutRepository, $this->rewardProvider, $this->api, $this->intervalEnforcer, $this->captchaProvider, $this->apikey, $this->referralPercentage);
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

        $apiResponse = $this->getMockBuilder('Looptribe\Paytoshi\Api\Response\FaucetSendResponse')
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

        $this->intervalEnforcer->expects($this->once())
            ->method('check')
            ->with($ip, $address)
            ->willReturn(null);

        $this->rewardProvider->expects($this->once())
            ->method('getReward')
            ->willReturn(10);

        $this->api->expects($this->once())
            ->method('send')
            ->with($this->apikey, $address, 10, $ip)
            ->willReturn($apiResponse);

        $apiResponse->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(true);

        $apiResponse->expects($this->once())
            ->method('getAmount')
            ->willReturn(10);

        $apiResponse->expects($this->once())
            ->method('getRecipient')
            ->willReturn($address);

        $this->payoutRepository->expects($this->once())
            ->method('insert');

        $this->connection
            ->expects($this->never())
            ->method('rollBack');

        $this->connection
            ->expects($this->once())
            ->method('commit');

        $sut = new RewardLogic($this->connection, $this->payoutRepository, $this->rewardProvider, $this->api, $this->intervalEnforcer, $this->captchaProvider, $this->apikey, $this->referralPercentage);
        $result = $sut->create($address, $ip, $challenge, $response);
        $this->assertSame(true, $result->isSuccessful());
        $this->assertSame(null, $result->getSeverity());
        $this->assertSame(null, $result->getError());
        $this->assertInstanceOf('Looptribe\Paytoshi\Api\Response\FaucetSendResponse', $result->getResponse());
        $this->assertSame(10, $result->getResponse()->getAmount());
        $this->assertSame($address, $result->getResponse()->getRecipient());
    }

    public function testCreate9()
    {
        $address = 'addr1';
        $ip = '10.10.10.10';
        $challenge = 'challenge';
        $response = 'response';

        $captchaResponse = $this->getMockBuilder('Looptribe\Paytoshi\Captcha\CaptchaProviderResponse')
            ->disableOriginalConstructor()
            ->getMock();

        $apiResponse = $this->getMockBuilder('Looptribe\Paytoshi\Api\Response\FaucetSendResponse')
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

        $this->intervalEnforcer->expects($this->once())
            ->method('check')
            ->with($ip, $address)
            ->willReturn(null);

        $this->rewardProvider->expects($this->once())
            ->method('getReward')
            ->willReturn(10);

        $this->api->expects($this->once())
            ->method('send')
            ->with($this->apikey, $address, 10, $ip)
            ->willReturn($apiResponse);

        $apiResponse->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(true);

        $apiResponse->expects($this->once())
            ->method('getAmount')
            ->willReturn(10);

        $apiResponse->expects($this->once())
            ->method('getRecipient')
            ->willReturn($address);

        $this->payoutRepository->expects($this->once())
            ->method('insert');

        $this->connection
            ->expects($this->never())
            ->method('rollBack');

        $this->connection
            ->expects($this->once())
            ->method('commit');

        $sut = new RewardLogic($this->connection, $this->payoutRepository, $this->rewardProvider, $this->api, $this->intervalEnforcer, $this->captchaProvider, $this->apikey, $this->referralPercentage);
        $result = $sut->create($address, $ip, $challenge, $response, '');
        $this->assertSame(true, $result->isSuccessful());
        $this->assertSame(null, $result->getSeverity());
        $this->assertSame(null, $result->getError());
        $this->assertInstanceOf('Looptribe\Paytoshi\Api\Response\FaucetSendResponse', $result->getResponse());
        $this->assertSame(10, $result->getResponse()->getAmount());
        $this->assertSame($address, $result->getResponse()->getRecipient());
    }

    public function testCreate10()
    {
        $address = 'addr1';
        $ip = '10.10.10.10';
        $challenge = 'challenge';
        $response = 'response';

        $captchaResponse = $this->getMockBuilder('Looptribe\Paytoshi\Captcha\CaptchaProviderResponse')
            ->disableOriginalConstructor()
            ->getMock();

        $apiResponse = $this->getMockBuilder('Looptribe\Paytoshi\Api\Response\FaucetSendResponse')
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

        $this->intervalEnforcer->expects($this->once())
            ->method('check')
            ->with($ip, $address)
            ->willReturn(null);

        $this->rewardProvider->expects($this->once())
            ->method('getReward')
            ->willReturn(10);

        $this->api->expects($this->once())
            ->method('send')
            ->with($this->apikey, $address, 10, $ip)
            ->willReturn($apiResponse);

        $apiResponse->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(true);

        $apiResponse->expects($this->once())
            ->method('getAmount')
            ->willReturn(10);

        $apiResponse->expects($this->once())
            ->method('getRecipient')
            ->willReturn($address);

        $this->payoutRepository->expects($this->once())
            ->method('insert');

        $this->connection
            ->expects($this->never())
            ->method('rollBack');

        $this->connection
            ->expects($this->once())
            ->method('commit');

        $sut = new RewardLogic($this->connection, $this->payoutRepository, $this->rewardProvider, $this->api, $this->intervalEnforcer, $this->captchaProvider, $this->apikey, 0);
        $result = $sut->create($address, $ip, $challenge, $response, $address);
        $this->assertSame(true, $result->isSuccessful());
        $this->assertSame(null, $result->getSeverity());
        $this->assertSame(null, $result->getError());
        $this->assertInstanceOf('Looptribe\Paytoshi\Api\Response\FaucetSendResponse', $result->getResponse());
        $this->assertSame(10, $result->getResponse()->getAmount());
        $this->assertSame($address, $result->getResponse()->getRecipient());
    }

    public function testCreate11()
    {
        $address = 'addr1';
        $ip = '10.10.10.10';
        $challenge = 'challenge';
        $response = 'response';
        $referralAddress = 'refaddr1';
        $referralPercentage = 0;

        $captchaResponse = $this->getMockBuilder('Looptribe\Paytoshi\Captcha\CaptchaProviderResponse')
            ->disableOriginalConstructor()
            ->getMock();

        $apiResponse = $this->getMockBuilder('Looptribe\Paytoshi\Api\Response\FaucetSendResponse')
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

        $this->intervalEnforcer->expects($this->once())
            ->method('check')
            ->with($ip, $address)
            ->willReturn(null);

        $this->rewardProvider->expects($this->once())
            ->method('getReward')
            ->willReturn(10);

        $this->api->expects($this->once())
            ->method('send')
            ->with($this->apikey, $address, 10, $ip)
            ->willReturn($apiResponse);

        $apiResponse->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(true);

        $apiResponse->expects($this->once())
            ->method('getAmount')
            ->willReturn(10);

        $apiResponse->expects($this->once())
            ->method('getRecipient')
            ->willReturn($address);

        $this->payoutRepository->expects($this->once())
            ->method('insert');

        $this->connection
            ->expects($this->never())
            ->method('rollBack');

        $this->connection
            ->expects($this->once())
            ->method('commit');

        $sut = new RewardLogic($this->connection, $this->payoutRepository, $this->rewardProvider, $this->api, $this->intervalEnforcer, $this->captchaProvider, $this->apikey, $referralPercentage);
        $result = $sut->create($address, $ip, $challenge, $response, $referralAddress);
        $this->assertSame(true, $result->isSuccessful());
        $this->assertSame(null, $result->getSeverity());
        $this->assertSame(null, $result->getError());
        $this->assertInstanceOf('Looptribe\Paytoshi\Api\Response\FaucetSendResponse', $result->getResponse());
        $this->assertSame(10, $result->getResponse()->getAmount());
        $this->assertSame($address, $result->getResponse()->getRecipient());
    }

    public function testCreate12()
    {
        $address = 'addr1';
        $ip = '10.10.10.10';
        $challenge = 'challenge';
        $response = 'response';
        $referralAddress = 'refaddr1';

        $captchaResponse = $this->getMockBuilder('Looptribe\Paytoshi\Captcha\CaptchaProviderResponse')
            ->disableOriginalConstructor()
            ->getMock();

        $apiResponse = $this->getMockBuilder('Looptribe\Paytoshi\Api\Response\FaucetSendResponse')
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

        $this->intervalEnforcer->expects($this->once())
            ->method('check')
            ->with($ip, $address)
            ->willReturn(null);

        $this->rewardProvider->expects($this->once())
            ->method('getReward')
            ->willReturn(10);

        $apiResponse->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(true);

        $apiResponse->expects($this->once())
            ->method('getAmount')
            ->willReturn(10);

        $apiResponse->expects($this->once())
            ->method('getRecipient')
            ->willReturn($address);

        $this->api->expects($this->at(0))
            ->method('send')
            ->with($this->apikey, $address, 10, $ip)
            ->willReturn($apiResponse);

        $this->api->expects($this->at(1))
            ->method('send')
            ->with($this->apikey, $referralAddress, 3, $ip)
            ->willThrowException(new \Exception('message'));

        $this->payoutRepository->expects($this->once())
            ->method('insert');

        $this->connection
            ->expects($this->never())
            ->method('rollBack');

        $this->connection
            ->expects($this->once())
            ->method('commit');

        $sut = new RewardLogic($this->connection, $this->payoutRepository, $this->rewardProvider, $this->api, $this->intervalEnforcer, $this->captchaProvider, $this->apikey, $this->referralPercentage);
        $result = $sut->create($address, $ip, $challenge, $response, $referralAddress);
        $this->assertSame(true, $result->isSuccessful());
        $this->assertSame(null, $result->getSeverity());
        $this->assertSame(null, $result->getError());
        $this->assertInstanceOf('Looptribe\Paytoshi\Api\Response\FaucetSendResponse', $result->getResponse());
        $this->assertSame(10, $result->getResponse()->getAmount());
        $this->assertSame($address, $result->getResponse()->getRecipient());
    }

    public function testCreate13()
    {
        $address = 'addr1';
        $ip = '10.10.10.10';
        $challenge = 'challenge';
        $response = 'response';
        $referralAddress = 'refaddr1';

        $captchaResponse = $this->getMockBuilder('Looptribe\Paytoshi\Captcha\CaptchaProviderResponse')
            ->disableOriginalConstructor()
            ->getMock();

        $apiResponse = $this->getMockBuilder('Looptribe\Paytoshi\Api\Response\FaucetSendResponse')
            ->disableOriginalConstructor()
            ->getMock();

        $referralApiResponse = $this->getMockBuilder('Looptribe\Paytoshi\Api\Response\FaucetSendResponse')
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

        $this->intervalEnforcer->expects($this->once())
            ->method('check')
            ->with($ip, $address)
            ->willReturn(null);

        $this->rewardProvider->expects($this->once())
            ->method('getReward')
            ->willReturn(10);

        $apiResponse->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(true);

        $apiResponse->expects($this->once())
            ->method('getAmount')
            ->willReturn(10);

        $apiResponse->expects($this->once())
            ->method('getRecipient')
            ->willReturn($address);

        $this->api->expects($this->at(0))
            ->method('send')
            ->with($this->apikey, $address, 10, $ip)
            ->willReturn($apiResponse);

        $this->api->expects($this->at(1))
            ->method('send')
            ->with($this->apikey, $referralAddress, 3, $ip)
            ->willReturn($referralApiResponse);

        $this->payoutRepository->expects($this->once())
            ->method('insert');

        $this->connection
            ->expects($this->never())
            ->method('rollBack');

        $this->connection
            ->expects($this->once())
            ->method('commit');

        $sut = new RewardLogic($this->connection, $this->payoutRepository, $this->rewardProvider, $this->api, $this->intervalEnforcer, $this->captchaProvider, $this->apikey, $this->referralPercentage);
        $result = $sut->create($address, $ip, $challenge, $response, $referralAddress);
        $this->assertSame(true, $result->isSuccessful());
        $this->assertSame(null, $result->getSeverity());
        $this->assertSame(null, $result->getError());
        $this->assertInstanceOf('Looptribe\Paytoshi\Api\Response\FaucetSendResponse', $result->getResponse());
        $this->assertSame(10, $result->getResponse()->getAmount());
        $this->assertSame($address, $result->getResponse()->getRecipient());
    }
}