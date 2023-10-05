<?php /** @noinspection PhpUnused */

namespace KwantRegistration\Core;

use DateTime;

class AppUtils {

  // ============= DATE FUNCTIONS ==============

  public static function getMicrotime() {
    $mt = explode(' ', microtime());
    return ((int)$mt[1]) * 1000 + ((int)round($mt[0] * 1000));
  }

  public static function dateTimeToMilliseconds(DateTime $dateTime) {
    $secs = $dateTime->getTimestamp(); // Gets the seconds
    $millisecs = $secs*1000; // Converted to milliseconds
    $millisecs += $dateTime->format("u")/1000; // Microseconds converted to seconds
    return $millisecs;
  }

  /**
   *
   * @param string $isoDate
   * @return string
   */
  public static function DateFromISO(string $isoDate): string {
    $date = DateTime::createFromFormat("Y-m-d\TH:i:s.u\Z", $isoDate);
    return $date->format("Y-m-d H:i:s");
  }

  // ============= MISC FUNCTIONS ==============

  public static function camelize($str, $sep = "_"): string {
    // camelizze
    $_words = preg_split('/'.$sep.'/', $str);
    $words = [];
    foreach ($_words as $word) {
      $words[] = ucfirst($word);
    }
    return implode("", $words);
  }

  public static function generateSeoURL(string $string, $wordLimit = 0): string {
    $separator = '-';

    if($wordLimit != 0){
        $wordArr = explode(' ', $string);
        $string = implode(' ', array_slice($wordArr, 0, $wordLimit));
    }

    $quoteSeparator = preg_quote($separator, '#');

    $trans = array(
        '&.+?;'                    => '',
        '[^\w\d _-]'            => '',
        '\s+'                    => $separator,
        '('.$quoteSeparator.')+'=> $separator
    );

    $string = strip_tags($string);
    foreach ($trans as $key => $val){
        $string = preg_replace('#'.$key.'#i', $val, $string);
    }

    $string = strtolower($string);

    return trim(trim($string, $separator));
  }

  /**
   * @param $value
   * @param $array
   * @param $column
   * @return array|null
   */
  public static function searchInArray($value, $array, $column): ?array {
    $key = array_search($value, array_column($array, $column));
    if ($key) {
      return $array[$key];
    }
    return null;
  }

  public static function endsWith($haystack, $needle): bool {
    return substr($haystack,-strlen($needle))===$needle;
  }

  public static function cleanupRecords(array &$records) {
    foreach ($records as &$item) {
      $item = array_filter($item, function ($value) {
        return ($value !== null) ;
      });
    }
  }

  public static function RandomStr($type = 'uppernum', $length = 8)
  {
    switch($type)
    {
      case 'basic'    : return mt_rand();
      case 'alpha'    :
      case 'alphanum' :
      case 'num'      :
      case 'nozero'   :
      case 'uppernum' :
        $seedings             = array();
        $seedings['alpha']    = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $seedings['alphanum'] = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $seedings['num']      = '0123456789';
        $seedings['nozero']   = '123456789';
        $seedings['uppernum']   = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        $pool = $seedings[$type];

        $str = '';
        for ($i=0; $i < $length; $i++)
        {
          $str .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
        }
        return $str;
      case 'unique'   :
      case 'md5'      :
        return md5(uniqid(mt_rand()));
    }

    return '';
  }

  public static function cryptoJsAesEncrypt($passphrase, $value){
    $salt = openssl_random_pseudo_bytes(8);
    $salted = '';
    $dx = '';
    while (strlen($salted) < 48) {
      $dx = md5($dx.$passphrase.$salt, true);
      $salted .= $dx;
    }
    $key = substr($salted, 0, 32);
    $iv  = substr($salted, 32,16);
    $encrypted_data = openssl_encrypt(json_encode($value), 'aes-256-cbc', $key, true, $iv);
    $data = array("ct" => base64_encode($encrypted_data), "iv" => bin2hex($iv), "s" => bin2hex($salt));
    return json_encode($data);
  }

  public static function encryptData($data, $key) {
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    $payload = $iv . $encrypted;
    return base64_encode($payload);
  }






}
