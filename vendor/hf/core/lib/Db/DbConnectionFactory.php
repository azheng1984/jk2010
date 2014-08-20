<?php
namespace Hyperframework\Db;

use PDO;
use Exception;
use Hyperframework\ConfigFileLoader;

class DbConnectionFactory {
    private static $config;

    public function get($name = null) {
        $config = $this->getConfig($name);
        if (isset($config['dsn'])) {
            $username = isset($config['username']) ? $config['username'] : null;
            $password = isset($config['password']) ? $config['password'] : null;
            $options = isset($config['options']) ? $config['options'] : null;
            return new PDO(
                $config['dsn'], $username, $password, $options
            );
        }
        throw new Exception;
    }

    private function getConfig($name) {
        if (self::$config === null) {
            $this->initializeConfig();
        }
        if ($name === null && isset(self::$config['dsn'])
            && is_string(self::$config['dsn'])) {
                return self::$config;
        }
        if (isset(self::$config[$name])) {
            return self::$config[$name];
        }
        throw new Exception("database config '$name' not found");
    }

    private function initializeConfig() {
        self::$config = ConfigFileLoader::loadPhp('db.php');
    }
}
