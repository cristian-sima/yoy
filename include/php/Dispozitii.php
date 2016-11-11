<?php

require_once "Guvern.php";
require_once "FirmaSpatiu.php";
require_once "DataCalendaristica.php";

/**
 *
 * @description		Contine informatiile necasare la totalul dispozitiilor și dispozitiile dintre 2 perioade de timp
 * @author			Cristian Sima
 * @data			02.02.2014
 * @version			1.0
 *
 */
class Dispozitii
{
	private $from		= null;
	private $to			= null;
	private $data		= null;

	/**
	 *
	 * Realizeaza și proceseaza datele
	 *
	 * @param DataCalendaristica $from			Inceputul de interval
	 * @param DataCalendaristica _type $to		Sfarstitul de interval
	 *
	 */
	public function Dispozitii($from, $to)
	{
		$this->_process();
	}

	/**
	 *
	 *  Returneaza data de inceput a intervalului
	 *
	 *  @return DataCalendaristica				Data de inceput a intervalului
	 */
	public function getFrom()
	{
		return $this->from;
	}



	/**
	 *
	 *  Returneaza data de sfarsit a intervalului
	 *
	 *  @return DataCalendaristica				Data de sfarsit a intervalului
	 */
	public function getTo()
	{
		return $this->to;
	}


	/**
	 *
	 * Proceseaza datele
	 *
	 */
	private function _process()
	{

		$this->data = array();

		$query = "SELECT
					d.id,
					d.data,
					d._to,
					d.tip,
					d.valoare,
					d.document,
					d.explicatie,
					(SELECT nume FROM `firma` AS f WHERE f.id = d._to) AS denumire_firma
				FROM dispozitie AS d
				WHERE  data>='".$this->getFrom()."' AND data <= '".$this->getTo()."' ";

		$result_zi = mysql_query($query, Aplicatie::getInstance()->getMYSQL()->getResource());

		while($dispozitie = mysql_fetch_array($result_zi))
		{
			push($this->data, $dispozitie['a']);
		}


	}
}
