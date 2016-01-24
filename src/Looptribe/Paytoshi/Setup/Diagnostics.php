<?php

namespace Looptribe\Paytoshi\Setup;

use Looptribe\Paytoshi\Model\ConnectionFactory;
use Looptribe\Paytoshi\Model\SettingsRepository;

class Diagnostics
{
    /** @var SettingsRepository */
    private $settingsRepository;

    /** @var ConnectionFactory */
    private $connectionFactory;

    /** @var string */
    private $configPath;

    public function __construct(SettingsRepository $settingsRepository, ConnectionFactory $connectionFactory, $configPath)
    {
        $this->settingsRepository = $settingsRepository;
        $this->connectionFactory = $connectionFactory;
        $this->configPath = $configPath;
    }

    /**
     * @return bool
     */
    public function requiresSetup()
    {
        try {
            if (null === $this->settingsRepository->get('password')) {
                return true;
            }
        }
        catch (\Exception $ex) {
            return true;
        }
        return false;
    }

    public function isConfigWritable()
    {
        return is_writable($this->configPath);
    }

    public function checkDatabase($connectionParams)
    {
        $connectionParams = array_merge($connectionParams, array('driver' => 'pdo_mysql'));
        $connection = $this->connectionFactory->create($connectionParams);
        $connection->getSchemaManager()->listTables();
    }

    public function checkRequirements()
    {
        return new RequirementsChecker();
    }
}
