<?php
use Hyperframework\ConfigFileLoader;

class CommandConfig {
    private static $configFile;
    private static $subcommandConfigFiles = [];

    public static function has($name, $subcommand = null) {
        $config = static::getAll($subcommand);
        return isset($config[$name]);
    }

    public static function get($name, $subcommand = null) {
        $config = static::getAll($subcommand);
        if (isset($config[$name])) {
            return $config[$name];
        }
    }

    final public static function getAll($subcommand = null) {
        if ($subcommand === null) {
            if (self::$configFile === null) {
                self::$configFile = ConfigFileLoader::loadPhp('command.php');
            }
            if (isset(self::$configFile['options'])) {
                self::$configFile['options'] = static::parseOptionConfig(
                    self::$configFile['options']
                );
            }
            if (isset(self::$configFile['arguments'])) {
                self::$configFile['arguments'] = static::parseArgumentConfig(
                    self::$configFile['arguments']
                );
            }
            return self::$configFile;
        }
        if (isset(self::$subcommandConfigFiles[$subcommand]) === false) {
            $config = ConfigFileLoader::loadPhp('command.php');
            if (isset($config['options'])) {
                $config['options'] = static::parseOptionConfig(
                    $config['options']
                );
            }
            if (isset($config['arguments'])) {
                $config['arguments'] = static::parseArgumentConfig(
                    $config['arguments']
                );
            }
            self::$subcommandConfigFiles[$subcommand] = $config;
        }
        return self::$subcommandConfigFiles[$subcommand];
    }

    public static function hasSubcommand($name) {
        return ConfigFileLoader::hasFile('subcommand/' . $name . '.php');
    }

    protected static function parseArgumentConfig($config) {
        return ArgumentConfigParser::parse($config);
    }

    protected static function parseOptionConfig($config) {
        return OptionConfigParser::parse($config);
    }
}
