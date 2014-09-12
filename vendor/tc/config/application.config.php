<?php
return array(
    'description' => 'Hyperframework CLI Tool 0.2.1',
    'commands' => array(
        'hello' => array(
            'description' => 'Build application',
            'class' => '\Tc\App\HelloCommand',
            'option' => array(
                'hi' => array('short' => 'h', 'class' => '\Tc\TestOption', 'infinite'),
                'hi2' => array('short' => 2, 'description' => 'hello hi2'),
                'flatoption',
            ),
        ),
    ),
    'package' => array(
        'description' => 'sub key',
        'commands' => array(
            'hello' => array(
                'description' => 'Build application',
                'class' => '\Tc\App\HelloCommand',
                'option' => array(
                    'hi' => array('short' => 'h', 'class' => '\Tc\TestOption', 'infinite'),
                    'hi2' => array('short' => 2, 'description' => 'hello hi2'),
                    'flatoption',
                ),
            ),
        ),
    )
);
