<?php
namespace App;
use Doctrine\DBAL\Connection;

class Database
{
    private static ?Connection $connection = null;

    public static function getConnection(): ?Connection
    {
        if (self::$connection == null) {
            $connectionParams = [
                'dbname' => $_ENV['dbname'],
                'user' => $_ENV['user'],
                'password' => $_ENV['password'],
                'host' => $_ENV['host'],
                'driver' => $_ENV['driver']
            ];
            self::$connection = \Doctrine\DBAL\DriverManager::getConnection($connectionParams);
        }
        return self::$connection;
    }
}