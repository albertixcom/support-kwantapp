<?php
/** @noinspection PhpMissingReturnTypeInspection */
/** @noinspection PhpMissingFieldTypeInspection */

namespace KwantRegistration\Core;

  trait Singleton {
    static private $instance = null;
    private function __construct() { /* ... @return Singleton */ } // Protect from creation through new Singleton
    private function __clone() { /* ... @return Singleton */ } // Protect from creation through clone
    static public function getInstance() {
      return self::$instance===null
        ? self::$instance = new static()//new self()
        : self::$instance;
    }
  }

