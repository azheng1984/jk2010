<?php
return array(
    'description' => 'Hyperframework CLI Tool 0.2.1',
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
