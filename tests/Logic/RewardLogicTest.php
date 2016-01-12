<?php

namespace Looptribe\Paytoshi\Tests\Logic;

use Looptribe\Paytoshi\Logic\RewardLogic;

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
        $challenge = '';
        $response = '';

        /*$this->connection
            ->expects($this->once())
            ->method('commit');*/

        $this->connection
            ->expects($this->never())
            ->method('rollback');

        $sut = new RewardLogic($this->connection, $this->recipientRepository, $this->rewardProvider, $this->api, $this->intervalEnforcer, $this->captchaProvider);
        $payout = $sut->create($address, $ip, $challenge, $response);
        $this->assertInstanceOf('Looptribe\Paytoshi\Model\Payout', $payout);
    }

    public function testCreate2()
    {
        $address = 'addr1';
        $ip = '10.10.10.10';
        $challenge = '';
        $response = '';

        $this->intervalEnforcer->method('check')
            ->willReturn(new \DateInterval('P1D'));

        $this->connection
            ->expects($this->never())
            ->method('commit');

        $this->connection
            ->expects($this->once())
            ->method('rollback');

        $sut = new RewardLogic($this->connection, $this->recipientRepository, $this->rewardProvider, $this->api, $this->intervalEnforcer, $this->captchaProvider);
        $this->setExpectedException('Exception');
        $payout = $sut->create($address, $ip, $challenge, $response);
    }
}
