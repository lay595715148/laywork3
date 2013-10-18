<?php
return array(
    'actions' => array(
        'index' => array(
            'classname' => 'DemoAction',
            'services' => array('in', 'out'),
            'preface' => 'out'
        ),
        'in2' => array(
            'classname' => 'DemoAction',
            'services' => array('in', 'out'),
            'preface' => 'out'
        )
    ),
    'actions.out' => array(
            'classname' => 'DemoAction',
            'services' => array('in', 'out')
    )
);
?>