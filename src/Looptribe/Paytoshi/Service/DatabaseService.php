<?php

/**
 * Paytoshi Faucet Script
 *
 * Contact: info@paytoshi.org
 *
 * @author: Looptribe
 * @link: https://paytoshi.org
 * @package: Looptribe\Paytoshi
 */

namespace Looptribe\Paytoshi\Service;

use Exception;
use Looptribe\Paytoshi\Exception\PaytoshiException;
use PDO;
use PDOException;
use Symfony\Component\Yaml\Yaml;

class DatabaseService
{

    protected $config;
    /** @var PDO */
    protected $conn;

    public function __construct($config)
    {
        $this->config = $config;
        $this->parseConfig();
    }

    private function connect()
    {
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        try {
            $dsn = sprintf("%s:host=%s;dbname=%s",
                'mysql',
                $this->config['database']['host'],
                $this->config['database']['name']
            );

            $this->conn = new PDO($dsn, $this->config['database']['username'], $this->config['database']['password'],
                $options);
        } catch (PDOException $e) {
            if ($e->getCode() == 1045) {
                throw new PaytoshiException(sprintf('Invalid database credentials. Please check your config in %s.',
                    $this->config['config_file']), null, $e);
            }

            throw new PaytoshiException(sprintf('Unable to connect. Please check your database exists and the config in "%s" is correct.',
                $this->config['config_file']), null, $e);
        }
    }

    private function parseConfig()
    {
        try {
            $config = Yaml::parse($this->config['config_file']);
            $this->config['database'] = $config['database'];
        } catch (Exception $e) {
            throw new PaytoshiException(sprintf('Error while reading configuration file "%s": %s',
                $this->config['config_file'], $e->getMessage()), null, $e);
        }
    }

    public function run($sql, $params = array())
    {
        if (!$this->conn) {
            $this->connect();
        }
        $sql = trim($sql);
        try {
            $statement = $this->conn->prepare($sql);
            foreach ($params as $key => &$value) {
                $statement->bindParam($key, $value);
            }
            if ($statement->execute($params) !== false) {
                if (preg_match("/^(" . implode("|", array("select", "describe", "pragma")) . ") /i", $sql)) {
                    return $statement->fetchAll(PDO::FETCH_ASSOC);
                } elseif (preg_match("/^(" . implode("|", array("insert")) . ") /i", $sql)) {
                    return $this->conn->lastInsertId();
                } elseif (preg_match("/^(" . implode("|", array("delete", "update")) . ") /i", $sql)) {
                    return $statement->rowCount();
                }
            }
        } catch (PDOException $e) {
            throw new PaytoshiException(sprintf('Unable to execute query: %s.', $sql), null, $e);
        }
    }

    public function beginTransaction()
    {
        if (!$this->conn) {
            $this->connect();
        }
        return $this->conn->beginTransaction();
    }

    public function commit()
    {
        if (!$this->conn) {
            $this->connect();
        }
        return $this->conn->commit();
    }

    public function rollback()
    {
        if (!$this->conn) {
            $this->connect();
        }
        return $this->conn->rollback();
    }

}
