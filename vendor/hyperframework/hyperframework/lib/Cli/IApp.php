<?php
namespace Hyperframework\Cli;

interface IApp {
    /**
     * @return string[]
     */
    function getArguments();

    /**
     * @param string $name
     * @return bool
     */
    function hasOption($name);

    /**
     * @param string $name
     * @return string
     */
    function getOption($name);

    /**
     * @return string[]
     */
    function getOptions();

    /**
     * @return ICommandConfig
     */
    function getCommandConfig();
}