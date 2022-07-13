<?php

namespace App\Utils\Cache;

class File {

  /**
   * Método responsável por retornar o caminho do arquivo de cache
   * @param string $hash
   * @return string
   */
  private static function getCacheFile($hash) {
    $dir = getenv('CACHE_DIR');

    if(!file_exists($dir)) {
      mkdir($dir, 0755, true);
    }

    return $dir . '/' . $hash . '.cache';
  }

  /**
   * Método responsável por guardar informações no cache
   * @param string $hash
   * @param string $content
   * @return boolean
   */
  private static function storageCache($hash, $content) {
    // Serializa o conteúdo
    $serialize = serialize($content);

    // Obtém o caminho até o arquivo de cache
    $cacheFile = self::getCacheFile($hash);

    // Salva o conteúdo no arquivo de cache
    return file_put_contents($cacheFile, $serialize);
  }

  /**
   * Método responsável por obter informações do cache
   * @param string $hash
   * @param integer $expiration
   * @return mixed
   */
  private static function getContentCache($hash, $expiration) {
    // Obter o caminho do arquivo
    $cacheFile = self::getCacheFile($hash);

    // Verifica se o arquivo existe
    if(!file_exists($cacheFile)) {
      return false;
    }

    // Verifica se o arquivo está expirado
    if(time() - filemtime($cacheFile) > $expiration) {
      return false;
    }

    // Retorna o conteúdo deserializado do arquivo
    $serialize = file_get_contents($cacheFile);
    return unserialize($serialize);
  }

  /**
   * Método responsável por obter uma informação do cache
   * @param string  $hash
   * @param integer $expiration
   * @param Closure $function
   * @return mixed
   */
  public static function getCache($hash, $expiration, $function) {
    // Verifica se existe conteúdo gravado
    if($content = self::getContentCache($hash, $expiration)) {
      return $content;
    }
    
    // Execução da função
    $content = $function();

    // Grava o retorno no cache
    self::storageCache($hash, $content);

    // Retorna o conteúdo
    return $content;
  }

}