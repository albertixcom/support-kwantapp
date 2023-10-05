<?php
namespace KwantRegistration\I18N;


class Lang {


  protected static array $texts = [];

  /** @noinspection PhpSameParameterValueInspection */
  private static function loadLang(string $lang) {
    $langFile = dirname(__FILE__) . "/".$lang.".json";
    if (file_exists($langFile)) {
      $texts = json_decode(file_get_contents($langFile), true);
      self::$texts = $texts;
    }
  }

  public static function l(string $key) {
    if (empty(self::$texts)) {
      self::loadLang('pl');
    }
    if (key_exists($key, self::$texts)) {
      return self::$texts[$key];
    }
    return $key;
  }

}
