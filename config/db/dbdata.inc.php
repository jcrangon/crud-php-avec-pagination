<?php 
$dbdata = [
    'driver' => 'mysql',
    'server' =>  'localhost',
    'base' => 'entreprise',
    'port' => 3306,
    'user' => '',
    'pass' => '',
    'charset' => 'utf8mb4',
    'options' => [
        // PDO::MYSQL_ATTR_INIT_COMMANDE => 'SET NAMES utf8mb4',
        // PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO:: ATTR_EMULATE_PREPARES => false,
    ]
];