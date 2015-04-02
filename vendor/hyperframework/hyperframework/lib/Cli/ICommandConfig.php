<?php
namespace Hyperframework\Cli;

interface ICommandConfig {
    /**
     * @param string $subcommandName
     * @return ArgumentConfig[]
     */
    function getArgumentConfigs($subcommandName = null);

    /**
     * @param string $subcommandName
     * @return string
     */
    function getClass($subcommandName = null);

    /**
     * @param string $subcommandName
     * @return OptionConfig[]
     */
    function getOptionConfigs($subcommandName = null);

    /**
     * @param string $nameOrShortName
     * @param string $subcommandName
     * @return OptionConfig
     */
    function getOptionConfig($nameOrShortName, $subcommandName = null);

    /**
     * @param string $subcommandName
     * @return MutuallyExclusiveOptionGroupConfig[]
     */
    function getMutuallyExclusiveOptionGroupConfigs(
        $subcommandName = null
    );

    /**
     * @param string $subcommandName
     * @return string
     */
    function getDescription($subcommandName = null);

    /**
     * @return string
     */
    function getName();

    /**
     * @return string|float|int
     */
    function getVersion();

    /**
     * @return bool
     */
    function isSubcommandEnabled();

    /**
     * @param string $subcommandName
     * @return bool
     */
    function hasSubcommand($subcommandName);

    /**
     * @return string[]
     */
    function getSubcommandNames();
}
