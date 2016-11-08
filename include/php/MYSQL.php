<?php

require_once "MYSQL.php";

/**
*
* Reprezinta obiectul care contine informatii despre conexiunea la baza de date
* @author			Cristian Sima
* @data			26.01.2014
* @version			1.0
*
*/
class MYSQL
{
	private $resource_ 		= null;
	private $utilizator		= "root";
	private $parola			= "root";
	private $host			= "localhost";
	private $numeDB			= "rapidcas_aplha";


	/**
	*
	* Realizeaza un obiect de conexiune la baza de date
	*
	* @param string $utilizator		Numele utilizatorului
	* @param string $parola			Parola utilizatorului
	* @param string $host				Adresa serverului
	* @param string $bd				Numele bazei de date
	*
	* @throws Exception				Problema la conexiunea la baza de date
	*
	*/
	public function __construct($host, $utilizator, $parola, $numeDB)
	{
		$this->utilizator 		= $utilizator;
		$this->parola			= $parola;
		$this->host				= $host;
		$this->numeDB			= $numeDB;
		@$this->resource_ 		= mysql_connect($host, $utilizator, $parola);


		if (!$this->resource_)
		{
			throw new Exception ('MYSQL Exception: Conectare nereusita la MySQL');
		}

		if (!mysql_select_db($this->numeDB, $this->getResource()))
		{
			throw new Exception ('MYSQL Exception: Baza de date nu a putut fi selectata');
		}

		@$result = mysql_query("SET NAMES 'utf8'", $this->getResource());

		if(!$result)
		{
			throw new Exception ('MYSQL Exception: Problema query');
		}
	}


	/**
	*
	* Retuneaza resursa link a conexiunii
	* @return 					Resursa link a conexiunii
	*
	*/
	public function getResource()
	{
		return $this->resource_;
	}
}
