<?php

namespace KwantRegistration\Core;

class AppConfig {

  use Singleton;

  protected array $config;

  private function __construct() {

    $this->loadConfig();
  }

  private function loadConfig() {

    $localConfigFile = APP_ROOT . "/config/config.json";
    $config = json_decode(file_get_contents($localConfigFile), true);
    $this->config = $config;
  }

  public function getConfig(): array {
    return $this->config;
  }

  public function getEnvConfig() {
    $env = $this->config['enviroment'];
    return $this->config[$env];
  }


}
