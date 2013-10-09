<?php
return array(
    'debug' => array(1|8, 8),
    'files' => array(
        '/laywork/inc/config.actions.php',
        '/laywork/inc/config.stores.php',
        '/laywork/inc/config.services.php',
        '/laywork/inc/config.beans.php',
        '/laywork/inc/config.prefaces.php',
        '/laywork/inc/config.templates.php'
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