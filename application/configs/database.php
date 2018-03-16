<?php

$conf    =       array('database' => array(              'adapter' => 'pdo_mysql',
                                                         'params'  => array(     'host'=>'localhost',
                                                                                 'dbname'=>'sww',
                                                                                 'username'=>'root',
                                                                                 'password' => 'hallo'),
                                                         'options' => array(),
                                                         'driver_options' => array(array(PDO::ERRMODE_EXCEPTION => true))
                                         ));

return $conf;
?>