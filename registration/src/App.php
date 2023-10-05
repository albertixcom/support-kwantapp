<?php /** @noinspection PhpMultipleClassDeclarationsInspection */


namespace KwantRegistration;

use KwantRegistration\Core\AppConfig;
use KwantRegistration\Http\RequestFactory;
use KwantRegistration\Http\RequestUtils;
use KwantRegistration\Resources;

class App {


  public function dispatch() {

    $request = RequestFactory::fromGlobals();
    $routes = [
      'registration' => Resources\Registration::class
    ];

    // la prima parte e dedicata alle pagine statiche
    // esempio https://www.kwant.app/app/registration/<TOKEN>
    $route = RequestUtils::getPathSegment($request, 1);
    if (array_key_exists($route, $routes)) {
      /** @var $class Resources\ResourceInterface */
      $class = new $routes[$route];
      $class->dispatch();
      die();
    }

    // altrimenti not allowe (404)
    header("HTTP/1.0 404 Not Found");
  }

  public static function BaseUrl() {
    return AppConfig::getInstance()->getConfig()['base_url'];
  }

}
