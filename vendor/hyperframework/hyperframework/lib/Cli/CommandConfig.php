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
use Hyperframework\Logging\LoggingException;

class CommandConfig {
    private $configs;
    private $class;
    private $optionConfigs;
    private $isSubcommandEnabled;
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
        }
        if ($subcommand === null && $this->isSubcommandEnabled()) {
            $this->argumentConfigs = [];
            return $this->argumentConfigs;
        }
        $configs = $this->get('arguments', $subcommand);
        if ($configs === null) {
            $argumentConfigs = $this->getDefaultArgumentConfigs($subcommand);
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
            return $this->subcommandClasses[$subcommand];
        } elseif ($this->class !== null) {
            return $this->class;
        }
        $class = $this->get('class', $subcommand);
        if ($class === null) {
            $class = $this->getDefaultClass($subcommand);
        }
        if (is_string($class) === false) {
            throw new ConfigException($this->getErrorMessage(
                $subcommand,
                " field 'class' must be a string, "
                    . gettype($class) . ' given.'
            ));
        }
        if ($subcommand !== null) {
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

    public function isSubcommandEnabled() {
        if ($this->isSubcommandEnabled === null) {
            $this->isSubcommandEnabled = Config::getBoolean(
                'hyperframework.cli.enable_subcommand', false
            );
        }
        return $this->isSubcommandEnabled;
    }

    public function hasSubcommand($subcommand) {
        if ($this->isSubcommandEnabled() === false) {
            return false;
        }
        if (preg_match('/^[a-zA-Z0-9][a-zA-Z0-9-]*$/', $subcommand) !== 1) {
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
                if (substr($file, -4) !== '.php') {
                    continue;
                }
                $name = substr($file, 0, strlen($file) - 4);
                $pattern = '/^[a-zA-Z0-9][a-zA-Z0-9-]*\.php$/';
                if (preg_match($pattern, $name) === 1) {
                    $this->subcommands[] = $name;
                }
            }
        }
        return $this->subcommands;
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
        if ($subcommand !== null) {
            if ($this->hasSubcommand($subcommand) === false) {
                throw new LoggingException(
                    "Subcommand '$subcommand' does not exist."
                );
            }
        }
        $configPath = $this->getConfigPath($subcommand);
        if (file_exists($configPath) === false) {
            throw new ConfigException($this->getErrorMessage(
                $subcommand, "config file '$configPath' does not exist."
            ));
        }
        $configs = require $configPath;
        if (is_array($configs) === false) {
            $type = gettype($configs);
            throw new ConfigException($this->getErrorMessage(
                $subcommand,
                "config file '$configPath' must return an array,"
                    . " $type returned."
            ));
        }
        if ($subcommand === null) {
            $this->configs = $configs;
        } else {
            $this->subcommandConfigs[$subcommand] = $configs;
        }
        return $configs;
    }

    protected function getDefaultClass($subcommand = null) {
        $rootNamespace = Config::getAppRootNamespace();
        if ($subcommand === null) {
            $class = 'Command';
            if ($rootNamespace !== '' && $rootNamespace !== '\\') {
                NamespaceCombiner::prepend($class, $rootNamespace);
            }
        } else {
            $tmp = ucwords(str_replace('-', ' ', $subcommand));
            $class = str_replace(' ', '', $tmp) . 'Command';
            $namespace = 'Subcommands';
            $rootNamespace = Config::getAppRootNamespace();
            if ($rootNamespace !== '' && $rootNamespace !== '\\') {
                NamespaceCombiner::prepend($namespace, $rootNamespace);
            }
            NamespaceCombiner::prepend($class, $namespace);
        }
        return $class;
    }

    protected function getDefaultOptionConfigs(
        array $options, $subcommand = null
    ) {
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

    protected function getDefaultArgumentConfigs($subcommand = null) {
        $class = $this->getClass($subcommand);
        if (method_exists($class, 'execute') === false) {
            if (class_exists($class) === false) {
                throw new ClassNotFoundException(
                    $this->getFailedToGetDefaultArgumentConfigsErrorMessage(
                        $subcommand,
                        "class '$class' does not exist"
                    )
                );
            }
            throw new MethodNotFoundException(
                $this->getFailedToGetDefaultArgumentConfigsErrorMessage(
                    $subcommand,
                    "method '$class::execute' does not exist"
                )
            );
        }
        $method = new ReflectionMethod($class, 'execute');
        $params = $method->getParameters();
        $result = [];
        $hasArray = false;
        $optionalArguemntName = null;
        foreach ($params as $param) {
            if ($hasArray) {
                throw new LogicException(
                    $this->getFailedToGetDefaultArgumentConfigsErrorMessage(
                        $subcommand,
                        "argument list of method '$class::execute' is "
                            . "invalid, array argument must be"
                            . " the last one"
                    )
                );
            }
            if ($optionalArguemntName !== null) {
                if ($param->isOptional() === false) {
                    throw new LogicException(
                        $this->getFailedToGetDefaultArgumentConfigsErrorMessage(
                            $subcommand,
                            "argument list of method '$class::execute' is "
                                . "invalid, argument '$optionalArguemntName'"
                                . " cannot be optional"
                        )
                    );
                }
            }
            if ($param->isArray()) {
                $hasArray = true;
            }
            if ($param->isOptional()) {
                $optionalArguemntName = $param->getName();
            }
            $result[] = new DefaultArgumentConfig($param);
        }
        return $result;
    }

    protected function parseArgumentConfigs(array $config, $subcommand = null) {
        return ArgumentConfigParser::parse($config, $subcommand);
    }

    protected function parseOptionConfigs(array $config, $subcommand = null) {
        return OptionConfigParser::parse(
            $config, $this->isSubcommandEnabled(), $subcommand
        );
    }

    protected function parseMutuallyExclusiveOptionGroupConfigs(
        array $configs, $subcommand = null
    ) {
        return MutuallyExclusiveOptionGroupConfigParser::parse(
            $configs,
            $this->getOptionConfigs($subcommand),
            $this->isSubcommandEnabled(),
            $subcommand
        );
    }

    private function getConfigPath($subcommand) {
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
            $folder = 'subcommands';
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

    private function getErrorMessage($subcommand, $extra) {
        if ($subcommand === null) {
            if ($this->isSubcommandEnabled()) {
                $result = 'Global command';
            } else {
                $result = 'Command';
            }
        } else {
            $result = "Subcommand '$subcommand'";
        }
        return $result . ' config error, ' . $extra;
    }

    private function getFailedToGetDefaultArgumentConfigsErrorMessage(
        $subcommand, $extra
    ) {
        $result = 'Failed to get ';
        if ($subcommand !== null) {
            $result .=
            "default argument configs of subcommand '$subcommand'";
        } else {
            $result .= 'command default argument configs';
        }
        return $result . ', ' . $extra . '.';
    }
}
