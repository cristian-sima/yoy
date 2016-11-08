<?php

	require_once "include/php/Total.php";
	require_once "include/php/DataCalendaristica.php";
	
/**
 *
 * Reprezinta o situatie tip registru. Aceasta are un total (incasari și plati) precum și datile de inceput și sfarsit
 * @author			Cristian Sima
 * @data			14.02.2014
 * @version			1.1
 *
 */
abstract class Registru extends Total
{	
	private $from	= null;
	private $to		= null;
	private $total	= null;
	
	/**
	 * Apeleaza contructorul parinte și initializeaz campurile. Apeleaza metoda care proceseaza și adauga date in situatie
	 */
	public function Registru(DataCalendaristica $data)
	{
		
		$this->from			= new DataCalendaristica($data->getFirstDayOfMonth());
		$this->to			= new DataCalendaristica($data->getLastDayOfMonth());
		
		
		parent::__construct("General");
		
		$this->_processData();					
	}
	
	/**
	 * Returneaza data de inceput a registrului
	 * @return DataCalendaristica 		Data de inceput a registrului
	 */
	public function getFrom()
	{
		return $this->from;
	}
	
/**
	 * Returneaza data de sfarsit a registrului
	 * @return DataCalendaristica 		Data de sfarsit a registrului
	 */
	public function getTo()
	{
		return $this->to;
	}
	
	/**
	 *  Metoda proceseaza informatiile și adauga randuri in tabel in functie de obiecte. Este diferita pentru fiecare obiect
	 */
	protected abstract function _processData();
}