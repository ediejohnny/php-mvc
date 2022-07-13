<?php

namespace App\Http\Middlewares;

class Queue {

  /**
   * Mapeamento de middlewares
   * @var array
   */
  private static $map = [];

  /**
   * Mapeamento de middlewares que serão executados em todas as rotas
   * @var array
   */
  private static $default = [];

  /**
   * Fila de middlewares a serem executados
   * @var array
   */
  private $middlewares = [];

  /**
   * Função de execução do controlador
   * @var Callable
   */
  private $controller;

  /**
   * Argumentos da função do controlador
   * @var array
   */
  private $controllerArgs = [];

  /**
   * Método responsável por construir a classe de fila de middlewares
   * @param array $middlewares
   * @param Callable $controller
   * @param array $controllerArgs
   */
  public function __construct($middlewares, $controller, $controllerArgs) {
    $this->middlewares = array_merge(self::$default, $middlewares);
    $this->controller = $controller;
    $this->controllerArgs = $controllerArgs;
  }

  /**
   * Método responsável por definir o mapeamento de middlewares
   * @param array $map
   */
  public static function setMap($map) {
    self::$map = $map;
  }

  /**
   * Método responsável por definir o mapeamento de middlewares padrões
   * @param array $default
   */
  public static function setdefault($default) {
    self::$default = $default;
  }

  /**
   * Método responsável por executar a fila de middlewares
   * @param Request $request
   * @return Response
   */
  public function next($request) {
    // Se não existir middlewares, executa o controlador
    if(empty($this->middlewares)) {
      return call_user_func_array($this->controller, $this->controllerArgs);
    }

    // Pega o primeiro middleware da fila
    $middleware = array_shift($this->middlewares);
    
    // Verifica se o middleware existe
    if(!isset(self::$map[$middleware])) {
      throw new \Exception("Problemas ao processar o middleware da aplicação.", 500);
    }

    // Instância de queue para usar na função anônima
    $queue = $this;

    // Criar a função anônima de next
    $next = function($request) use ($queue) {
      return $queue->next($request);
    };

    // Executa o middleware
    return (new self::$map[$middleware])->handle($request, $next);

  }

}