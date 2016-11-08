<?php

require_once "include/php/Total.php";
require_once "include/php/Bilete.php";
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
		parent::setTitle("REGISTRU DE CASA");
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
	 * Proceseaza datele pentru situatia registru grafica pentru o firma intr-o luna. Datele sunt formate din incasari, plati si bilete.
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
									"content" 	=> 	"NR. <br /> ACT CASA",
									"width" 	=>	"125px" 
									),
									array(
									"content" 	=> 	"DATA",
									"width" 	=>	"100px" 
									),
									array(
									"content" 	=> 	"EXPLICATII",
									"width" 	=>	"300px" 
									),
									array(
									"content" 	=> 	"INCASARI",
									"width" 	=>	"200px" 
									),
									array(
									"content" 	=> 	"PLATI",
									"width"		=>	"200px" 
									)
									);
		$pret_taxa_pe_bilet		= Guvern::getPretBilet($this->getFrom());
		$prag_de_impozitare 	= Guvern::getPragDeImpozitare($this->getFrom());
		$procent_impozitare 	= Guvern::getProcentDeImpozitare($this->getFrom());
		$data_curenta 			= $this->getFrom();
		
		$incasari		= new Total("Incasari");
		$plati			= new Total("Plati");
		$bilete			= new Total("Bilete");
		$total			= new Total("General");
		$impozit		= new Total("Impozit");
		$dispozitii		= new Total("Dispozitii");
		
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

				
			/* -------------- PLATI SI INCASARI ---------------- */
				
			$situatie = new SituatieMecanicaTotaluri($data_curenta, $data_curenta, $this->firma);

			if($situatie->isCompletata())
			{
				if($situatie->getTotalIncasari() != 0)
				{
					$this->addRow(array($this->getIndexNewRow(), "", $data_curenta->romanianFormat(), "INCASARI",  $situatie->getTotalIncasari(), 0));
				}

				if($situatie->getTotalPremii() != 0)
				{
					$this->addRow(array($this->getIndexNewRow(), "", $data_curenta->romanianFormat(), "PREMII",  0, $situatie->getTotalPremii()));
				}

				$incasari->actualizeazaIncasari($situatie->getTotalIncasari());
				$plati->actualizeazaIncasari($situatie->getTotalPremii());


				/* ------------------------ BILETE ---------------------- */


				$calcul_bilete = new Bilete($data_curenta, $data_curenta, $this->firma);

				foreach ($calcul_bilete->getCarnete() as $carnet)
				{
					$numar_de_bilete  = $carnet->getNumarulDeBilete();
						
					if($numar_de_bilete != 0)
					{
						$_suma = $numar_de_bilete * $pret_taxa_pe_bilet;
						$this->addRow(array($this->getIndexNewRow(), "<span class='tabel_page_bilete_big'>".$carnet.'</span>', $data_curenta->romanianFormat(), "BILETE",  $_suma, 0));
						$bilete->actualizeazaIncasari($_suma);
					}
				}
			}

				
			/* ------------------------ IMPOZIT ------------------------------*/
	
			$_suma = 0;
												
	
			$q="SELECT 	i.id,
					i.data,
					i.suma
			FROM impozit AS i 
			LEFT JOIN firma AS f ON f.id=i.idFirma 
			WHERE 	i.idFirma='".$this->getFirma()->getID()."' AND 
					i.data='".$data_curenta."'";		
			
			$result = mysql_query($q, Aplicatie::getInstance()->getMYSQL()->getResource());
			while($premiu = mysql_fetch_array($result))
			{
				if($premiu['suma'] > $prag_de_impozitare)
				{
					$_suma += (($premiu['suma'] - $prag_de_impozitare) * $procent_impozitare / 100);
				}
			}
			
			if($_suma != 0)
			{
				$impozit->actualizeazaIncasari($_suma);
				$this->addRow(array($this->getIndexNewRow(), "", $data_curenta->romanianFormat(), "IMPOZIT",  $_suma, 0));
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
			
			$result_zi = mysql_query($query, Aplicatie::getInstance()->getMYSQL()->getResource());
	
			while($dispozitie = mysql_fetch_array($result_zi))
			{	
				if($dispozitie['tip'] == "plata")
				{	
					$this->addRow(array($this->getIndexNewRow(), htmlspecialchars($dispozitie['document']), $data_curenta->romanianFormat(), "DISPOZITIE INCASARE",  $dispozitie['valoare'],0));
					$dispozitii->actualizeazaIncasari($dispozitie['valoare']);
				}
				else 
				{
					$this->addRow(array($this->getIndexNewRow(), htmlspecialchars($dispozitie['document']), $data_curenta->romanianFormat(), "DISPOZITIE PLATA",  0, $dispozitie['valoare']));
					$dispozitii->actualizeazaPlati($dispozitie['valoare']);					
				}
			}	
		
			/* ----------------------- mergem la ziua urmatoare ---------------------------*/
			
			$data_curenta = new DataCalendaristica(DataCalendaristica::getZiuaUrmatoare($data_curenta));
		}
	
		/* --------------- Adaugare totaluri ---------------- */
			
		$total->actualizeazaIncasari($impozit->getIncasari());		
		$total->actualizeazaIncasari($incasari->getIncasari());
		$total->actualizeazaIncasari($bilete->getIncasari());
		$total->actualizeazaIncasari($dispozitii->getIncasari());
		$total->actualizeazaPlati($dispozitii->getPlati());
		$total->actualizeazaPlati($plati->getIncasari());
	
		$this->addTotal($plati);
		$this->addTotal($incasari);
		$this->addTotal($bilete);
		$this->addTotal($impozit);
		$this->addTotal($dispozitii);
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
		
		$result = mysql_query($q, Aplicatie::getInstance()->getMYSQL()->getResource());
		while($db = mysql_fetch_array($result))
		{				
			$_total += intval($db['valoare']);
		}		
		return $_total;
	}
}