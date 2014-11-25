<?php
namespace Hyperframework\Cli;

use Exception;
use ReflectionMethod;
use Hyperframework;
use Hyperframework\Common\ConfigFileLoader;
use Hyperframework\Common\Config;
use Hyperframework\Common\FullPathRecognizer;

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
            $config = null;
            $configPath = Config::get('hyperframework.cli.command_config_path');
            if ($configPath !== false) {
                $isDefaultConfigPath = false;
                if ($configPath === null) {
                    $isDefaultConfigPath = true;
                    $configPath = 'command.php';
                }
                if (FullPathRecognizer::isFull($configPath) === false) {
                    $configRootPath = Config::get(
                        'hyperframework.cli.command_config_root_path'
                    );
                    if ($configRootPath !== null) {
                        $configPath = $configRootPath
                            . DIRECTORY_SEPARATOR . $configPath;
                    }
                }
                if (file_exists($configPath)) {
                    $config = require $configPath;
                } else {
                    if ($isDefaultConfigPath === false) {
                        throw new Exception;
                    }
                    $config = [];
                }
            } else {
                $config = [];
            }
            $this->initializeConfig($config, false);
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
        $path = $folder . DIRECTORY_SEPARATOR . $subcommand . '.php';
        $rootPath = Config::get(
            'hyperframework.cli.command_config_root_path'
        );
        if ($rootPath !== null) {
            if (FullPathRecognizer::isFull($path) === false) {
                $path = $rootPath . DIRECTORY_SEPARATOR . $path;
            } else {
                return $path;
            }
        }
        return ConfigFileLoader::getFullPath($path);
    }

    private function initializeConfig(&$config, $isSubcommand) {
        $this->initializeClass($config, $isSubcommand);
        $this->initializeOptions($config);
        $this->initializeArguments($config, $isSubcommand);
    }

    private function initializeClass(array &$config, $isSubcommand) {
        if (isset($config['class']) === false) {
            $config['class'] = $this->getDefaultCommandClass();
        }
        $class = (string)$config['class'];
        if ($class === '') {
            throw new Exception;
        }
        if ($class[0] === '\\') {
            $config['class'] = substr($class, 1);
            return;
        }
        $namespace = Config::get('hyperframework.cli.command_root_namespace');
        if ($namespace === null) {
            $namespace = Config::get('hyperframework.app_root_namespace');
        }
        if ($isSubcommand) {
            $namespace .= '\Subcommands';
        }
        if ($namespace != '' && (string)$namespace !== '') {
            $config['class'] = $namespace . '\\' . $class;
        }
    }

    //add --help and --version option
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
