<?php

namespace App\Controller\Pages;

use App\Utils\View;

class About extends Page {
  /**
   * Retorna o conteúdo da página Sobre
   * @return string
   */
  public static function getAbout() {
    $content = View::render('pages/about', [
      'name' => 'Pagina de about',
    ]);

    return parent::getPage('SOBRE', $content);
  }
}
