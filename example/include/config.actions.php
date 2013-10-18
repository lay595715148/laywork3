<?php
return array(
    'actions' => array(
        'index' => array(
            'classname' => 'cn\laysoft\laywork\demo\DemoAction',
            'services' => array('in', 'out'),
            'preface' => 'out'
        ),
        'in2' => array(
            'classname' => 'cn\laysoft\laywork\demo\DemoAction',
            'services' => array('in', 'out'),
            'preface' => 'out'
        )
    ),
    'actions.out' => array(
            'classname' => 'cn\laysoft\laywork\demo\DemoAction',
            'services' => array('in', 'out')
    )
);
?>