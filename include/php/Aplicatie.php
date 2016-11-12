<?php

require_once "Page.php";
require_once "Login.php";
require_once "Utilizator.php";
require_once "FirmaOrganizatoare.php";
require_once "DataCalendaristica.php";

function connectToMySQL() {
  $host         = "localhost";
  $username     = "root";
  $password     = "";
  $databaseName = "yoy_ro_date";
  try {
    $locator = 'mysql:host=' . $host . ';dbname=' . $databaseName . ';charset=utf8mb4';
    return new PDO($locator, $username, $password);
  }
  catch (PDOException $ex) {
    throw new Exception('Ceva nu a mers bine');
  }
}

class Aplicatie {
  const version = "2.8";
  private static $_instance = null;
  public $Database = null;
  private $firma_organizatoare = null;
  private $utilizator = null;
  private $time_start = null;
  private function __construct() {
    try {
      $this->Database            = connectToMySQL();
      $currentUserID             = Login::request_access($this->Database);
      $this->utilizator          = new Utilizator($this->Database, $currentUserID);
      $this->firma_organizatoare = new FirmaOrganizatoare($this->Database, 1);
    }
    catch (Exception $e) {
      PAGE::showCSSLogin();
      PAGE::showError($e->getMessage());
      Page::showLoginForm();
      die();
    }
  }
  public function getUtilizator() {
    return $this->utilizator;
  }
  public static function getInstance() {
    if (!isset(self::$_instance)) {
      self::$_instance = new Aplicatie();
    }
    return self::$_instance;
  }
  public function getVersion() {
    return self::version;
  }
  public function getFirmaOrganizatoare() {
    return $this->firma_organizatoare;
  }
}
