<?php
// FRONT CONTROLLER

// Mostrar en pantalla siempre que ocurran errores
// esto es para developing, no para production
// ini_set('display_errros', 1); // inicializa variables de php para mostrar los errores
// ini_set('display_starup_errror', 1); // encender los errores
// error_reporting(E_ALL); // E_ALL: todos los errores

const BASE_ROUTE = '';

require_once '../vendor/autoload.php';

session_start();

$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__.'/..');
$dotenv->load();

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => getenv('DB_HOST'),
    'database'  => getenv('DB_DATABASE'),
    'username'  => getenv('DB_USERNAME'),
    'password'  => getenv('DB_PASSWORD'),
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();

include('routes.php');
