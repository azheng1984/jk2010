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

class CommandConfig implements CommandConfigInterface {
    private $configs;
    private $class;
    private $optionConfigs;
    private $optionConfigIndex;
    private $mutuallyExclusiveOptionGroupConfigs;
    private $argumentConfigs;
    private $isSubcommandEnabled;
    private $subcommandNames;
    private $subcommandConfigs = [];
    private $subcommandClasses = [];
    private $subcommandOptionConfigs = [];
    private $subcommandOptionConfigIndexes = [];
    private $subcommandMutuallyExclusiveOptionGroupConfigs = [];
    private $subcommandArgumentConfigs = [];

    /**
     * @param string $subcommandName
     * @return ArgumentConfig[]
     */
    public function getArgumentConfigs($subcommandName = null) {
        if ($subcommandName !== null
            && isset($this->subcommandArgumentConfigs[$subcommandName])
        ) {
            return $this->subcommandArgumentConfigs[$subcommandName];
        } elseif ($this->argumentConfigs !== null) {
            return $this->argumentConfigs;
        }
        if ($subcommandName === null && $this->isSubcommandEnabled()) {
            $this->argumentConfigs = [];
            return $this->argumentConfigs;
        }
        $configs = $this->get('arguments', $subcommandName);
        if ($configs === null) {
            $result = $this->getDefaultArgumentConfigs($subcommandName);
        } else {
            if (is_array($configs) === false) {
                throw new ConfigException(
                    $this->getErrorMessage(
                        $subcommandName,
                        "field 'arguments' must be an array, "
                            . gettype($configs) . ' given'
                    )
                );
            }
            $result = $this->parseArgumentConfigs($configs, $subcommandName);
        }
        if ($subcommandName !== null) {
            $this->subcommandArgumentConfigs[$subcommandName] = $result;
        } else {
            $this->argumentConfigs = $result;
        }
        return $result;
    }

    /**
     * @param string $subcommandName
     * @return string
     */
    public function getClass($subcommandName = null) {
        $class = null;
        if ($subcommandName !== null
            && isset($this->subcommandClasses[$subcommandName])
        ) {
            return $this->subcommandClasses[$subcommandName];
        } elseif ($this->class !== null) {
            return $this->class;
        }
        $class = $this->get('class', $subcommandName);
        if ($class === null) {
            $class = $this->getDefaultClass($subcommandName);
        }
        if (is_string($class) === false) {
            throw new ConfigException($this->getErrorMessage(
                $subcommandName,
                "field 'class' must be a string, "
                    . gettype($class) . ' given'
            ));
        }
        if ($subcommandName !== null) {
            $this->subcommandClasses[$subcommandName] = $class;
        } else {
            $this->class = $class;
        }
        return $class;
    }

    /**
     * @param string $subcommandName
     * @return OptionConfig[]
     */
    public function getOptionConfigs($subcommandName = null) {
        if ($subcommandName !== null
            && isset($this->subcommandOptionConfigs[$subcommandName])
        ) {
            return $this->subcommandOptionConfigs[$subcommandName];
        } elseif ($subcommandName === null && $this->optionConfigs !== null) {
            return $this->optionConfigs;
        }
        $configs = $this->get('options', $subcommandName);
        if ($configs === null) {
            $result = $this->getDefaultOptionConfigs($subcommandName);
        } elseif (is_array($configs)) {
            $result = $this->parseOptionConfigs($configs, $subcommandName);
        } else {
            throw new ConfigException($this->getErrorMessage(
                $subcommandName,
                "field 'options' must be an array, "
                    . gettype($configs) . ' given'
            ));
        }
        if ($subcommandName !== null) {
            $this->subcommandOptionConfigs[$subcommandName] = $result;
        } else {
            $this->optionConfigs = $result;
        }
        return $result;
    }

    /**
     * @param string $nameOrShortName
     * @param string $subcommandName
     * @return OptionConfig
     */
    public function getOptionConfig($nameOrShortName, $subcommandName = null) {
        $index = $this->getOptionConfigIndex($subcommandName);
        if (isset($index[$nameOrShortName])) {
            return $index[$nameOrShortName];
        }
    }

    /**
     * @param string $subcommandName
     * @return MutuallyExclusiveOptionGroupConfig[]
     */
    public function getMutuallyExclusiveOptionGroupConfigs(
        $subcommandName = null
    ) {
        if ($subcommandName !== null && isset(
            $this->subcommandMutuallyExclusiveOptionGroupConfigs[
                $subcommandName
            ]
        )) {
            return $this->subcommandMutuallyExclusiveOptionGroupConfigs[
                $subcommandName
            ];
        } elseif ($this->mutuallyExclusiveOptionGroupConfigs !== null) {
            return $this->mutuallyExclusiveOptionGroupConfigs;
        }
        $configs = $this->get(
            'mutually_exclusive_option_groups', $subcommandName
        );
        if ($configs !== null) {
            if (is_array($configs) === false) {
                throw new ConfigException($this->getErrorMessage(
                    $subcommandName,
                    "field 'mutually_exclusive_option_groups'"
                         . ' must be an array,' . gettype($configs) . ' given'
                ));
            }
            $result = $this->parseMutuallyExclusiveOptionGroupConfigs(
                $configs, $subcommandName
            );
        } else {
            $result = [];
        }
        if ($subcommandName !== null) {
            $this->subcommandMutuallyExclusiveOptionGroupConfigs[
                $subcommandName
            ] = $result;
        } else {
            $this->mutuallyExclusiveOptionGroupConfigs = $result;
        }
        return $result;
    }

    /**
     * @param string $subcommandName
     * @return string
     */
    public function getDescription($subcommandName = null) {
        return $this->get('description', $subcommandName);
    }

    /**
     * @return string
     */
    public function getName() {
        $result = $this->get('name');
        if ($result === null) {
            throw new ConfigException(
                "Command config error, field 'name' is required."
            );
        }
        if (is_string($result) === false) {
            throw new ConfigException($this->getErrorMessage(
                null,
                "field 'name' must be a string, " . gettype($result) . ' given'
            ));
        }
        return $result;
    }

    /**
     * @return string|float|int
     */
    public function getVersion() {
        $result = $this->get('version');
        if ($result === null) {
            return;
        }
        if (is_string($result) === false && is_float($result) === false
            && is_int($result) === false
        ) {
            throw new ConfigException($this->getErrorMessage(
                null,
                "field 'version' must be a string or a float or an int, "
                    . gettype($result) . ' given'
            ));
        }
        return $result;
    }

    /**
     * @return bool
     */
    public function isSubcommandEnabled() {
        if ($this->isSubcommandEnabled === null) {
            $this->isSubcommandEnabled = Config::getBool(
                'hyperframework.cli.enable_subcommand', false
            );
        }
        return $this->isSubcommandEnabled;
    }

    /**
     * @param string $subcommandName
     * @return bool
     */
    public function hasSubcommand($subcommandName) {
        return in_array($subcommandName, $this->getSubcommandNames(), true);
    }

    /**
     * @return string[]
     */
    public function getSubcommandNames() {
        if ($this->isSubcommandEnabled() === false) {
            return [];
        }
        if ($this->subcommandNames === null) {
            $this->subcommandNames = [];
            foreach (scandir($this->getSubcommandConfigRootPath()) as $file) {
                if (substr($file, -4) !== '.php') {
                    continue;
                }
                $name = substr($file, 0, strlen($file) - 4);
                $pattern = '/^[a-zA-Z0-9][a-zA-Z0-9-]*$/';
                if (preg_match($pattern, $name) === 1) {
                    $this->subcommandNames[] = $name;
                }
            }
        }
        return $this->subcommandNames;
    }

    /**
     * @param string $name
     * @param string $subcommandName
     * @return mixed
     */
    protected function get($name, $subcommandName = null) {
        $configs = $this->getAll($subcommandName);
        if (isset($configs[$name])) {
            return $configs[$name];
        }
    }

    /**
     * @param string $subcommandName
     * @return array
     */
    protected function getAll($subcommandName = null) {
        if ($subcommandName === null) {
            if ($this->configs !== null) {
                return $this->configs;
            }
        } elseif (isset($this->subcommandConfigs[$subcommandName])) {
            return $this->subcommandConfigs[$subcommandName];
        }
        if ($subcommandName !== null) {
            if ($this->hasSubcommand($subcommandName) === false) {
                throw new LogicException(
                    "Subcommand '$subcommandName' does not exist."
                );
            }
        }
        $configPath = $this->getConfigPath($subcommandName);
        if (file_exists($configPath) === false) {
            throw new ConfigException($this->getErrorMessage(
                $subcommandName, "config file '$configPath' does not exist."
            ));
        }
        $configs = require $configPath;
        if (is_array($configs) === false) {
            $type = gettype($configs);
            throw new ConfigException($this->getErrorMessage(
                $subcommandName,
                "config file '$configPath' must return an array,"
                    . " $type returned"
            ));
        }
        if ($subcommandName === null) {
            $this->configs = $configs;
        } else {
            $this->subcommandConfigs[$subcommandName] = $configs;
        }
        return $configs;
    }

    /**
     * @param string $subcommandName
     * @return string
     */
    protected function getDefaultClass($subcommandName = null) {
        $rootNamespace = Config::getAppRootNamespace();
        if ($subcommandName === null) {
            $class = 'Command';
            if ($rootNamespace !== '' && $rootNamespace !== '\\') {
                NamespaceCombiner::prepend($class, $rootNamespace);
            }
        } else {
            $tmp = ucwords(str_replace('-', ' ', $subcommandName));
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

    /**
     * @param string $subcommandName
     * @return ArgumentConfig[]
     */
    protected function getDefaultArgumentConfigs($subcommandName = null) {
        $class = $this->getClass($subcommandName);
        if (method_exists($class, 'execute') === false) {
            if (class_exists($class) === false) {
                throw new ClassNotFoundException(
                    $this->getErrorMessageOfFailedToGetDefaultArgumentConfigs(
                        $subcommandName,
                        "class '$class' does not exist"
                    )
                );
            }
            throw new MethodNotFoundException(
                $this->getErrorMessageOfFailedToGetDefaultArgumentConfigs(
                    $subcommandName,
                    "method '$class::execute' does not exist"
                )
            );
        }
        $method = new ReflectionMethod($class, 'execute');
        $params = $method->getParameters();
        $result = [];
        $hasArray = false;
        foreach ($params as $param) {
            if ($hasArray) {
                throw new LogicException(
                    $this->getErrorMessageOfFailedToGetDefaultArgumentConfigs(
                        $subcommandName,
                        "argument list of method '$class::execute' is invalid,"
                            . " array argument must be the last one"
                    )
                );
            }
            if ($param->isArray()) {
                $hasArray = true;
            }
            $result[] = new DefaultArgumentConfig($param);
        }
        return $result;
    }

    /**
     * @param string $subcommandName
     * @return OptionConfig[]
     */
    protected function getDefaultOptionConfigs($subcommandName = null) {
        $configs = [['name' => 'help', 'short_name' => 'h']];
        if ($subcommandName === null && $this->getVersion() !== null) {
            $configs[] = ['name' => 'version'];
        }
        return $this->parseOptionConfigs($configs, $subcommandName);
    }

    /**
     * @param array $configs
     * @param string $subcommandName
     * @return ArgumentConfig[]
     */
    protected function parseArgumentConfigs(
        array $configs, $subcommandName = null
    ) {
        return ArgumentConfigParser::parse($configs, $subcommandName);
    }

    /**
     * @param array $configs
     * @param string $subcommandName
     * @return OptionConfig[]
     */
    protected function parseOptionConfigs($configs, $subcommandName = null) {
        return OptionConfigParser::parse(
            $configs, $this->isSubcommandEnabled(), $subcommandName
        );
    }

    /**
     * @param array $configs
     * @param string $subcommandName
     * @return MutuallyExclusiveOptionGroupConfig[]
     */
    protected function parseMutuallyExclusiveOptionGroupConfigs(
        $configs, $subcommandName = null
    ) {
        return MutuallyExclusiveOptionGroupConfigParser::parse(
            $configs,
            $this->getOptionConfigIndex($subcommandName),
            $this->isSubcommandEnabled(),
            $subcommandName
        );
    }

    /**
     * @param string $subcommandName
     * @return string
     */
    private function getConfigPath($subcommandName) {
        if ($subcommandName === null) {
            $configPath = Config::getString(
                'hyperframework.cli.command_config_path', ''
            );
            if ($configPath === '') {
                $configPath = 'command.php';
            }
            if (FileFullPathRecognizer::isFullPath($configPath) === false) {
                $configPath = ConfigFileFullPathBuilder::build($configPath);
            }
            return $configPath;
        } else {
            return $this->getSubcommandConfigPath($subcommandName);
        }
    }

    /**
     * @param string $subcommandName
     * @return string
     */
    private function getSubcommandConfigPath($subcommandName) {
        return $this->getSubcommandConfigRootPath()
            . DIRECTORY_SEPARATOR . $subcommandName . '.php';
    }

    /**
     * @return string
     */
    private function getSubcommandConfigRootPath() {
        $folder = Config::getString(
            'hyperframework.cli.subcommand_config_root_path'
        );
        if ($folder === null) {
            $folder = 'subcommands';
        }
        return ConfigFileFullPathBuilder::build($folder);
    }

    /**
     * @param string $subcommandName
     * @return OptionConfig[]
     */
    private function getOptionConfigIndex($subcommandName = null) {
        if ($subcommandName !== null
            && isset($this->subcommandOptionConfigIndexes[$subcommandName])
        ) {
            return $this->subcommandOptionConfigIndexes[$subcommandName];
        } elseif ($subcommandName === null
            && $this->optionConfigIndex !== null
        ) {
            return $this->optionConfigIndex;
        }
        $configs = $this->getOptionConfigs($subcommandName);
        $result = [];
        foreach ($configs as $config) {
            if ($config->getName() !== null) {
                $result[$config->getName()] = $config;
            }
            if ($config->getShortName() !== null) {
                $result[$config->getShortName()] = $config;
            }
        }
        if ($subcommandName !== null) {
            $this->subcommandOptionConfigIndexes[$subcommandName] = $result;
        } else {
            $this->optionConfigIndex = $result;
        }
        return $result;
    }

    /**
     * @param string $subcommandName
     * @param string $extra
     * @return string
     */
    private function getErrorMessage($subcommandName, $extra) {
        if ($subcommandName === null) {
            if ($this->isSubcommandEnabled()) {
                $result = 'Global command';
            } else {
                $result = 'Command';
            }
        } else {
            $result = "Subcommand '$subcommandName'";
        }
        return $result . ' config error, ' . $extra . '.';
    }

    /**
     * @param string $subcommandName
     * @param string $extra
     * @return string
     */
    private function getErrorMessageOfFailedToGetDefaultArgumentConfigs(
        $subcommandName, $extra
    ) {
        $result = 'Failed to get ';
        if ($subcommandName !== null) {
            $result .=
                 "default argument configs of subcommand '$subcommandName'";
        } else {
            if ($this->isSubcommandEnabled()) {
                $result .= 'default argument configs of global command';
            } else {
                $result .= 'command default argument configs';
            }
        }
        return $result . ', ' . $extra . '.';
    }
}
