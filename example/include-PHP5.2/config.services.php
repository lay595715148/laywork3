<?php
return array(
    'services' => array(
        'in' => array(
            'classname' => 'DemoService',
            'bean' => 'in',
            'store' => 'mysql'
        ),
        'out' => array(
            'classname' => 'DemoService',
            'bean' => 'in',
            'store' => 'pdomysql'
        )
    )
);
?>