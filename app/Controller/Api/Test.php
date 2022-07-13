<?php

namespace App\Controller\Api;

class Test extends Api {

  public static function test($request, $testValue = null) {
    return [
      'testKey' => $testValue ?? 'testValue',
    ];
  }

}