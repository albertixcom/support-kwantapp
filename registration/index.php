<?php /** @noinspection PhpDefineCanBeReplacedWithConstInspection */

use KwantRegistration\App;

require_once __DIR__ . '/vendor/autoload.php';

define("APP_ROOT", dirname(__FILE__));

  $app = new App();
  $app->dispatch();










