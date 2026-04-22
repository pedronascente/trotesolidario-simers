<?php
$dbHost = getenv('DB_HOST') ?: 'mysql-db';
$dbPort = getenv('DB_PORT') ?: '3306';
$dbName = getenv('DB_DATABASE') ?: 'trotesolidario';
$dbUser = getenv('DB_USERNAME') ?: 'trote';
$dbPassword = getenv('DB_PASSWORD') ?: 'trote';

return [
    'class' => 'yii\db\Connection',
    'dsn' => "mysql:host={$dbHost};port={$dbPort};dbname={$dbName}",
    'username' => $dbUser,
    'password' => $dbPassword,
    'charset' => 'utf8',
];
