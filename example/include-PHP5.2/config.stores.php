<?php
return array(
    'stores' => array(
        'mysql' => array(
            'classname' => 'Mysql',
            'host' => '127.0.0.1',
            'port' => 3306,
            'user' => 'lay',
            'password' => '123456',
            'database' => 'laysoft',
            'encoding' => 'UTF8',
            'showsql' => true
        ),
        'pdomysql' => array(
            'classname' => 'PdoStore',
            'host' => '127.0.0.1',
            'port' => 3306,
            'user' => 'lay',
            'password' => '123456',
            'database' => 'laysoft',
            'encoding' => 'UTF8',
            'showsql' => true
        )
    )
);
?>