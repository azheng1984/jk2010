<?php
namespace Hyperframework\Cli;

interface IMultipleCommandApp extends IApp {
    function getGlobalOptions();
    function getGlobalOption($name);
    function hasGlobalOption($name);
    function hasSubcommand();
    function getSubcommandName();
}