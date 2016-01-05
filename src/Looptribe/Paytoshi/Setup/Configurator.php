<?php

namespace Looptribe\Paytoshi\Setup;

use Doctrine\DBAL\Connection;
use Looptribe\Paytoshi\Security\PasswordGeneratorInterface;
use Looptribe\Paytoshi\Security\SaltGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Yaml\Yaml;

class Configurator
{
    /** @var Connection */
    private $database;

    /** @var PasswordGeneratorInterface */
    private $passwordGenerator;

    /** @var SaltGeneratorInterface */
    private $saltGenerator;

    /** @var PasswordEncoderInterface */
    private $passwordEncoder;

    private $sqlFile;

    private $configPath;

    public function __construct(
        Connection $database,
        PasswordGeneratorInterface $passwordGenerator,
        SaltGeneratorInterface $saltGenerator,
        PasswordEncoderInterface $passwordEncoder,
        $sqlFile,
        $configPath
    ) {
        $this->database = $database;
        $this->passwordGenerator = $passwordGenerator;
        $this->saltGenerator = $saltGenerator;
        $this->passwordEncoder = $passwordEncoder;
        $this->sqlFile = $sqlFile;
        $this->configPath = $configPath;
    }

    public function saveConfig($config)
    {
        $yml = Yaml::dump($config);

        if (@file_put_contents($this->configPath, $yml) === false) {
            throw new \RuntimeException(sprintf('Cannot write configuration file "%s".', $this->configPath));
        }
    }

    public function setup()
    {
        $password = $this->passwordGenerator->generate();
        $salt = $this->saltGenerator->generate();
        $passwordHash = $this->passwordEncoder->encodePassword($password, $salt);
        $this->setupDatabase($passwordHash);
        return array(
            'password' => $password
        );
    }

    private function setupDatabase($passwordHash)
    {
        $sql = @file_get_contents($this->sqlFile);
        if ($sql === false) {
            throw new \RuntimeException(sprintf('Cannot read file "%s".', $this->sqlFile));
        }
        $this->database->executeQuery($sql, array(
            'password' => $passwordHash
        ));
    }
}
