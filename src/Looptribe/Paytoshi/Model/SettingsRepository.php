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

    public function setAll(array $data)
    {
        if ($this->cache === null) {
            $this->loadAll();
        }
        $insert = array();
        $update = array();
        foreach ($data as $key => $value)
        {
            if (isset($this->cache[$key])) {
                $update[] = $key;
            } else {
                $insert[] = $key;
            }
        }

        $count = 0;

        if (count($insert) > 0) {
            $sql = sprintf('INSERT INTO %s (`name`, `value`) VALUES ', self::TABLE_NAME);
            $database = $this->database;
            $sql .= implode(',', array_map(function ($field) use ($database, $data) {
                return sprintf('(%s,%s)', $database->quote($field), $database->quote($data[$field]));
            }, $insert));
            $count += $this->database->executeUpdate($sql);
        }

        if (count($update) > 0) {
            $sql = sprintf('UPDATE %s SET `value` = CASE `name` ', self::TABLE_NAME);
            foreach ($update as $field) {
                $sql .= sprintf('WHEN %s THEN %s ', $this->database->quote($field), $this->database->quote($data[$field]));
            }
            $sql .= 'END WHERE `name` IN (';
            $sql .= implode(', ', array_map(array($this->database, 'quote'), $update));
            $sql .= ')';
            $count += $this->database->executeUpdate($sql);
        }

        return $count;
    }

    private function loadAll()
    {
        $results = $this->database->fetchAll(sprintf('SELECT * FROM %s', self::TABLE_NAME));
        $this->cache = array();
        foreach ($results as $row) {
            $this->cache[$row['name']] = $row['value'];
        }
    }
}
