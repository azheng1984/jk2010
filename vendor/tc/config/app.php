<?php
return array(
    'name' => 'hf',
    'namespace' => 'Tc',
    'version' => '1.1.1',
    'description' => 'Hyperframework CLI Tool',
    'usage' => array(
        '--main --opt[=<arg>] (--opt1 | --opt2 | --opt3) <arg>',
        '[options] [<arg>]...',
    ), //optional, [options] <argument_name_from_command> or [options] <command> [...]
    //可以设置 generate_usage = false 配置，或 usage => false 来禁用
    'options' => array(
        'name-of-option',
        'name-of-option2' => 'description',
        'n, name-of-option3[=<arg-name>]' => array(
            'class' => 'xx',
            'description' => 'x',
            'multiple',
        ),
        'name-of-option[=<arg-key>]' => 'message',
    ),
//    'class' => 'Command', //default
    'subcommands' => array( //none(subcommands = 'subcommands')
        //subcommands or subcommands = true or subcommands = 'path'
        'hello' => array( //inline. lazy load is supported
//          'alias' => 'shit',
            'usage' => '',
            'description' => 'Build application',
//          'class' => 'HelloCommand', //default
            'options' => array(
                'h, hi' => array('multiple'),
                'hi2' => array('description' => 'hello hi2'),
                'flatoption',
            ),
        ),
    ),
);
