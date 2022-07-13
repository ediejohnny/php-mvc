<?php

namespace App\Http;

use App\Http\Request;
use App\Http\Response;

use \Closure;
use \ReflectionFunction;
use Exception;
use \App\Http\Middlewares\Queue as MiddlewaresQueue;

class Router
{
  /**
   * URL completa do projeto
   * @var string
   */
  private $url = '';

  /**
   * Prefixo de todas as rotas
   * @var string
   */
  private $prefix;

  /**
   * Indice de rotas
   * @var array
   */
  private $routes = [];

  /**
   * Instancia de request
   * @var Request
   */
  private $request;

  /**
   * Content-Type do response
   * @var string
   */
  private $contentType = 'text/html';

  public function __construct($url) {
    $this->request  = new Request($this);
    $this->url      = $url;

    $this->setPrefix();
  }

  /**
   * Método responsável por alterar o content-type do response
   * @param string $contentType
   */
  public function setContentType($contentType) {
    $this->contentType = $contentType;
  }

  /**
   * Metodo responsavel por definir prefixo
   *
   */
  private function setPrefix()
  {
    // Informações da url atual
    $parseUrl = parse_url($this->url);

    // Define o prefixo
    $this->prefix = $parseUrl['path'] ?? '';
  }

  /**
   * Metodo responsavel por adicionar uma rota na classe
   * @param string
   * @param string
   * @param array
   */
  private function addRoute($method, $route, $params = []) : void {
    // Middlewares da rota
    $params['middlewares'] = $params['middlewares'] ?? [];

    // Validação dos parametros
    foreach ($params as $key => $value) {
      if ($value instanceof Closure) {
        $params['controller'] = $value;
        unset($params[$key]);
        continue;
      }
    }

    // Variaveis da rota
    $params['variables'] = [];

    // Padrão de validação das variaveis das rotas
    $patternvariable = '/{(.*?)}/';
    if (preg_match_all($patternvariable, $route, $matches)) {
      $route                = preg_replace($patternvariable, '(.*?)', $route);
      $params['variables']  = $matches[1];
    }

    // Remove a barra no final da rota
    $route = rtrim($route, '/');

    // Padrão de validação da url
    $patternRoute = '/^' . str_replace('/', '\/', $route) . '$/';

    // Adiciona a rota dentro da classe
    $this->routes[$patternRoute][$method] = $params;
  }

  /**
   * Metodo responsavel por definir uma rota de get
   * @param string
   * @param array
   */
  public function get($route, $params = []) {
    return $this->addRoute('GET', $route, $params);
  }

  /**
   * Metodo responsavel por definir uma rota de get
   * @param string
   * @param array
   */
  public function post($route, $params = []) {
    return $this->addRoute('POST', $route, $params);
  }

  /**
   * Metodo responsavel por definir uma rota de get
   * @param string
   * @param array
   */
  public function put($route, $params = []) {
    return $this->addRoute('PUT', $route, $params);
  }

  /**
   * Metodo responsavel por definir uma rota de get
   * @param string
   * @param array
   */
  public function delete($route, $params = []) {
    return $this->addRoute('DELETE', $route, $params);
  }

  /**
   * Metodo responsavel por retornar a URI desconseiderendo o prefixo
   * @return string
   */
  public function getUri() {
    // Uri da request
    $uri = $this->request->getUri();

    // Fatia a uri com o prefixo
    $xUri = strlen($this->prefix) ? explode($this->prefix, $uri) : [$uri];

    // Retorna a uri sem prefixo
    return rtrim(end($xUri), '/');
  }

  /**
   * Metodo responsavel por retornar os dados da rota atual
   *
   */
  private function getRoute() {
    // uri
    $uri = $this->getUri();

    // Metodo
    $httpMethod = $this->request->getHttpMethod();

    // Valida as rotas
    foreach ($this->routes as $patternRoute => $method) {
      // Verifica se a uri bate o padrao
      if (preg_match($patternRoute, $uri, $matches)) {
        // Verifica metodo
        if (isset($method[$httpMethod])) {
          // Remove a primeira posição
          unset($matches[0]);

          // Variaveis processadas
          $keys                                         = $method[$httpMethod]['variables'];
          $method[$httpMethod]['variables']             = array_combine($keys, $matches);
          $method[$httpMethod]['variables']['request']  = $this->request;

          // Retorno dos parametros da rota
          return $method[$httpMethod];
        }

        // Metodo não permitido
        throw new Exception('Metodo não permitido', 405);
      }
    }

    // PAgina nao encontrada
    throw new Exception('Url não encontrada', 404);
  }

  public function run() {
    try {
      // Obtem a rota atuall
      $route = $this->getRoute();

      // Verifica controlador
      if (!isset($route['controller'])) {
        throw new Exception('A url não pode ser processada', 500);
      }

      $args = [];

      $reflection = new ReflectionFunction($route['controller']);
      foreach ($reflection->getParameters() as $parameter) {
        $name         = $parameter->getName();
        $args[$name]  = $route['variables'][$name] ?? '';
      }

      // Retorna a execução da fila de middlewares
      return (new MiddlewaresQueue($route['middlewares'], $route['controller'], $args))->next($this->request);
    } catch (Exception $e) {
      return new Response($e->getCode(), $this->getErrorMessage($e->getMessage()), $this->contentType);
    }
  }

  /**
   * Método responsável por retornar a mensagem de erro de acordo com o Content-Type
   * @param string $message
   * @return mixed
   */
  private function getErrorMessage($message) {
    switch($this->contentType) {
      case 'application/json':
        return json_encode(['error' => $message]);
      case 'text/html':
        return '<h1>' . $message . '</h1>';
      default:
        return $message;
    }
  }
}
