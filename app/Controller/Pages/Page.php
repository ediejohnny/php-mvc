<?php

namespace App\Controller\Pages;

use App\Utils\View;

class Page {
  private static function getHeader() {
    return View::render('partials/header');
  }

  private static function getFooter() {
    return View::render('partials/footer');
  }

  /**
   * Método responsável por retornar o conteúdo da página
   */
  public static function getPage(string $title, $content) {
    return View::render('pages/page', [
        'title'   => $title,
        'header'  => self::getHeader(),
        'content' => $content,
        'footer'  => self::getFooter()
    ]);
  }
}
