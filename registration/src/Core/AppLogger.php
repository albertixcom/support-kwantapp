<?php
/** @noinspection PhpUnused */

namespace KwantRegistration\Core;

  use Exception;
  use Monolog\Logger;
  use Monolog\Handler\StreamHandler;
  use Monolog\Processor\IntrospectionProcessor;
  use Monolog\Formatter\LineFormatter;

class AppLogger {

  use Singleton;

  /**
   *
   * @var Logger
   */
  protected $log;

  protected string $prefixPath = "";
  protected string $suffixPath = "";

  /**
   * AppLogger constructor.
   *
   * @throws Exception
   */
  private function __construct() {
    $this->setup();
  }

  /**
   * @throws Exception
   */
  private function setup() {

    // create a log channel
    $log = new Logger('KwantRegistration');

    // const SIMPLE_FORMAT = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";
    $this->createHandlers(Logger::DEBUG, $log);
    $this->createHandlers(Logger::ERROR, $log);

    $this->log = $log;
  }

  /**
   * @param int $level
   * @param Logger $log
   * @throws Exception
   * @noinspection PhpParameterByRefIsNotUsedAsReferenceInspection
   */
  private function createHandlers(int $level, Logger &$log) {

    $fileName = "debug.log";
    switch ($level) {
      case Logger::DEBUG:
        $fileName = "debug.log";
        break;
      case Logger::ERROR:
        $fileName = "error.log";
    }

    $fileHandler = new StreamHandler($this->getLogsPath().'/'.$fileName, $level);
    $fileHandler->setFormatter(new LineFormatter(null,null,true,true));
    $log->pushHandler($fileHandler);

    $cliHandler = new StreamHandler('php://stdout', $level);
    $log->pushHandler($cliHandler);

    $processor = new IntrospectionProcessor($level);
    $log->pushProcessor($processor);

  }

  private function getLogsPath(): string {
    $logsPath = APP_ROOT.'/../../logs/'.date("Y-m-d");
    if (!empty($this->prefixPath)) {
      $logsPath = APP_ROOT.'/../../logs/'.$this->prefixPath.'/'.date("Y-m-d");
      if (!is_dir($logsPath)) { mkdir($logsPath, 0755, true); }
    }
    if (!empty($this->suffixPath)) {
      $logsPath .= '/'.$this->suffixPath;
      if (!is_dir($logsPath)) { mkdir($logsPath, 0755, true); }
    }
    //echo "\$logsPath [$logsPath]\n";
    return $logsPath;
  }

  /**
   * @param $prefixPath
   */
  public function setPrefixPath($prefixPath) {
    $this->prefixPath = $prefixPath;
    try {
      $this->setup();
    } catch (Exception $e) {
    }
  }

  public function setSuffixPath(string $suffixPath) {
    $this->suffixPath = $suffixPath;
    try {
      $this->setup();
    } catch (Exception $e) {
    }
  }

  /**
   *
   * @return Logger
   */
  public function getLogger(): Logger {
    return $this->log;
  }

}
