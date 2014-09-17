<?php
return array(
    'name' => 'hf',
    'version' => '1.1.1',
    'description' => 'Hyperframework CLI Tool',
    'usage' => 'main (-x | -x | -x)',
    'options' => array(
        'name-of-option' => 'message',
        '-x, --name-of-option[<arg-name>]' => array(
            'class' => 'xx',
            'callback' => 'xx',
            'description' => 'x'
        ),
        'l,name-of-option[=<arg-key>]' => 'message',
    ),
    'commands' => array(
        'hello' => array(
//            'alias' => 'shit',
            'description' => 'Build application',
            'class' => '\Tc\App\HelloCommand',
            'option' => array(
                'hi' => array('short' => 'h', 'class' => '\Tc\TestOption', 'infinite'),
                'hi2' => array('short' => 2, 'description' => 'hello hi2'),
                'flatoption',
            ),
//            'commands' => array(
//                'dis' => array(
//                    'description' => 'hi',
//                    'class' => '\Tc\App\HelloCommand',
//                    'commands' => array(
//                        'dis' => array(
//                            'description' => 'hi',
//                            'class' => '\Tc\App\HelloCommand',
//                        )                
//                    )
//                ),
//
//            )
        ),
    ),
    'option' => array('hi'),
);
