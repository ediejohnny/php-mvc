<?php

require __DIR__.'/includes/config.php';
use \App\Http\Router;

// Objeto da rota
$obRouter = new Router(URL);

// Inclui arquivo de rotas
include __DIR__.'/routes/pages.php';
include __DIR__.'/routes/api.php';

// Imprime o response da rota
$obRouter->run()
         ->sendResponse();