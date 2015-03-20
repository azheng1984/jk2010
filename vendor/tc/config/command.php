<?php
return [
    'name' => 'gitxcx',
    'version' => '2.2.2',
    'options' => [
        '--hello=(adf)' => 'dsdas jfoa fsdiofjsa ojfdsfaoidf sd fosdjofasdf ',
        '-x, --xx-dsafasdf' => [
            'name' => 'xxx',
            'short_name' => 'x',
            'required' => true,
            'description' => 'description xx'
        ],
        '--xxx[=arg]',
        '-t[arg][xx]',
        '-g[arg]',
        '-b ,    --axx-dsafasdf=<arg>' => 'xx',
        '-c ,    --cxx-dsafasdf[=<arg>]',
        '-d,     --ddxx-dsafasdf=[<key>:]<value>' => 'dfsdf f fsdaf sdf ',
        '-h,   --help' => 'show help message'
    ],
    'mutually_exclusive_option_groups' => [
        '-t', '-g', '--xx-dsafasdf'
    ]
];
