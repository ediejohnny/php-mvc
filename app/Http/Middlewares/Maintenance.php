<?php

namespace App\Http\Middlewares;

class Maintenance {

  /**
   * Método responsável por executar o middleware
   * @param Request $request
   * @param Closure $next
   * @return Response
   */
  public function handle($request, $next) {
    // Se o sistema estiver em manutenção, retorna status 503
    if(getenv('MAINTENANCE') == 'true') {
      throw new \Exception("Página em manutenção. Tente novamente mais tarde.", 503);
    }

    // Executa o próximo nível do middleware
    return $next($request);
  }

}