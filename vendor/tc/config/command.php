<?php
return array(
    'name' => 'hf',
    'namespace' => 'Tc',
    'version' => '1.1.1',
    'description' => 'Hyperframework CLI Tool',
    'usage' => array(
        'usage_name' => '--main --opt[=<arg>] (--opt1 | --opt2 | --opt3) <arg>',
        '--main --opt[=<arg>] (--opt1 | --opt2 | --opt3) <arg>',
        '[options] [<arg>]...',
        '[options] [<command>]',
    ),
    //最简单的配置，逻辑尽量用代码写
    //  xxxx cmd -xxd lai
    'options' => array(
        array(
            'group name',
            array(
                '--name' => ''
            )
        ),
        '-x, --opt[=(a|b|c)]',
        '--name-of-option',
        '--name-of-option2' => 'description',
        '-n, --name-of-option3[=<arg-name>]' => array(
            'description' => 'x',
            'multiple',
        ),
        '--name-of-option[=<arg-key>]' => 'message',
    ),
//    'class' => 'Command', //default
    'subcommands' => array( //none(subcommands = 'subcommands')
        //subcommands or subcommands = true or subcommands = 'path'
        'hello' => array( //inline. lazy load is supported
//          'alias' => 'hill',
            'usage' => '',
            'description' => 'Build application',
//          'class' => 'HelloCommand', //default
            'options' => array(
                '-h, --hi' => array('multiple'),
                '--hi2' => array('description' => 'hello hi2'),
                '--flatoption',
                '--global-option' //display global option
            ),
        ),
    )
);
