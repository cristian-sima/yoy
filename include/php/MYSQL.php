<?php
require_once "MYSQL.php";
class MYSQL {
  private $resource_ = null;
  private $utilizator = "yoy";
  private $parola = "";
  private $host = "localhost";
  private $numeDB = "yoy_ro_date";
  public function __construct($host, $utilizator, $parola, $numeDB) {

		$this->host       = $host;

    $this->utilizator = $utilizator;
    $this->parola     = $parola;

    $this->numeDB     = $numeDB;

		try {
			$locator = 'mysql:host='.$host.';dbname='.$numeDB.';charset=utf8mb4';

			$this->resource_ = new PDO($locator, $utilizator, $parola);
		} catch(PDOException $ex) {
		    throw new Exception('Ceva nu a mers bine');
		}

  }
  public function getResource() {
    return $this->resource_;
  }
}
