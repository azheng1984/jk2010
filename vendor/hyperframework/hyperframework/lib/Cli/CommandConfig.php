<?php
namespace Hyperframework\Cli;

use Exception;
use ReflectionMethod;
use Hyperframework;
use Hyperframework\Common\Config;
use Hyperframework\Common\ConfigFileLoader;
use Hyperframework\Common\FullPathRecognizer;

class CommandConfig {
    private $isSubcommandEnabled;
    private $configs;
    private $class;
    private $options;
    private $arguments;
    private $subcommandConfigs = [];
    private $subcommandClasses = [];
    private $subcommandOptions = [];
    private $subcommandArguments = [];

    public function getArguments($subcommand = null) {
        if ($this->isSubcommandEnabled() && $isSubcommand = false) {
            if (isset($config['arguments'])) {
                throw new Exception;
            }
            return;
        }
        if (isset($config['arguments'])) {
            $config['arguments'] = $this->parseArgumentConfigs(
                $config['arguments']
            );
        } else {
            $config['arguments'] = $this->getDefaultArgumentConfigs(
                $config['class']
            );
        }
    }

    public function getClass($subcommand = null) {
        $class = null;
        if (isset($config['class'])) {
            $class = (string)$config['class'];
            if ($class !== '') {
                return $class;
            }
        }
        $config['class'] = $this->getDefaultCommandClass();
        $class = (string)$config['class'];
        if ($class === '') {
            throw new Exception;
        }
        if ($class[0] === '\\') {
            $config['class'] = substr($class, 1);
            return;
        }
        $namespace = (string)Config::get(
            'hyperframework.cli.command_root_namespace'
        );
        if ($namespace === '') {
            $namespace = (string)Config::get(
                'hyperframework.app_root_namespace'
            );
        }
        if ($isSubcommand) {
            if ($namespace === '') {
                $namespace = 'Subcommands';
            } else {
                $namespace .= '\Subcommands';
            }
        }
        if ($namespace !== '') {
            $config['class'] = $namespace . '\\' . $class;
        }
    }

    public function getOptions($subcommand = null) {
        if ($this->options !== null) {
            return $this->options;
        }
        $optionConfigs = $this->get('options', $subcommand);
        if ($optionConfigs === null) {
            $this->options = $this->parseOptionConfigs($config);
        } else {
            $this->options = [];
        }
        $defaultConfigs = $this->getDefaultOptionConfigs($subcommand);
        $defaultOptions = $this->parseOptionConfigs($defaultConfigs);
        foreach ($defaultOptions as $key => $value) {
            if (isset($this->options[$key]) === false) {
                $this->options[$key] = $value;
            }
        }
        return $this->options;
    }

    public function getMutuallyExclusiveOptions($option, $subcommand = null) {
    }

    public function getDescription($subcommand = null) {
        return $this->get('description', $subcommand);
    }

    public function getName() {
        $name = (string)$this->get('name');
        if ($name === '') {
            throw new Exception;
        }
        return $name;
    }

    public function getVersion() {
        return $this->get('version');
    }

    public function getHelpClass($subcommand = null) {
        return $this->get('help_class');
    }

    protected function get($name, $subcommand = null) {
        $configs = $this->getAll($subcommand);
        if (isset($configs[$name])) {
            return $configs[$name];
        }
    }

    protected function getAll($subcommand = null) {
        if ($subcommand === null) {
            if ($this->configs !== null) {
                return $this->configs;
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
                $configPath = ConfigFileLoader::getFullPath($configPath);
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
            //$this->initializeConfig($config, false);
            $this->configs = $config;
            return $config;
        }
        if (isset($this->subcommandConfigs[$subcommand]) === false) {
            $config = ConfigFileLoader::loadPhp(
                $this->getSubcommandConfigPath($subcommand)
            );
            if ($config === null) {
                $config = [];
            }
            //$this->initializeConfig($config, true);
            $this->subcommandConfigs[$subcommand] = $config;
        }
        return $this->subcommandConfigs[$subcommand];
    }

    public function isSubcommandEnabled() {
        if ($this->isSubcommandEnabled === null) {
            $this->isSubcommandEnabled =
                Config::get('hyperframework.cli.enable_subcommand') === true;
        }
        return $this->isSubcommandEnabled;
    }

    public function hasSubcommand($name) {
        return file_exists($this->getSubcommandConfigPath($name));
    }

    protected function parseArgumentConfigs($config) {
        return ArgumentConfigParser::parse($config);
    }

    protected function getDefaultArgumentConfigs($class) {
        $method = new ReflectionMethod($class, 'execute');
        $params = $method->getParameters();
        $result = [];
        foreach ($params as $param) {
            $result[] = new DefaultArgumentConfig($param);
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

    protected function parseOptionConfigs($config) {
        return OptionConfigParser::parse($config);
    }

    private function getSubcommandConfigPath($subcommand) {
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

    protected function getDefaultOptionConfigs($subcommand = null) {
        if ($subcommand !== null) {
            return ['-h, --help'];
        } else {
            return['-h, --help', '--version'];
        }
    }
}
