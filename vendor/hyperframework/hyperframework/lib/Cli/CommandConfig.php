<?php
namespace Hyperframework\Cli;

use ReflectionMethod;
use LogicException;
use Hyperframework;
use Hyperframework\Common\Config;
use Hyperframework\Common\NamespaceCombiner;
use Hyperframework\Common\PathCombiner;
use Hyperframework\Common\ConfigFileLoader;
use Hyperframework\Common\FullPathRecognizer;
use Hyperframework\Common\ConfigException;
use Hyperframework\Common\MethodNotFoundException;

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
            if ($arguments === null) {
            }
        } else {
            if (is_array($config) === false) {
                throw new ConfigException(
                    $this->getErrorMessage(
                        $subcommand,
                        'argument config must be an array, '
                            . gettype($config) . ' given.'
                    )
                );
            }
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
        if ($class !== null) {
            return $class;
        }
        $class = (string)$this->get('class', $subcommand);
        if ($class === '') {
            $class = (string)$this->getDefaultClass($subcommand);
        }
        if ($subcommand !== null) {
            if ($class[0] === '\\') {
                $class = ltrim($class, '\\');
            } else {
                $namespace = Config::getString(
                    'hyperframework.cli.subcommand_root_namespace'
                );
                if ($namespace === null) {
                    $namespace = 'Subcommands';
                    $rootNamespace = Config::getAppRootNamespace();
                    if ($rootNamespace !== '' && $rootNamespace !== '\\') {
                        NamespaceCombiner::prepend($rootNamespace, $namespace);
                    }
                }
                if ($namespace !== '' && $namespace !== '\\') {
                    NamespaceCombiner::prepend($class, $namespace);
                }
            }
            $this->subcommandClasses[$subcommand] = $class;
        } else {
            $this->class = $class;
        }
        return $class;
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
        $config = $this->get('options', $subcommand);
        if ($config !== null) {
            if (is_array($config) === false) {
                throw new ConfigException($this->getErrorMessage(
                    $subcommand,
                    'option config must be an array, '
                        . gettype($config) . ' given.'
                ));
            }
            $options = $this->parseOptionConfigs($config);
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
        $config = $this->get('mutually_exclusive_options', $subcommand);
        if ($config !== null) {
            if (is_array($config) === false) {
                throw new ConfigException($this->getErrorMessage(
                    $subcommand,
                    'mutually exclusive options must be an array, '
                        . gettype($config) . ' given.'
                ));
            }
            $optionGroups =
                $this->parseMutuallyExclusiveOptionConfigs(
                    $config, $subcommand
                );
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

    private function parseMutuallyExclusiveOptionConfigs(
        array $configs, $subcommand
    ) {
        if (is_array(current($configs)) === false) {
            $configs = [$configs];
        }
        $result = [];
        $includedOptions = [];
        $options = $this->getOptions();
        $errorMessagePrefix = $this->getErrorMessage(
            $subcommand, 'mutually exclusive option '
        );
        foreach ($configs as $config) {
            $isRequired = false;
            $mutuallyExclusiveOptions = [];
            foreach ($config as $item) {
                $item = (string)$item;
                if ($item === 'required') {
                    $isRequired = true;
                    continue;
                }
                if ($item === '' || $item[0] !== '-') {
                    $prefix = '-';
                    if (strlen($item) > 1) {
                        $prefix = '--';
                    }
                    throw new ConfigException(
                        $errorMessagePrefix
                            . "'$item' must start with '$prefix'."
                    );
                }
                $length = strlen($item);
                if ($length === 1) {
                    throw new ConfigException(
                        $errorMessagePrefix . "'$item' is not allowed."
                    );
                } elseif ($length === 2) {
                    $item = $item[1];
                } else {
                    if ($item[1] !== '-') {
                        throw new ConfigException(
                            $errorMessagePrefix
                                . "'$item' must start with '--'."
                        );
                    }
                    $item = substr($item, 2);
                }
                if (isset($options[$item]) === false) {
                    if ($item === '') {
                        continue;
                    }
                    throw new ConfigException(
                        $errorMessagePrefix . "'$item' is not defined."
                    );
                }
                $option = $options[$item];
                if (in_array($option, $includedOptions, true)) {
                    throw new ConfigException(
                        $errorMessagePrefix
                            . "'$item' cannot belong to multiple groups."
                    );
                }
                if (in_array($option, $mutuallyExclusiveOptions, true)) {
                    continue;
                }
                $mutuallyExclusiveOptions[] = $option;
            }
            if (count($mutuallyExclusiveOptions) !== 0) {
                $result[] = new MutuallyExclusiveOptionGroupConfig(
                    $mutuallyExclusiveOptions, $isRequired
                );
                $includedOptions =
                    array_merge($includedOptions, $mutuallyExclusiveOptions);
            }
        }
        return $result;
    }

    public function getDescription($subcommand = null) {
        return $this->get('description', $subcommand);
    }

    public function getName() {
        $name = (string)$this->get('name');
        if ($name === '') {
            throw new ConfigException(
                "Command config error, field 'name' is required."
            );
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
            $isConfigEnabled = Config::getBoolean(
                'hyperframework.cli.enable_command_config', true
            );
            if ($isConfigEnabled !== false) {
                $isDefaultConfigPath = false;
                $configPath = Config::getString(
                    'hyperframework.cli.command_config_path', ''
                );
                if ($configPath === '') {
                    $isDefaultConfigPath = true;
                    $configPath = 'command.php';
                }
                if ($isDefaultConfigPath
                    || FullPathRecognizer::isFull($configPath) === false
                ) {
                    $configRootPath = Config::getString(
                        'hyperframework.cli.command_config_root_path', ''
                    );
                    if ($configRootPath !== '') {
                        PathCombiner::prepend($configPath, $configRootPath);
                    }
                    $configPath = ConfigFileLoader::getFullPath($configPath);
                }
                if (file_exists($configPath)) {
                    $config = require $configPath;
                } else {
                    throw new ConfigException($this->getErrorMessage(
                        $subcommand,
                        "config file '$configPath' does not exist."
                    ));
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
            $this->isSubcommandEnabled = Config::getBoolean(
                'hyperframework.cli.enable_subcommand', false
            );
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
                $name = substr($file, 0, strlen($file) - 4);
                if (preg_match('/^[a-zA-Z0-9][a-zA-Z0-9-]*\.php$/', $name) === 1
                ) {
                    $this->subcommands[] = $name;
                }
            }
        }
        return $this->subcommands;
    }

    protected function parseArgumentConfigs(array $config) {
        return ArgumentConfigParser::parse($config);
    }

    protected function getDefaultArgumentConfigs($subcommand = null) {
        $class = $this->getClass($subcommand);
        $errorMessagePrefix = 'Failed to get default argument list config, ';
        if (method_exists($class, 'execute') === false) {
            throw new MethodNotFoundException(
                $errorMessagePrefix . "method '$class::execute' does not exist."
            );
        }
        $method = new ReflectionMethod($class, 'execute');
        $params = $method->getParameters();
        $result = [];
        $isArray = false;
        foreach ($params as $param) {
            if ($param->isArray()) {
                if ($isArray) {
                    throw new LogicException(
                        $errorMessagePrefix
                            . "argument list of method '$class::execute' is "
                            . "invalid, array argument must be the last one."
                    );
                }
                $isArray = true;
            }
            $result[] = new DefaultArgumentConfig($param);
        }
        return $result;
    }

    private function getDefaultClass($subcommand = null) {
        if ($subcommand === null) {
            $namespace = Config::getAppRootNamespace();
            $class = 'Command';
            if ($namespace !== '' && $namespace !== '\\') {
                NamespaceCombiner::prepend($class, $namespace);
            }
            return $class;
        }
        $tmp = ucwords(str_replace('-', ' ', $subcommand));
        return str_replace(' ', '', $tmp) . 'Command';
    }

    protected function parseOptionConfigs(array $config) {
        return OptionConfigParser::parse($config);
    }

    private function getSubcommandConfigPath($subcommand) {
        return $this->getSubcommandConfigRootPath()
            . DIRECTORY_SEPARATOR . $subcommand . '.php';
    }

    private function getSubcommandConfigRootPath() {
        $folder = Config::getString(
            'hyperframework.cli.subcommand_config_root_path'
        );
        if ($folder === null) {
            $folder = 'subcommand';
        }
        $commandConfigRootPath = Config::getString(
            'hyperframework.cli.command_config_root_path', ''
        );
        if ($commandConfigRootPath !== '') {
            if (FullPathRecognizer::isFull($folder) === false) {
                PathCombiner::prepend($folder, $commandConfigRootPath);
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

    private function getErrorMessage($subcommand, $extra = null) {
        if ($subcommand === null) {
            $result = 'Command';
        } else {
            $result = "Subcommand '$subcommand'";
        }
        $result .= ' config error';
        if ($extra === null) {
            return $result . '.';
        }
        return $result . ', ' . $extra;
    }
}
