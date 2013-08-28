<?php
//TODO 支持真持久链接 ATTR_PERSISTENT
class DbConnectionFactory {
    private static $config;

    public function get($name) {
        $config = $this->getConfig($name);
        if (isset($config['dsn'])) {
            $username = isset($config['username']) ? $config['username'] : null;
            $password = isset($config['password']) ? $config['password'] : null;
            return new PDO(
                $config['dsn'],
                $username,
                $password,
                array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
            );
        }
        throw new Exception;
    }

    private function getConfig($name) {
        if (self::$config === null) {
            self::$config = require CONFIG_PATH.'database.config.php';
        }
        if ($name === 'default' && isset(self::$config['dsn'])
            && is_string(self::$config['dsn'])) {
                return self::$config;
            }
        if (isset(self::$config[$name])) {
            return self::$config[$name];
        }
        throw new Exception("database config '$name' not found");
    }
}
