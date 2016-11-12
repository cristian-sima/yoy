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
 * Realizeaza un registru tabelat pentru toate firmele intr-o anumita luna
 * @author			Cristian Sima
 * @data			22.02.2014
 * @version			1.2
 *
 */
class RegistruGraficGeneral extends RegistruGrafic
{
	/**
	 *
	 * Realizeaza un obiect registruFirmaTabel
	 *
	 * @param DataCalendaristica $data		Referinta spre obiectul data calendaristica
	 *
	 */
	public function RegistruGraficGeneral($data)
	{
		parent::__construct($data);
		parent::setTitle("REGISTRU GENERAL");
		parent::setPrimulRand(Aplicatie::getInstance()->getFirmaOrganizatoare()->getLocatie());
	}


	/**
	 *
	 * Proceseaza datele pentru situatia registru grafica pentru toate firmele intr-o luna. Datele sunt formate din incasari, plati.
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
									"width" 	=>	"90px"
									),
									array(
									"content" 	=> 	"EXPLICAȚII",
									"width" 	=>	"310px"
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

		$suma = self::getSoldTotalLunar(new DataCalendaristica(DataCalendaristica::getZiuaPrecedenta($this->getFrom())));

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
			$_aparate_mecanice		= new Total("Temporar");
			$_impozit				= new Total("Temporar");
			$_dispozitii			= new Total("Temporar");

			$data_curenta 			= new DataCalendaristica($data_curenta);


			/* -------------- PLĂȚI SI ÎNCASĂRI ---------------- */


			$q="SELECT 	id_firma
			FROM completare_mecanica
			 WHERE data_= '".$data_curenta."'
			 GROUP BY id_firma";
			$result = mysql_query($q, Aplicatie::getInstance()->Database);
			while($db = mysql_fetch_array($result))
			{
				$firma = new FirmaSpatiu($db['id_firma']);

				$situatie = new SituatieMecanicaTotaluri($data_curenta, $data_curenta, $firma);

				if($situatie->isCompletata())
				{

					$_aparate_mecanice->actualizeazaIncasari($situatie->getTotalIncasari());


				}
			}

			if($_aparate_mecanice->getIncasari() != 0)
			{
				$this->addRow(array($this->getIndexNewRow(), "", $data_curenta->romanianFormat(), "ÎNCASĂRI",  $_aparate_mecanice->getIncasari(), 0));
			}

			$plati->actualizeazaIncasari($_aparate_mecanice->getPlati());
			$incasari->actualizeazaIncasari($_aparate_mecanice->getIncasari());

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
					WHERE  data='".$data_curenta."'
					ORDER by d.id";

			$result_zi = mysql_query($query, Aplicatie::getInstance()->Database);

			while($dispozitie = mysql_fetch_array($result_zi))
			{
				// perspectiva dinspre firmele spatiu

				if($dispozitie['tip'] == "plata")
				{
					$this->addRow(array($this->getIndexNewRow(), htmlspecialchars($dispozitie['document']), $data_curenta->romanianFormat(), "DISP INCASARE DE LA CASA SPRE ".$dispozitie['denumire_firma'], $dispozitie['valoare'],0));
					$dispoziții->actualizeazaIncasari($dispozitie['valoare']);
				}
				else
				{
					$this->addRow(array($this->getIndexNewRow(), htmlspecialchars($dispozitie['document']), $data_curenta->romanianFormat(), "DISP. PLATA DE LA ".$dispozitie['denumire_firma'].' SPRE CASA',  0, $dispozitie['valoare']));
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
	 * Returneaza soldul total care a fost in luna trecuta. Returneaza 0 daca nu exista niciun sold
	 *
	 * @param DataCalendaristica $data		Data calendaristica corespunzatoare lunii in care se doreste sa se afle
	 * @return float						Soldul total de luna trecuta pentru toate firmele
	 */
	public static function getSoldTotalLunar(DataCalendaristica $data)
	{
		$_total = 0;

		$q="SELECT 	valoare
		FROM sold_inchidere_luna
		 WHERE data_>='".$data->getFirstDayOfMonth()."' AND data_<= '".$data->getLastDayOfMonth()."'";

		$result = mysql_query($q, Aplicatie::getInstance()->Database);
		while($db = mysql_fetch_array($result))
		{
			$_total += intval($db['valoare']);
		}

		return $_total;
	}
}
