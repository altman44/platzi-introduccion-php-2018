<?php
// FRONT CONTROLLER

// Mostrar en pantalla siempre que ocurran errores
// esto es para developing, no para production
// ini_set('display_errros', 1); // inicializa variables de php para mostrar los errores
// ini_set('display_starup_errror', 1); // encender los errores
// error_reporting(E_ALL); // E_ALL: todos los errores

require_once '../vendor/autoload.php';

session_start();

$db_driver = '';
$db_host = '';
$db_database = '';
$db_username = '';
$db_password = '';

try {
    $dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__.'/..');
    $dotenv->load();
    $db_driver = getenv('DB_DRIVER');
    $db_host = getenv('DB_HOST');
    $db_database = getenv('DB_DATABASE');
    $db_username = getenv('DB_USERNAME');
    $db_password = getenv('DB_PASSWORD');
} catch (Exception $err) {
    $db_driver = $_SERVER['DB_DRIVER'];
    $db_host = $_SERVER['DB_HOST'];
    $db_database = $_SERVER['DB_DATABASE'];
    $db_username = $_SERVER['DB_USERNAME'];
    $db_password = $_SERVER['DB_PASSWORD'];
}

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => $db_driver,
    'host'      => $db_host,
    'database'  => $db_database,
    'username'  => $db_username,
    'password'  => $db_password,
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
    'sslmode' => 'require'
]);
// $capsule->addConnection([
//     'driver'    => 'pgsql',
//     'host'      => 'ec2-23-20-168-40.compute-1.amazonaws.com',
//     'database'  => 'd3m5ebrg7il9b8',
//     'username'  => 'kkjmgsrbflcmib',
//     'password'  => '00b2f3def22567084024d60724c07ff417b6a0f7969b4dd740f1ec0343a8d80e',
//     'charset'   => 'utf8',
//     'collation' => 'utf8_unicode_ci',
//     'prefix'    => '',
//     'sslmode' => 'require'
// ]);

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();

include('routes.php');
