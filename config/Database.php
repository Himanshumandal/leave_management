<?php

class Database
{
    private static ?PDO $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {

            $host = env('DB_HOST', 'localhost');
            $port = env('DB_PORT', '3306');
            $dbname = env('DB_NAME', 'leavemanagementsystem');
            $username = env('DB_USERNAME', 'root');
            $password = env('DB_PASSWORD', '');

            $dbs = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,

                // TiDB Cloud SSL/TLS
                PDO::MYSQL_ATTR_SSL_CA => __DIR__ . '/certs/ca.pem',
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true,
            ];

            self::$instance = new PDO(
                $dbs,
                $username,
                $password,
                $options
            );
        }

        return self::$instance;
    }
}
?>