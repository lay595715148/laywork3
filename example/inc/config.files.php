<?php
return array(
    'debug' => array(1|2|8, 8),
    'files' => array(
        '/laywork/example/inc/config.actions.php',
        '/laywork/example/inc/config.stores.php',
        '/laywork/example/inc/config.services.php',
        '/laywork/example/inc/config.beans.php',
        '/laywork/example/inc/config.prefaces.php',
        '/laywork/example/inc/config.templates.php'
    ),
    'actions' => array(
        'out2' => array(
            'classname' => 'cn\laysoft\laywork\demo\DemoAction',
            'services' => array('in', 'out')
        )
    ),
    0 => array(
        1,2,3,4,5,6
    )
);
?>