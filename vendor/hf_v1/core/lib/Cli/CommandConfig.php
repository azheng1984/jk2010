<?php
namespace Hyperframework\Cli;

use ReflectionMethod;
use Hyperframework;
use Hyperframework\ConfigFileLoader;
use Hyperframework\FullPathRecognizer

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
            if ($this->configFile !== null) {
                return $this->configFile;
            }
            $path = 'command.php';
            $rootPath = Config::get(
                'hyperframework.cli.command_config_root_path'
            );
            if ($rootPath !== null) {
                $path = $rootPath . DIRECTORY_SEPARATOR . $path;
            }
            $configPath = ConfigFileLoader::getFullPath(
                $path, 'hyperframework.cli.command_config_path'
            );
            if ($configPath === false) {
                $config = [];
            } else {
                $config = require $configPath;
                $this->initializeConfig($config, false);
            }
            $this->configFile = $config;
            return $config;
        }
        if (isset($this->subcommandConfigFiles[$subcommand]) === false) {
            $config = ConfigFileLoader::loadPhp(
                $this->getDefaultSubcommandConfigPath($subcommand)
            );
            if ($config === null) {
                $config = [];
            }
            $this->initializeConfig($config, true);
            $this->subcommandConfigFiles[$subcommand] = $config;
        }
        return $this->subcommandConfigFiles[$subcommand];
    }

    public function hasMultipleCommands() {
        return $this->hasMultipleCommands;
    }

    public function hasSubcommand($name) {
        return file_exists($this->getDefaultSubcommandConfigPath($name));
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

    private function getDefaultSubcommandConfigPath($subcommand) {
        $folder = Config::get('hyperframework.cli.subcommand_config_root_path');
        if ($folder === null) {
            $folder = 'subcommand';
        }
        $rootPath = Config::get(
            'hyperframework.cli.command_config_root_path'
        );
        if ($rootPath !== null) {
            if (FullPathRecognizer::isFull($folder) === false) {
                $folder = $rootPath . DIRECTORY_SEPARATOR . $folder;
            }
        }
        return ConfigFileLoader::getFullPath(
            $folder . DIRECTORY_SEPARATOR . $subcommand . '.php'
        );
    }

    private function initializeConfig(&$config, $isSubcommand) {
        $this->initializeClass($config);
        $this->initializeOptions($config);
        $this->initializeArguments($config, $isSubcommand);
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
        $namespace = Config::get('hyperframework.cli.command_root_namespace');
        if ($namespace === null) {
            $namespace = Hyperframework\APP_ROOT_NAMESPACE;
        }
        $config['class'] = $namespace . '\\' . $class;
    }

    private function initializeOptions(&$config) {
        if (isset($config['options'])) {
            $config['options'] = $this->parseOptionConfig($config['options']);
        } else {
            $config['options'] = [];
        }
    }

    private function initializeArguments(&$config, $isSubcommand) {
        if ($this->hasMultipleCommands() && $isSubcommand = false) {
            if (isset($config['arguments'])) {
                throw new Exception;
            }
            return;
        }
        if (isset($config['arguments'])) {
            $config['arguments'] = $this->parseArgumentConfig(
                $config['arguments']
            );
        } else {
            $config['arguments'] = $this->getDefaultArgumentConfig(
                $config['class']
            );
        }
    }
}
