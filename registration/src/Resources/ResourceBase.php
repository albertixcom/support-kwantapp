<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

namespace KwantRegistration\Resources;

use KwantRegistration\Core;
use Monolog\Logger;

class ResourceBase {

  protected Logger $logger;

  protected array $templateVars = [];

  public function get(string $name) {
    if (isset($this->templateVars[$name])) {
      return $this->templateVars[$name];
    }
    return null;
  }

  public function __construct() {
    $this->logger = Core\AppLogger::getInstance()->getLogger();
  }

}
