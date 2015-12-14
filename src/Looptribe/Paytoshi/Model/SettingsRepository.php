<?php

namespace Looptribe\Paytoshi\Model;

use Doctrine\DBAL\Connection;

class SettingsRepository
{
    const TABLE_NAME = 'paytoshi_settings';

    private $cache = null;

    /** @var Connection */
    private $database;

    public function __construct(Connection $dabatase)
    {
        $this->database = $dabatase;
    }

    public function get($key, $default = null)
    {
        if ($this->cache === null) {
            $this->loadAll();
        }
        return isset($this->cache[$key]) ? $this->cache[$key] : $default;
    }

    private function loadAll()
    {
        $this->cache = array();
        $results = $this->database->fetchAll(sprintf('SELECT * FROM %s', self::TABLE_NAME));
        foreach ($results as $key => $value) {
            $this->cache[$key] = $value;
        }
    }
}
