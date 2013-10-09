<?php
return array(
    'debug' => array(1|2|8, 8),
    'files' => array(
        '/laywork/example/include/config.actions.php',
        '/laywork/example/include/config.stores.php',
        '/laywork/example/include/config.services.php',
        '/laywork/example/include/config.beans.php',
        '/laywork/example/include/config.prefaces.php',
        '/laywork/example/include/config.templates.php'
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