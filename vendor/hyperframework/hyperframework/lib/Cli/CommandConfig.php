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
    private $mutuallyExclusiveOptionGroup;
    private $arguments;
    private $subcommandConfigs = [];
    private $subcommandClasses = [];
    private $subcommandOptionConfigs = [];
    private $subcommandMutuallyExclusiveOptionGroups = [];
    private $subcommandArguments = [];

    public function getArguments($subcommand = null) {
        $commandConfig->getArguments();
        foreach ($arguments as $argument) {
        }
        $optionGroup = $commandConfig->getMutuallyExclusiveOptionGroups();
        $optionGroup->isRequired();
        foreach ($configs as $config) {
        }
        $optionConfig = $commandConfig->getMutuallyExclusiveOptions();
        $optionConfig->isRequired();
        $options = $optionConfig->getOptions();
        $commandConfig->getMutuallyExclusiveOptionsByOption();
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
        if ($class !== '') {
            if ($class[0] === '\\') {
                $class = substr($class, 1);
            } else {
                $namespace = (string)Config::get(
                    'hyperframework.cli.command_root_namespace'
                );
                if ($namespace === '') {
                    $namespace = (string)Config::get(
                        'hyperframework.app_root_namespace'
                    );
                }
                if ($subcommand !== null) {
                    if ($namespace === '') {
                        $namespace = 'Subcommands';
                    } else {
                        $namespace .= '\Subcommands';
                    }
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
            $options = $this->parseOptionConfigs($config);
            if ($options === null) {
                $options = [];
            }
        } else {
            $options = [];
        }
        $defaultConfigs = $this->getDefaultOptionConfigs($subcommand);
        $defaultOptions = $this->parseOptionConfigs($defaultConfigs);
        foreach ($defaultOptions as $key => $value) {
            if (isset($options[$key]) === false) {
                $options[$key] = $value;
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
        $optionConfigs = $commandConfig->getOptionConfigs();
        if ($subcommand !== null
            && isset($this->subcommandMutuallyExclusiveOptions[$subcommand])
        ) {
            return $this->subcommandMutuallyExclusiveOptions[$subcommand];
        } elseif ($this->mutuallyExclusiveOptions !== null) {
            return $this->mutuallyExclusiveOptions;
        }
        $mutuallyExclusiveOptionConfigs = $this->get('options', $subcommand);
        if ($mutuallyExclusiveOptionConfigs !== null) {
            $mutuallyExclusiveOptions = $this->parseOptionConfigs($mutuallyExclusiveOptionConfigs);
            if ($mutuallyExclusiveOptions === null) {
                $mutuallyExclusiveOptions = [];
            }
        } else {
            $options = [];
        }
        if ($subcommand !== null) {
            $this->subcommandArguments[$subcommand] = $arguments;
        } else {
            $this->arguments = $arguments;
        }
        return $arguments;
    }

    public function getMutuallyExclusiveOptionGroupByOption(
        $option, $subcommand = null
    ) {
        $configs = $this->getMutuallyExclusiveOptionGroups($subcommand);
        foreach ($configs as $config) {
            if (in_array($option, $config->getOptions(), true)) {
                return $config;
            }
        }
    }

    protected function parseMutuallyExclusiveOptionConfigs($config) {
        $configs = $this->get('mutually_exclusive_options', $subcommand);
        if ($configs === null) {
            return;
        }
        if (is_array(current($configs) ===  false) {
            $configs = [$configs];
        }
        $results = [];
        $includedOptions = [];
        foreach ($configs as $childConfigs) {
            foreach ($childConfigs as $config) {
                $isRequired = false;
                $shouldIncludeAll = false;
                $options = [];
                foreach ($config as $item) {
                    $item = (string)$item;
                    if ($item === 'all') {
                        $shouldIncludeAll = true;
                        if (count($includedOptions) !== 0) {
                            throw new Exception;
                        }
                        foreach ($options as $option) {
                            $name = $option->getName();
                            if ($name === null) {
                                $name = $option->getShortName();
                            }
                            if (in_array($name, $includedOptions)) {
                                continue;
                            }
                            $includedOptions[] = $name;
                            $options[] = $option;
                        }
                        continue;
                    }
                    if ($item === 'required') {
                        $isRequired = true;
                        continue;
                    }
                    if (isset($options[$item]) === false) {
                        //check full format
                        if ($item === '' || $item[0] !== '-') {
                            throw new Exception;
                        }
                        throw new Exception;
                    }
                    if ($shouldIncludeAll) {
                        throw new Exception;
                    }
                    $name = $option->getName();
                    if ($name === null) {
                        $name = $option->getShortName();
                    }
                    if (in_array($name, $includedOptions)) {
                        throw new Exception;
                    }
                    $includedOptions[] = $name;
                    $option = $options[$item];
                }
                $result[] = new MultualExclusiveOptionConfig(
                    $options, $isRequired
                );
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
        return file_exists($this->getSubcommandConfigPath($name));
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
        return $results;
    }

    protected function getDefaultClass($subcommand = null) {
        if ($subcommand === null) {
            return 'Command';
        }
        $tmp = ucwords(str_replace('-', ' ', $subcommand));
        return str_replace(' ', '', $tmp) . 'Command';
    }

    protected function parseOptionConfigs(array $config) {
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
