<?php

namespace Looptribe\Paytoshi\Tests\Logic;

use Doctrine\DBAL\Connection;
use Looptribe\Paytoshi\Api\PaytoshiApiInterface;
use Looptribe\Paytoshi\Logic\IntervalEnforcerInterface;
use Looptribe\Paytoshi\Logic\RewardLogic;
use Looptribe\Paytoshi\Logic\RewardProviderInterface;
use Looptribe\Paytoshi\Model\RecipientRepository;

class RewardLogicTest extends \PHPUnit_Framework_TestCase
{
    /** @var Connection */
    private $connection;
    /** @var RecipientRepository */
    private $recipientRepository;
    /** @var  RewardProviderInterface */
    private $rewardProvider;
    /** @var PaytoshiApiInterface */
    private $api;
    /** @var IntervalEnforcerInterface */
    private $intervalEnforcer;

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
    }

    public function testCreate1()
    {
        $address = 'addr1';
        $ip = '10.10.10.10';
        $challenge = '';
        $response = '';

        $sut = new RewardLogic($this->connection, $this->recipientRepository, $this->rewardProvider, $this->api, $this->intervalEnforcer);
        $sut->create($address, $ip, $challenge, $response);
    }
}
