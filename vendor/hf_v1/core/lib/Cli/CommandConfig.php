<?php
namespace Hyperframework\Cli;

use Hyperframework;
use Hyperframework\ConfigFileLoader;

class CommandConfig {
    private $configFile;
    private $subcommandConfigFiles = [];
    private $hasMultipleCommands;

    public function __construct($hasMultipleCommands = false) {
        $this->hasMultipleCommands = $hasMultipleCommands;
    }

    public function has($name, $subcommand = null) {
        $config = $this->getAll($subcommand);
        return isset($config[$name]);
    }

    public function get($name, $subcommand = null) {
        $config = $this->getAll($subcommand);
        if (isset($config[$name])) {
            return $config[$name];
        }
    }

    final public function getAll($subcommand = null) {
        if ($subcommand === null) {
            if ($config !== null) {
                return $config;
            }
            //config
            $config = ConfigFileLoader::loadPhp('command.php');
            $this->initializeClass($config);
            if (isset($config['options'])) {
                $config['options'] = $this->parseOptionConfig(
                    $config['options']
                );
            } else {
                $config['options'] = [];
            }
            if (isset($config['arguments'])) {
                if ($this->hasMultipleCommands()) {
                    throw new Exception;
                }
                $config['arguments'] = $this->parseArgumentConfig(
                    $config['arguments']
                );
            } elseif ($this->hasMultipleCommands() === false) {
                $class = null;
                $config['arguments'] = $this->getDefaultArgumentConfig($class);
            }
            $this->configFile = $config;
            return $config;
        }
        if (isset($this->subcommandConfigFiles[$subcommand]) === false) {
            //config
            $config = ConfigFileLoader::loadPhp(
                'subcommand/' . $subcommand . '.php'
            );
            $this->initializeClass($config);
            if (isset($config['options'])) {
                $config['options'] = $this->parseOptionConfig(
                    $config['options']
                );
            }
            if (isset($config['arguments'])) {
                $config['arguments'] = $this->parseArgumentConfig(
                    $config['arguments']
                );
            } else {
                $config['arguments'] =
                    $this->getDefaultArgumentConfig($config['class']);
            }
            $this->subcommandConfigFiles[$subcommand] = $config;
        }
        return $this->subcommandConfigFiles[$subcommand];
    }

    protected function hasMultipleCommands() {
        return $this->hasMultipleCommands;
    }

    public function hasSubcommand($name) {
        //config
        return ConfigFileLoader::hasFile('subcommand/' . $name . '.php');
    }

    protected function parseArgumentConfig($config) {
        return ArgumentConfigParser::parse($config);
    }

    protected function getDefaultArgumentConfig($class) {
        $method = new ReflectionMethod($class, 'execute');
        $params = $method->getParameters();
        $results = [];
        foreach ($params as $param) {
            //todo inflect name
            $results[] = array(
                'name' => $param->getName(),
                'is_optional' => $param->isOptional(),
                'is_collection' => $param->isArray()
            );
            //todo check valid for collection argument
        }
        return $results;
    }

    protected function getDefaultCommandClass($subcommand = null) {
        if ($subcommand === null) {
            return 'Command';
        }
        $tmp = ucwords(str_replace('-', ' ', $subcommand));
        return str_replace(' ', '', $tmp) . 'Command';
    }

    protected function parseOptionConfig($config) {
        return OptionConfigParser::parse($config);
    }

    private function initializeClass(array &$config) {
        if (isset($config['class']) === false) {
            $config['class'] = $this->getDefaultCommandClass();
        }
        $class = (string)$config['class'];
        if ($class === '') {
            throw new Exception;
        }
        if ($class[0] === '\\') {
            $config['class'] = substr($class, 1);
        }
        $config['class'] = Hyperframework\APP_ROOT_NAMESPACE . $class;
    }
}
