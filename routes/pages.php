<?php 


use App\Controller\Pages;
use App\Http\Response;

//ROTA HOME
$obRouter->get('/', [
  function() {
    return new Response(200, Pages\Home::getHome());
  }
]);

//ROTA SOBRE
$obRouter->get('/about', [
  'middlewares' => [
    'cache'
  ],
  function() {
    return new Response(200, Pages\About::getAbout());
  }
]);

//ROTA DINAMICA
$obRouter->get('/pagina/{idPagina}', [
    function($idPagina) {
        return new Response(200, 'Pagina '.$idPagina);
    }
]);