<?php

require_once "include/php/Total.php";
require_once "include/php/Bilete.php";
require_once "include/php/Guvern.php";
require_once "include/php/Romanian.php";
require_once "include/php/FirmaSpatiu.php";
require_once "include/php/RegistruGrafic.php";
require_once "include/php/SituațieMecanica.php";
require_once "include/php/DataCalendaristica.php";
require_once "include/php/SituațieMecanicaTotaluri.php";

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
	 * Proceseaza datele pentru situatia registru grafica pentru toate firmele intr-o luna. Datele sunt formate din incasari, plati si bilete.
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
									"width" 	=>	"90px" 
									),
									array(
									"content" 	=> 	"EXPLICATII",
									"width" 	=>	"310px" 
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
			$_bilete				= new Total("Temporar");
			$_impozit				= new Total("Temporar");
			$_dispozitii			= new Total("Temporar");
			
			$data_curenta 			= new DataCalendaristica($data_curenta);

				
			/* -------------- PLATI SI INCASARI ---------------- */
			
			
			$q="SELECT 	id_firma 
			FROM completare_mecanica
			 WHERE data_= '".$data_curenta."'
			 GROUP BY id_firma";			
			$result = mysql_query($q, Aplicatie::getInstance()->getMYSQL()->getResource());
			while($db = mysql_fetch_array($result))
			{				
				$firma = new FirmaSpatiu($db['id_firma']);
				
				$situatie = new SituațieMecanicaTotaluri($data_curenta, $data_curenta, $firma);
	
				if($situatie->isCompletata())
				{
					
					$_aparate_mecanice->actualizeazaIncasari($situatie->getTotalIncasari());
					$_aparate_mecanice->actualizeazaPlati($situatie->getTotalPremii());
	
	
					/* ------------------------ BILETE ---------------------- */	
	
					$calcul_bilete = new Bilete($data_curenta, $data_curenta, $firma);
	
					foreach ($calcul_bilete->getCarnete() as $carnet)
					{
						$numar_de_bilete  = $carnet->getNumarulDeBilete();
							
						if($numar_de_bilete != 0)
						{
							$_suma = $numar_de_bilete * $pret_taxa_pe_bilet;
							$_bilete->actualizeazaIncasari($_suma);
						}
					}
				}
			}			
		
			if($_aparate_mecanice->getIncasari() != 0)
			{
				$this->addRow(array($this->getIndexNewRow(), "", $data_curenta->romanianFormat(), "INCASARI",  $_aparate_mecanice->getIncasari(), 0));
			}

			if($_aparate_mecanice->getPlati() != 0)
			{
				$this->addRow(array($this->getIndexNewRow(), "", $data_curenta->romanianFormat(), "PREMII",  0, $_aparate_mecanice->getPlati()));
			}
			
			if($_bilete->getTotal() != 0)
			{
				$this->addRow(array($this->getIndexNewRow(), "", $data_curenta->romanianFormat(), "BILETE",  $_bilete->getTotal(), 0));
				$bilete->actualizeazaIncasari($_bilete->getTotal());					
			}	
			
			$plati->actualizeazaIncasari($_aparate_mecanice->getPlati());
			$incasari->actualizeazaIncasari($_aparate_mecanice->getIncasari());
			
			
			/* ------------------------ IMPOZIT ------------------------------*/
		
						
			$q="SELECT 	i.id,
					i.data,
					i.suma
			FROM impozit AS i 
			LEFT JOIN firma AS f ON f.id=i.idFirma 
			WHERE 	i.data='".$data_curenta."'";		
			
			$result = mysql_query($q, Aplicatie::getInstance()->getMYSQL()->getResource());
			while($premiu = mysql_fetch_array($result))
			{
				if($premiu['suma'] > $prag_de_impozitare)
				{
					$_impozit->actualizeazaIncasari((($premiu['suma'] - $prag_de_impozitare) * $procent_impozitare / 100));
				}
			}
			
			if($_impozit->getTotal() != 0)
			{
				$impozit->actualizeazaIncasari($_impozit->getTotal());
				$this->addRow(array($this->getIndexNewRow(), "", $data_curenta->romanianFormat(), "IMPOZIT",  $impozit->getTotal(), 0));
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
					WHERE  data='".$data_curenta."' 
					ORDER by d.id";					
			
			$result_zi = mysql_query($query, Aplicatie::getInstance()->getMYSQL()->getResource());
	
			while($dispozitie = mysql_fetch_array($result_zi))
			{	
				// perspectiva dinspre firmele spatiu
				
				if($dispozitie['tip'] == "plata")
				{	
					$this->addRow(array($this->getIndexNewRow(), htmlspecialchars($dispozitie['document']), $data_curenta->romanianFormat(), "DISP INCASARE DE LA CASA SPRE ".$dispozitie['denumire_firma'], $dispozitie['valoare'],0));
					$dispozitii->actualizeazaIncasari($dispozitie['valoare']);
				}
				else 
				{
					$this->addRow(array($this->getIndexNewRow(), htmlspecialchars($dispozitie['document']), $data_curenta->romanianFormat(), "DISP. PLATA DE LA ".$dispozitie['denumire_firma'].' SPRE CASA',  0, $dispozitie['valoare']));
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

		$result = mysql_query($q, Aplicatie::getInstance()->getMYSQL()->getResource());
		while($db = mysql_fetch_array($result))
		{
			$_total += intval($db['valoare']);
		}

		return $_total;
	}
}