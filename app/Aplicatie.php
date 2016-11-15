<?php

require_once "Design.php";
require_once "Login.php";
require_once "Utilizator.php";
require_once "FirmaOrganizatoare.php";
require_once "DataCalendaristica.php";

function prepareEnvironment($configuration) {
  $isProduction = $configuration["IsProduction"];

  if($isProduction) {
    error_reporting(0);
  }
}

function getLocalConfiguration() {
  try {
    $string = file_get_contents("config/local.json");
    return json_decode($string, true);
  } catch (Exception $e) {
    throw new Exception("Contactează administratorul - cod CONFIG_FILE_NOT_SET");
  }
}
function connectToMySQL($configuration) {

  $host         =  $configuration["Host"];
  $username     =  $configuration["Username"];
  $password     = $configuration["Password"];
  $databaseName =  $configuration["DatabaseName"];

  try {
    $locator = 'mysql:host=' . $host . ';dbname=' . $databaseName . ';charset=utf8mb4';
    return new PDO($locator, $username, $password);
  }
  catch (PDOException $ex) {
    throw new Exception('Ceva nu a mers bine');
  }
}

class Aplicatie {
  private static $_instance = null;
  public $Database = null;
  private $firma_organizatoare = null;
  private $utilizator = null;
  private $time_start = null;
  private function __construct() {
    try {

      $configuration = getLocalConfiguration();
      prepareEnvironment($configuration);

      $this->Database            = connectToMySQL($configuration);
      $currentUserID             = Login::request_access($this->Database);
      $this->utilizator          = new Utilizator($this->Database, $currentUserID);
      $this->firma_organizatoare = new FirmaOrganizatoare($this->Database, 1);
    }
    catch (Exception $e) {
      Design::showLoginForm($e->getMessage());
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
  public function getFirmaOrganizatoare() {
    return $this->firma_organizatoare;
  }
}
