<?php

use \App\Http\Response;
use \App\Controller\Api;

$obRouter->get('/api/v1/test', [
  function($request) {
    return new Response(200, Api\Test::test($request), 'application/json');
  }
]);

$obRouter->get('/api/v1/test/{testValue}', [
  'middlewares' => [
    'api'
  ],
  function($request, $testValue) {
    return new Response(200, Api\Test::test($request, $testValue), 'application/json');
  }
]);