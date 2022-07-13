<?php
require __DIR__ . '/../vendor/autoload.php';

use \App\Utils\View;
use \App\Utils\Environment;
use \App\Utils\Database;
use \App\Http\Middlewares\Queue as MiddlewaresQueue;

Environment::load(__DIR__.'/../');

// Define as configurações do banco de dados
Database::config(
  getenv('DB_HOST'),
  getenv('DB_NAME'),
  getenv('DB_USER'),
  getenv('DB_PASS'),
  getenv('DB_PORT')
);

// Define a constante de URL do projeto
define('URL', getenv('URL'));

// Define valor padrão das variaveis
View::init([
    'URL' => URL
]);

// Define o mapeamento de middlewares
MiddlewaresQueue::setMap([
    'maintenance' => \App\Http\Middlewares\Maintenance::class,
    'cache' => \App\Http\Middlewares\Cache::class,
    'api' => \App\Http\Middlewares\Api::class
  ]);

// Define o mapeamento de middlewares padrões (executados em todas as rotas)
MiddlewaresQueue::setDefault([
    'maintenance'
]);
