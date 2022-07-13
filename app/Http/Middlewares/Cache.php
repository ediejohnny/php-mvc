<?php

namespace App\Http\Middlewares;

use \App\Utils\Cache\File as CacheFile;

class Cache {
  /**
   * Método responsável por verificar se a request é cacheável.
   * @param Request $request
   * @return boolean
   */
  public function isCacheable($request) {
    // Valida o tempo de cache
    if(getenv('CACHE_TIME') <= 0) {
      return false;
    }

    // Valida o método da requisição
    if($request->getHttpMethod() != 'GET') {
      return false;
    }

    // Valida o header de cache
    $headers = $request->getHeaders();
    if(isset($headers['Cache-Control']) && $headers['Cache-Control'] == 'no-cache') {
      return false;
    }

    return true;
  }

  /**
   * Método responsável por retornar a hash do cache
   */
  private function getHash($request) {
    // URI da rota
    $uri = $request->getRouter()->getUri();

    // Query params
    $queryParams = $request->getQueryParams();
    $uri .= !empty($queryParams) ? '?' . http_build_query($queryParams) : '';

    // Remove caracteres não alfanuméricos e retorna a hash
    return preg_replace('/[^a-z0-9]/i', '', ltrim($uri, '/'));
  }

  /**
   * Método responsável por executar o middleware
   * @param Request $request
   * @param Closure $next
   * @return Response
   */
  public function handle($request, $next) {
    // Verifica se a request atual é cacheável
    if(!$this->isCacheable($request)) {
      return $next($request);
    }

    // Obtém o hash da requisição
    $hash = $this->getHash($request);

    // Retorna o conteúdo do cache
    return CacheFile::getCache($hash, getenv('CACHE_TIME'), function() use($request, $next) {
      return $next($request);
    });
  }
}
