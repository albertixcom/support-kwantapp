<?php /** @noinspection PhpUnused */

/*
 * Esempio
 *
 */
namespace KwantRegistration\Core;

class AppArgs {

  /**
   * Parametri obbligatori (uno di questi)
   */
  private static bool $runTest = false;
  private static bool $checkPromocodes = false;

  /*
   * Setters/Getters
   */

  /**
   *
   * @param bool $runTest
   */
  public static function setRunTest(bool $runTest) {
    self::$runTest = $runTest;
  }

  /**
   *
   * @return bool
   */
  public static function getRunTest():bool {
    return self::$runTest;
  }

  /**
   *
   * @param bool $checkPromocodes
   */
  public static function setCheckPromocodes(bool $checkPromocodes) {
    self::$checkPromocodes = $checkPromocodes;
  }

  /**
   *
   * @return bool
   */
  public static function getCheckPromocodes():bool {
    return self::$checkPromocodes;
  }


  /**
   *
   * @param array $options
   * @param boolean $exit
   */
  public static function usage(array $options, bool $exit = true) {

    // elencare lista dei parametri disponibili
    print_r($options);
    if ($exit) {
      die();
    }
  }

  /** @noinspection PhpUnused */
  public static function dumpArgs() {
    $out = " \n ========================================== "."\n";
    $out .= " - run-test: ".self::formatBool(self::$runTest)."\n";
    $out .= " - check-promocodes: ".self::formatBool(self::$checkPromocodes)."\n";
    $out .= " ========================================== "."\n";

    echo("$out");
  }

  /**
   *
   */
  public static function parseArgs() {

    $longOptions = [];
    $shortOptions = [];

    $longOptions[] = "run-test";
    $longOptions[] = "check-promocodes";

    // --------- Inizio elaborazione
    $options = getopt(implode("", $shortOptions), $longOptions);

    if (array_key_exists("help", $options) || array_key_exists("h", $options)) {
      self::usage($options);
    }

    if (array_key_exists("run-test", $options)) {
      self::$runTest = true;
    } elseif (array_key_exists("check-promocodes", $options)) {
      self::$checkPromocodes = true;
    }
  }

  static function formatBool($var = true): string {
    return ($var)?'Si':'No';
  }
}
