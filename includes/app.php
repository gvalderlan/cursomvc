<?php
require __DIR__.'/../vendor/autoload.php';

use \App\Utils\View;
use \WilliamCosta\DotEnv\Environment;
use \WilliamCosta\DatabaseManager\Database;
use \App\Http\Middleware\Queue as MiddlewareQueue;

//Carrega variaveis de ambiente
Environment::load(__DIR__.'/../');

//Define as configurações de BD
Database::config(
    getenv('DB_HOST'),
    getenv('DB_NAME'),
    getenv('DB_USER'),
    getenv('DB_PASS'),
    getenv('DB_PORT')
);

//Deifine a constante de URL
define('URL', getenv('URL'));

//Define o valor padrão das variáveis
View::init([
    'URL' => URL
]);

//DEFINE O MAPEAMENTO DOS MIDDLEWARES
MiddlewareQueue::setMap([
    'maintenance' => \App\Http\Middleware\Maintenance::class,
    'required-admin-logout'=> \App\Http\Middleware\RequireAdminLogout::class,
    'required-admin-login'=> \App\Http\Middleware\RequireAdminLogin::class
]);

//DEFINE O MAPEAMENTO DE MIDDLEWARES PDRÕES (EXECUTA EM TODAS AS ROTAS)
MiddlewareQueue::setDefault([
    'maintenance'
]);