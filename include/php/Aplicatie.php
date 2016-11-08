<?php

require_once "Page.php";
require_once "MYSQL.php";
require_once "Login.php";
require_once "Utilizator.php";
require_once "FirmaOrganizatoare.php";
require_once "DataCalendaristica.php";

/**
 *
 * @description		Reprezinta obiectul care incapsuleaza datele despre aplicatie (conexiuneMysql, firma organizatoare, date despre login)
 * @author			Cristian Sima
 * @data			27.02.2014
 * @version			2.7
 *
 */
class Aplicatie
{

	const version = "2.8";

	private static $_instance		= null;
	private $mysql					= null;
	private $firma_organizatoare	= null;
	private $utilizator				= null;
	private $time_start				= null;

	/**
	 *
	 * @description				Realizeaza aplicatia. Aplicatia are access la baza de date, și verifica conexiunea utilizatorului
	 * @die						In cazul in care obiectele MYSQL și login arunca o exceptie
	 *
	 */
	private function __construct()
	{
		try
		{
			// error_reporting(0);

			$this->mysql				=	new MYSQL("localhost",
														"root",
														"",
														"yoy_ro_date"
														);
			Login::request_access($this->getMYSQL());
			$this->firma_organizatoare	= 	new FirmaOrganizatoare($this->getMYSQL(), 1);

			$this->time_start 			= microtime(true);
		}
		catch(Exception $e)
		{
			PAGE::showCSSLogin();
			PAGE::showError($e->getMessage());
			Page::showLoginForm();
			die();
		}
	}


	/**
	 *
	 * Returneaza utilizatorul care este conectat la aplicatie
	 *
	 * @return Utilizator		Utilizatorul conectat la aplicatie
	 */
	public function getUtilizator()
	{
		if($this->utilizator == null)
			$this->utilizator 			= 	new Utilizator(Login::getUserId());

		return $this->utilizator;
	}



	/**
	 *
	 * Returneaza timpul de executie al applicatiei in secunde
	 *
	 * @return  			Timpul de executie
	 *
	 */
	public function getTimeOfExecution()
	{

		$time_end = microtime(true);
    	$time = $time_end - $this->time_start;

    	return round($time,5);
	}


	/**
	 *
	 * @description				Creza un obiect aplicatie sau returneaza referinta despre cel creeat
	 * @return Aplicatie		Referinta spre aplicatie
	 *
	 */
	public static function getInstance()
	{
		if(!isset(self::$_instance))
		{
			self::$_instance = new Aplicatie();
		}

		return self::$_instance;
	}


	/**
	 *
	 * Returneaza o referinta spre obiectul de conexiune MYSQL
	 * @return MYSQL				Referinta spre obiectul MYSQL
	 *
	 */
	public function getMYSQL()
	{
		return $this->mysql;
	}

	/**
	 * Returneaza versiunea aplicatiei. Aceasta versiune corespunde cu fisierele urcate pe server
	 * @return				Versiunea aplicatiei
	 */
	public function getVersion()
	{
		return self::version;
	}

	/**
	 *
	 * Returneaza o referinta spre firma care organizeaza
	 * @return FirmaOrganizatoare	Referinta spre firma care organizeaza
	 *
	 */
	public function getFirmaOrganizatoare()
	{
		return $this->firma_organizatoare;
	}



}
