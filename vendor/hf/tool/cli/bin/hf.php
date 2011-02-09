#!/usr/bin/php
<?php
define('ROOT_PATH', dirname(dirname(__FILE__)).'/');
define('HF_PATH', dirname(dirname(ROOT_PATH)).'/');
define('HF_WEB_PATH', HF_PATH.'web/');
define('HF_CLI_PATH', HF_PATH.'cli/');
define('HF_CONFIG_PATH', ROOT_PATH.'config/');

$path = str_replace('\\', '/', get_include_path().
';'.ROOT_PATH.'lib'.
';'.ROOT_PATH.'lib/Command'.
';'.HF_CLI_PATH.'lib'.
';'.HF_CLI_PATH.'lib/Parser'.
';'.HF_WEB_PATH.'lib');
set_include_path($path);

function __autoload($name) {
  require "$name.php";
}

print_r($argv);
exit;

$_ENV['context'] = new CommandContext;
$_ENV['command_parser'] = new CommandContext;
$_ENV['command_parser']->run();