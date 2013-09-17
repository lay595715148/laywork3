<?php
return array(
    'actions' => array(
        'in' => array(
            'classname' => 'DemoAction',
            'services' => array('in', 'out')
        )
    ),
    'actions.out' => array(
            'classname' => 'DemoAction',
            'services' => array('in', 'out')
    )
);
?>