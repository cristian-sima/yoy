<?php

require_once "include/php/Total.php";
require_once "include/php/Guvern.php";
require_once "include/php/Romanian.php";
require_once "include/php/FirmaSpatiu.php";
require_once "include/php/RegistruGrafic.php";
require_once "include/php/SituatieMecanica.php";
require_once "include/php/DataCalendaristica.php";
require_once "include/php/SituatieMecanicaTotaluri.php";

/**
 *
 * Realizeaza un registru tabelat pentru o firma intr-o anumita luna
 * @author			Cristian Sima
 * @data			17.10.2014
 * @version			1.4
 *
 */
class RegistruGraficFirma extends RegistruGrafic
{
	private $firma	 = 	null;

	/**
	 *
	 * Realizeaza un obiect registruFirmaTabel
	 *
	 * @param Firma $firma					Referinta spre obiectul firma
	 * @param DataCalendaristica $data		Referinta spre obiectul data calendaristica
	 *
	 */
	public function RegistruGraficFirma($firma, $data)
	{
		$this->firma		= $firma;

		parent::__construct($data);
		parent::setTitle("REGISTRU DE CASĂ");
		parent::setPrimulRand($firma->getDenumire().' din '.$firma->getLocatie());
	}

	/**
	 * Returneaza o referinta spre obiectul firma al registrului
	 */
	public function getFirma()
	{
		return $this->firma;
	}

	/**
	 *
	 * Proceseaza datele pentru situatia registru grafica pentru o firma intr-o luna. Datele sunt formate din incasari, plati.
	 *
	 * @see RegistruGrafic::_processData()
	 */
	protected function _processData()
	{
		$columns	= array(
		array(
									"content"	=> "NR. CRT",
									"width"		=> "50px"
									),
									array(
									"content" 	=> 	"NR. <br /> ACT CASĂ",
									"width" 	=>	"125px"
									),
									array(
									"content" 	=> 	"DATA",
									"width" 	=>	"100px"
									),
									array(
									"content" 	=> 	"EXPLICAȚII",
									"width" 	=>	"300px"
									),
									array(
									"content" 	=> 	"ÎNCASĂRI",
									"width" 	=>	"200px"
									),
									array(
									"content" 	=> 	"PLĂȚI",
									"width"		=>	"200px"
									)
									);
		$data_curenta 			= $this->getFrom();

		$incasari		= new Total("Încasări");
		$plati			= new Total("Plăți");
		$total			= new Total("General");
		$impozit		= new Total("Impozit");
		$dispoziții		= new Total("Dispoziții");

		$suma = self::getSoldTotalLunar($this->getFirma(), new DataCalendaristica(DataCalendaristica::getZiuaPrecedenta($this->getFrom())));

		if($suma > 0)
		{
			$total->actualizeazaIncasari($suma);
		}
		else
		{
			$total->actualizeazaPlati(-$suma);
		}
		parent::setColumns($columns);
		parent::setColoaneTotalizate(array(4, 5));
		parent::setTotalTitleColumn(3);
		parent::setSumeColoaneTotalizate(array(4=>$total->getIncasari(), 5=>$total->getPlati()));



		while(strtotime($data_curenta) <= strtotime($this->getTo()))
		{
			$data_curenta = new DataCalendaristica($data_curenta);


			/* -------------- PLĂȚI SI ÎNCASĂRI ---------------- */

			$situatie = new SituatieMecanicaTotaluri($data_curenta, $data_curenta, $this->firma);

			if($situatie->isCompletata())
			{
				if($situatie->getTotalIncasari() != 0)
				{
					$this->addRow(array($this->getIndexNewRow(), "", $data_curenta->romanianFormat(), "ÎNCASĂRI",  $situatie->getTotalIncasari(), 0));
				}

				$incasari->actualizeazaIncasari($situatie->getTotalIncasari());

			}

			/* ---------------------------- DISPOZITII -------------------------------*/


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
					WHERE  data='".$data_curenta."'  AND _to='".$this->getFirma()->getID()."' ";

			$result_zi = mysql_query($query, Aplicatie::getInstance()->getMYSQL());

			while($dispozitie = mysql_fetch_array($result_zi))
			{
				if($dispozitie['tip'] == "plata")
				{
					$this->addRow(array($this->getIndexNewRow(), htmlspecialchars($dispozitie['document']), $data_curenta->romanianFormat(), "DISPOZIȚIE ÎNCASARE",  $dispozitie['valoare'],0));
					$dispoziții->actualizeazaIncasari($dispozitie['valoare']);
				}
				else
				{
					$this->addRow(array($this->getIndexNewRow(), htmlspecialchars($dispozitie['document']), $data_curenta->romanianFormat(), "DISPOZIȚIE PLATĂ",  0, $dispozitie['valoare']));
					$dispoziții->actualizeazaPlati($dispozitie['valoare']);
				}
			}

			/* ----------------------- mergem la ziua urmatoare ---------------------------*/

			$data_curenta = new DataCalendaristica(DataCalendaristica::getZiuaUrmatoare($data_curenta));
		}

		/* --------------- Adaugare totaluri ---------------- */

		$total->actualizeazaIncasari($impozit->getIncasari());
		$total->actualizeazaIncasari($incasari->getIncasari());
		$total->actualizeazaIncasari($dispoziții->getIncasari());
		$total->actualizeazaPlati($dispoziții->getPlati());
		$total->actualizeazaPlati($plati->getIncasari());

		$this->addTotal($plati);
		$this->addTotal($incasari);
		$this->addTotal($impozit);
		$this->addTotal($dispoziții);
		$this->addTotal($total);


		$this->actualizeazaIncasari($total->getIncasari());
		$this->actualizeazaPlati($total->getPlati());
	}


	/**
	 * Returneaza soldul total care a fost in luna trecuta penmtru o firma. Returneaza 0 daca nu exista niciun sold
	 *
	 * @param Firma $firma 					Firma pentru care se doreste
	 * @param DataCalendaristica $data		Data calendaristica corespunzatoare lunii in care se doreste sa se afle
	 * @return float						Soldul total de luna trecuta pentru toate firmele
	 */
	public static function getSoldTotalLunar(Firma $firma, DataCalendaristica $data)
	{
		$_total = 0;

		$q="SELECT 	valoare
		FROM sold_inchidere_luna
		 WHERE data_>='".$data->getFirstDayOfMonth()."' AND data_<= '".$data->getLastDayOfMonth()."' AND idFirma = '".$firma->getID()."'";

		$result = mysql_query($q, Aplicatie::getInstance()->getMYSQL());
		while($db = mysql_fetch_array($result))
		{
			$_total += intval($db['valoare']);
		}
		return $_total;
	}
}
