<?php
/** @noinspection PhpUnused */

namespace KwantRegistration\Core;


class AppCore {

  use Singleton;

  private function __construct() {
  }



  public static function getEnv() {
    return AppConfig::getInstance()->getConfig()['enviroment'];
  }

}
