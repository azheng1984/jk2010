<?php
namespace Hyperframework\Cli;

interface IMultipleCommandApp extends IApp {
    /**
     * @return string[]
     */
    function getGlobalOptions();

    /**
     * @param string $name
     * @return string
     */
    function getGlobalOption($name);

    /**
     * @param string $name
     * @return bool
     */
    function hasGlobalOption($name);

    /**
     * @return bool
     */
    function hasSubcommand();

    /**
     * @return string
     */
    function getSubcommandName();
}
