<?php

namespace App\Controller\Api;

class Api {

  public static function getDetails($request) {
    return [
      'name' => 'Api',
      'version' => '1.0.0',
      'author' => 'Edie Johnny (edie.eu@gmail.com)'
    ];
  }

  /**
   * Método responsável por retornar os detalhes da paginação
   * @param  Request $request
   * @param  Pagination $obPagination
   * @return array
   */
  protected static function getPagination($request, $obPagination) {
    // Obtém os parâmetros da requisição
    $queryParams = $request->getParams();

    // Obtém as informações da paginação
    $pages = $obPagination->getPages();

    // Retorna os dados da paginação
    return [
      'currentPage' => isset($queryParams['page']) ? (int)$queryParams['page'] : 1,
      'totalPages'  => !empty($pages) ? count($pages) : 1
    ];
  }

}