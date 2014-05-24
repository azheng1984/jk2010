<?php
return array(
    'description' => 'Hyperframework CLI Tool 0.2.1',
    'commands' => array(
        'build' => array(
            'description' => 'Build application',
            'class' => 'BuildCommand',
        ),
        'new' => array(
            'description' => 'Create scaffold',
            'class' => 'NewCommand',
        ),
    ),
);
