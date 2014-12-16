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
    private $subcommands;
    private $mutuallyExclusiveOptionGroups;
    private $arguments;
    private $subcommandConfigs = [];
    private $subcommandClasses = [];
    private $subcommandOptionConfigs = [];
    private $subcommandMutuallyExclusiveOptionGroups = [];
    private $subcommandArguments = [];

    public function getArguments($subcommand = null) {
        if ($subcommand !== null
            && isset($this->subcommandArguments[$subcommand])
        ) {
            return $this->subcommandArguments[$subcommand];
        } elseif ($this->arguments !== null) {
            return $this->arguments;
        } elseif ($subcommand === null && $this->isSubcommandEnabled()) {
            return [];
        }
        $config = $this->get('arguments', $subcommand);
        if ($config === null) {
            $arguments = $this->getDefaultArgumentConfigs($subcommand);
        } else {
            $arguments = $this->parseArgumentConfigs($config);
        }
        if ($arguments === null) {
            $arguments = [];
        }
        if ($subcommand !== null) {
            $this->subcommandArguments[$subcommand] = $arguments;
        } else {
            $this->arguments = $arguments;
        }
        return $arguments;
    }

    public function getClass($subcommand = null) {
        $class = null;
        if ($subcommand !== null
            && isset($this->subcommandClasses[$subcommand])
        ) {
            $class = $this->subcommandClasses[$subcommand];
        } elseif ($this->class !== null) {
            $class = $this->class;
        }
        if ($class === '') {
            return;
        } elseif ($class !== null) {
            return $class;
        }
        $class = (string)$this->get('class', $subcommand);
        if ($class === '') {
            $class = (string)$this->getDefaultClass($subcommand);
        }
        if ($class !== '' && $subcommand !== null) {
            if ($class[0] === '\\') {
                $class = substr($class, 1);
            } else {
                $namespace = Config::get(
                    'hyperframework.cli.subcommand_root_namespace'
                );
                if ($namespace === null) {
                    $namespace = (string)Config::get(
                        'hyperframework.app_root_namespace'
                    );
                    if ($namespace === '') {
                        $namespace = 'Subcommands';
                    } else {
                        $namespace .= '\Subcommands';
                    }
                } else {
                    $namespace = (string)$namespace;
                }
                if ($namespace !== '') {
                    $class = $namespace . '\\' . $class;
                }
            }
        }
        if ($subcommand !== null) {
            $this->subcommandClasses[$subcommand] = $class;
        } else {
            $this->class = $class;
        }
        if ($class !== '') {
            return $class;
        }
    }

    public function getOptions($subcommand = null) {
        if ($subcommand !== null
            && isset($this->subcommandOptions[$subcommand])
        ) {
            return $this->subcommandOptions[$subcommand];
        } elseif ($this->options !== null) {
            return $this->options;
        }
        if ($this->options !== null) {
            return $this->options;
        }
        $optionConfigs = $this->get('options', $subcommand);
        if ($optionConfigs !== null) {
            $options = $this->parseOptionConfigs($optionConfigs);
            if ($options === null) {
                $options = [];
            }
        } else {
            $options = [];
        }
        $defaultOptions = $this->getDefaultOptions($options, $subcommand);
        foreach ($defaultOptions as $option) {
            $name = $option->getName();
            $shortName = $option->getShortName();
            if ($name !== null && isset($options[$name]) === false) {
                $options[$name] = $option;
            }
            if ($shortName !== null && isset($options[$shortName]) === false) {
                $options[$shortName] = $option;
            }
        }
        if ($subcommand !== null) {
            $this->subcommandOptions[$subcommand] = $options;
        } else {
            $this->options = $options;
        }
        return $options;
    }

    public function getMutuallyExclusiveOptionGroups($subcommand = null) {
        if ($subcommand !== null &&
            isset($this->subcommandMutuallyExclusiveOptionGroups[$subcommand])
        ) {
            return $this->subcommandMutuallyExclusiveOptionGroups[$subcommand];
        } elseif ($this->mutuallyExclusiveOptionGroups !== null) {
            return $this->mutuallyExclusiveOptionGroups;
        }
        $configs = $this->get('mutually_exclusive_options', $subcommand);
        if ($configs !== null) {
            $optionGroups =
                $this->parseMutuallyExclusiveOptionConfigs($configs);
            if ($optionGroups === null) {
                $optionGroups = [];
            }
        } else {
            $optionGroups = [];
        }
        if ($subcommand !== null) {
            $this->subcommandMutuallyExclusiveOptionGroups[$subcommand] =
                $optionGroups;
        } else {
            $this->mutuallyExclusiveOptionGroups = $optionGroups;
        }
        return $optionGroups;
    }

    public function getMutuallyExclusiveOptionGroupByOption(
        $option, $subcommand = null
    ) {
        $optionGroups = $this->getMutuallyExclusiveOptionGroups($subcommand);
        foreach ($optionGroups as $optionGroup) {
            if (in_array($option, $optionGroup->getOptions(), true)) {
                return $optionGroup;
            }
        }
    }

    protected function parseMutuallyExclusiveOptionConfigs($configs) {
        if (is_array($configs) === false) {
            throw new Exception;
        }
        if (is_array(current($configs)) === false) {
            $configs = [$configs];
        }
        $result = [];
        $includedOptions = [];
        $options = $this->getOptions();
        foreach ($configs as $config) {
            $isRequired = false;
            $mutuallyExclusiveOptions = [];
            foreach ($config as $item) {
                $item = (string)$item;
                if ($item === 'required') {
                    $isRequired = true;
                    continue;
                }
                if ($item[0] !== '-') {
                    throw new Exception;
                }
                $length = strlen($item);
                if ($length === 1) {
                    throw new Exception;
                } elseif ($length === 2) {
                    $item = $item[1];
                } else {
                    if ($item[1] !== '-') {
                        throw new Exception;
                    }
                    $item = substr($item, 2);
                }
                if (isset($options[$item]) === false) {
                    if ($item === '') {
                        continue;
                    } elseif ($item[0] !== '-') {
                        $message = "Unknown attribute '$item'";
                    } else {
                        $message = "Undefined option '$item'";
                    }
                    throw new Exception($message);
                }
                $option = $options[$item];
                if (in_array($option, $includedOptions, true)) {
                    throw new Exception;
                }
                $includedOptions[] = $option;
                $mutuallyExclusiveOptions[] = $option;
            }
            if (count($mutuallyExclusiveOptions) === 0) {
                throw new Exception;
            }
            $result[] = new MutuallyExclusiveOptionGroupConfig(
                $mutuallyExclusiveOptions, $isRequired
            );
        }
        return $result;
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
        if ($this->isSubcommandEnabled() === false) {
            return false;
        }
        return file_exists($this->getSubcommandConfigPath($name));
    }

    public function getSubcommands() {
        if ($this->isSubcommandEnabled() === false) {
            return [];
        }
        if ($this->subcommands === null) {
            $this->subcommands = [];
            foreach (scandir($this->getSubcommandConfigRootPath()) as $file) {
                if (substr($file, -4) === '.php') {
                    $this->subcommands[] =
                        substr(substr($file, 0, strlen($file) - 4));
                } else {
                    throw new Exception;
                }
            }
        }
        return $this->subcommands;
    }

    protected function parseArgumentConfigs($config) {
        return ArgumentConfigParser::parse($config);
    }

    protected function getDefaultArgumentConfigs($subcommand = null) {
        $class = $this->getClass($subcommand);
        if ($class === null) {
            throw new Exception;
        }
        if (class_exists($class) === false) {
            throw new Exception;
        }
        $method = new ReflectionMethod($class, 'execute');
        $params = $method->getParameters();
        $result = [];
        foreach ($params as $param) {
            $result[] = new DefaultArgumentConfig($param);
        }
        return $result;
    }

    private function getDefaultClass($subcommand = null) {
        if ($subcommand === null) {
            $namespace = (string)Config::get(
                'hyperframework.app_root_namespace'
            );
            if ($namespace === '') {
                return 'Command';
            } else {
                return $namespace . '\Command';
            }
        }
        $tmp = ucwords(str_replace('-', ' ', $subcommand));
        return str_replace(' ', '', $tmp) . 'Command';
    }

    protected function parseOptionConfigs($config) {
        return OptionConfigParser::parse($config);
    }

    private function getSubcommandConfigPath($subcommand) {
        return $this->getSubcommandConfigRootPath()
            . DIRECTORY_SEPARATOR . $subcommand . '.php';
    }

    private function getSubcommandConfigRootPath() {
        $folder = Config::get('hyperframework.cli.subcommand_config_root_path');
        if ($folder === null) {
            $folder = 'subcommand';
        }
        $commandConfigRootPath = Config::get(
            'hyperframework.cli.command_config_root_path'
        );
        if ($commandConfigRootPath !== null) {
            if (FullPathRecognizer::isFull($folder) === false) {
                $folder = $commandConfigRootPath
                    . DIRECTORY_SEPARATOR . $folder;
            } else {
                return $folder;
            }
        }
        return ConfigFileLoader::getFullPath($folder);
    }

    protected function getDefaultOptions(array $options, $subcommand = null) {
        $result = [];
        if (isset($options['help']) === false) {
            $shortName = 'h';
            if (isset($options['-h'])) {
                $shortName = null;
            }
            $result[] = new OptionConfig('help', $shortName);
        }
        if ($subcommand === null && isset($options['version']) === false) {
            $result[] = new OptionConfig('version');
        }
        return $result;
    }
}
