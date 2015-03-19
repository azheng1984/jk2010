<?php
namespace Hyperframework\Cli;

use ReflectionMethod;
use LogicException;
use Hyperframework\Common\Config;
use Hyperframework\Common\NamespaceCombiner;
use Hyperframework\Common\FilePathCombiner;
use Hyperframework\Common\ConfigFileLoader;
use Hyperframework\Common\ConfigFileFullPathBuilder;
use Hyperframework\Common\FileFullPathRecognizer;
use Hyperframework\Common\ConfigException;
use Hyperframework\Common\ClassNotFoundException;
use Hyperframework\Common\MethodNotFoundException;

class CommandConfig {
    private $configs;
    private $class;
    private $optionConfigs;
    private $subcommands;
    private $mutuallyExclusiveOptionGroupConfigs;
    private $argumentConfigs;
    private $subcommandConfigs = [];
    private $subcommandClasses = [];
    private $subcommandOptionConfigs = [];
    private $subcommandMutuallyExclusiveOptionGroupConfigs = [];
    private $subcommandArgumentConfigs = [];

    public function getArgumentConfigs($subcommand = null) {
        if ($subcommand !== null
            && isset($this->subcommandArgumentConfigs[$subcommand])
        ) {
            return $this->subcommandArgumentConfigs[$subcommand];
        } elseif ($this->argumentConfigs !== null) {
            return $this->argumentConfigs;
        } elseif ($subcommand === null && $this->isSubcommandEnabled()) {
            return [];
        }
        $configs = $this->get('arguments', $subcommand);
        if ($configs === null) {
            $argumentConfigs = $this->getDefaultArgumentConfigs($subcommand);
            if ($argumentConfigs === null) {
            }
        } else {
            if (is_array($configs) === false) {
                throw new ConfigException(
                    $this->getErrorMessage(
                        $subcommand,
                        " field 'arguments' must be an array, "
                            . gettype($configs) . ' given.'
                    )
                );
            }
            $argumentConfigs =
                $this->parseArgumentConfigs($configs, $subcommand);
        }
        if ($argumentConfigs === null) {
            $argumentConfigs = [];
        }
        if ($subcommand !== null) {
            $this->subcommandArgumentConfigs[$subcommand] = $argumentConfigs;
        } else {
            $this->argumentConfigs = $argumentConfigs;
        }
        return $argumentConfigs;
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
                        NamespaceCombiner::prepend($namespace, $rootNamespace);
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

    public function getOptionConfigs($subcommand = null) {
        if ($subcommand !== null
            && isset($this->subcommandOptionConfigs[$subcommand])
        ) {
            return $this->subcommandOptionConfigs[$subcommand];
        } elseif ($subcommand === null && $this->optionConfigs !== null) {
            return $this->optionConfigs;
        }
        $config = $this->get('options', $subcommand);
        if ($config !== null) {
            if (is_array($config) === false) {
                throw new ConfigException($this->getErrorMessage(
                    $subcommand,
                    " field 'options' must be an array, "
                        . gettype($config) . ' given.'
                ));
            }
            $optionConfigs = $this->parseOptionConfigs($config, $subcommand);
            if ($optionConfigs === null) {
                $optionConfigs = [];
            }
        } else {
            $optionConfigs = [];
        }
        $defaultOptionConfigs = $this->getDefaultOptionConfigs(
            $optionConfigs, $subcommand
        );
        foreach ($defaultOptionConfigs as $optionConfig) {
            $name = $optionConfig->getName();
            $shortName = $optionConfig->getShortName();
            if ($name !== null && isset($optionConfigs[$name]) === false) {
                $optionConfigs[$name] = $optionConfig;
            }
            if ($shortName !== null
                && isset($optionConfigs[$shortName]) === false
            ) {
                $optionConfigs[$shortName] = $optionConfig;
            }
        }
        if ($subcommand !== null) {
            $this->subcommandOptionConfigs[$subcommand] = $optionConfigs;
        } else {
            $this->optionConfigs = $optionConfigs;
        }
        return $optionConfigs;
    }

    public function getMutuallyExclusiveOptionGroupConfigs($subcommand = null) {
        if ($subcommand !== null && isset(
            $this->subcommandMutuallyExclusiveOptionGroupConfigs[$subcommand]
        )) {
            return $this
                ->subcommandMutuallyExclusiveOptionGroupConfigs[$subcommand];
        } elseif ($this->mutuallyExclusiveOptionGroupConfigs !== null) {
            return $this->mutuallyExclusiveOptionGroupConfigs;
        }
        $config = $this->get('mutually_exclusive_option_groups', $subcommand);
        if ($config !== null) {
            if (is_array($config) === false) {
                throw new ConfigException($this->getErrorMessage(
                    $subcommand,
                    " field 'mutually_exclusive_option_groups'"
                         . ' must be an array,' . gettype($config) . ' given.'
                ));
            }
            $result = $this->parseMutuallyExclusiveOptionGroupConfigs(
                $config, $subcommand
            );
            if ($result === null) {
                $result = [];
            }
        } else {
            $result = [];
        }
        if ($subcommand !== null) {
            $this->subcommandMutuallyExclusiveOptionGroupConfigs[$subcommand] =
                $result;
        } else {
            $this->mutuallyExclusiveOptionGroupConfigs = $result;
        }
        return $result;
    }

    public function getMutuallyExclusiveOptionGroupConfigByOption(
        $option, $subcommand = null
    ) {
        $configs =
            $this->getMutuallyExclusiveOptionGroupConfigs($subcommand);
        foreach ($configs as $config) {
            if (in_array($option, $config->getOptionConfigs(), true)) {
                return $config;
            }
        }
    }

    protected function parseMutuallyExclusiveOptionGroupConfigs(
        array $configs, $subcommand
    ) {
        return MutuallyExclusiveOptionGroupConfigParser::parse(
            $configs, $this->getOptionConfigs($subcommand), $subcommand
        );
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
        } elseif (isset($this->subcommandConfigs[$subcommand])) {
            return $this->subcommandConfigs[$subcommand];
        }
        $configPath = $this->getConfigPath($subcommand);
        if (file_exists($configPath)) {
            $configs = require $configPath;
            if ($configs === null) {
                $configs = [];
            }
        } else {
            throw new ConfigException($this->getErrorMessage(
                $subcommand, "config file '$configPath' does not exist."
            ));
        }
        if ($subcommand === null) {
            $this->configs = $configs;
        } else {
            $this->subcommandConfigs[$subcommand] = $configs;
        }
        return $configs;
    }

    public function isSubcommandEnabled() {
        return Config::getBoolean(
            'hyperframework.cli.enable_subcommand', false
        );
    }

    public function hasSubcommand($subcommand) {
        if ($this->isSubcommandEnabled() === false) {
            return false;
        }
        return file_exists($this->getSubcommandConfigPath($subcommand));
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

    protected function parseArgumentConfigs(array $config, $subcommand) {
        return ArgumentConfigParser::parse($config, $subcommand);
    }

    private function getDefaultArgumentConfigs($subcommand) {
        $class = $this->getClass($subcommand);
        $errorMessagePrefix = 'Failed to get default argument list config, ';
        if (method_exists($class, 'execute') === false) {
            if (class_exists($class) === false) {
                throw new ClassNotFoundException(
                    $errorMessagePrefix . "class '$class' does not exist."
                );
            }
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

    protected function parseOptionConfigs(array $config, $subcommand) {
        return OptionConfigParser::parse($config, $subcommand);
    }

    private function getConfigPath($subcommand = null) {
        if ($subcommand === null) {
            $configPath = Config::getString(
                'hyperframework.cli.command_config_path', ''
            );
            $isDefault = false;
            if ($configPath === '') {
                $isDefault = true;
                $configPath = 'command.php';
            }
            if ($isDefault
                || FileFullPathRecognizer::isFullPath($configPath) === false
            ) {
                $configRootPath = Config::getString(
                    'hyperframework.cli.command_config_root_path', ''
                );
                if ($configRootPath !== '') {
                    FilePathCombiner::prepend($configPath, $configRootPath);
                }
                $configPath = ConfigFileFullPathBuilder::build($configPath);
            }
            return $configPath;
        } else {
            return $this->getSubcommandConfigPath($subcommand);
        }
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
            if (FileFullPathRecognizer::isFullPath($folder) === false) {
                FilePathCombiner::prepend($folder, $commandConfigRootPath);
            } else {
                return $folder;
            }
        }
        return ConfigFileFullPathBuilder::build($folder);
    }

    protected function getDefaultOptionConfigs(array $options, $subcommand) {
        $result = [];
        if (isset($options['help']) === false) {
            $shortName = 'h';
            if (isset($options['-h'])) {
                $shortName = null;
            }
            $result[] = new OptionConfig(
                'help', $shortName, false, false, null, null
            );
        }
        if ($subcommand === null && isset($options['version']) === false) {
            $result[] = new OptionConfig(
                'version', null, false, false, null, null
            );
        }
        return $result;
    }

    private function getErrorMessage($subcommand, $extra) {
        if ($subcommand === null) {
            $result = 'Command';
        } else {
            $result = "Subcommand '$subcommand'";
        }
        return $result . ' config error, ' . $extra;
    }
}
