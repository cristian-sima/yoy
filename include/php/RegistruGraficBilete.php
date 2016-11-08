<?php

	require_once "include/php/Total.php";
	require_once "include/php/Bilete.php";
	require_once "include/php/Guvern.php";
	require_once "include/php/Romanian.php";
	require_once "include/php/FirmaSpatiu.php";
	require_once "include/php/RegistruGrafic.php";
	require_once "include/php/SituatieMecanica.php";
	require_once "include/php/DataCalendaristica.php";

/**
 *
 * Afiseaza situatia a biletelor desfasurata pentru o luna pentru toate firmele
 * @author			Cristian Sima
 * @data			13.02.2014
 * @version			1.0
 *
 */
class RegistruGraficBilete extends RegistruGrafic
{
	private $data		= null;
	private $start		= null;
	private $end		= null;
	
	/**
	 * 
	 * Realizeaza un obiect 
	 * 
	 * @param DataCalendaristica $data		Referinta spre obiectul data calendaristica
	 * 
	 */
	public function RegistruGraficBilete($data)
	{		
		parent::__construct($data);		
		parent::setTitle("SITUATIE LUNARA BILETE");
		parent::setPrimulRand("");
	}
	
	
	/**
	 * 
	 * Proceseaza datele pentru situatia bilete
	 *  
	 * @see SituatieRegistru::_processData()			
	 */
	protected function _processData()
	{
		$columns	= array(
								array(
									"content"	=> "NR. CRT",
									"width"		=> "50px"
								),
								array(
									"content" 	=> 	"SERII CARNETE",
									"width" 	=>	"160px" 
								),
								array(
									"content" 	=> 	"DENUMIRE FIRMA",
									"width" 	=>	"210px" 
								),
								array(
									"content" 	=> 	"LOCATIE FIRMA",
									"width" 	=>	"274px" 
								),
								array(
									"content" 	=> 	"INCASARI",
									"width"		=>	"286px" 
								)
							);
							
		parent::setColumns($columns);
		parent::setColoaneTotalizate(array(4));
		parent::setTotalTitleColumn(2);

		
		$pret_taxa_pe_bilet	= Guvern::getPretBilet($this->getFrom());
		$bilete				= new Total("Bilete");		 
		
		$result = mysql_query("SELECT b.id_firma,f.nume
								FROM completare_bilete AS b
								LEFT JOIN firma AS f
								ON f.id=b.id_firma
								WHERE b.data_ >='".$this->getFrom()."' AND b.data_ <='".$this->getTo()."'
								GROUP BY b.id_firma
								ORDER BY f.nume ASC", 
		Aplicatie::getInstance()->getMYSQL()->getResource());

		/*
		 * Cat timp sunt completari despre bilete in luna pentru firme, afisam firmele in ordine alfabetica
		 */
		while($db = mysql_fetch_array($result))
		{
			
			$data_curenta 		= new DataCalendaristica($this->getFrom());
			$firma				= new FirmaSpatiu($db['id_firma']);		
			$calcul_bilete 		= new Bilete($this->getFrom(), $this->getTo(), $firma);
		
			$total_firma		= new Total($firma->getDenumire().' ('.$firma->getLocatie().')');	
					
				foreach ($calcul_bilete->getCarnete() as $carnet) 
				{
					$numar_de_bilete  	= $carnet->getNumarulDeBilete();
					
					$_suma 				= $numar_de_bilete * $pret_taxa_pe_bilet;	
						
					if($numar_de_bilete != 0)
					{					
						$this->addRow(array($this->getIndexNewRow(), 
									"<span class='tabel_page_bilete_big'>".$carnet.'</span>',
									$firma->getDenumire(),
									$firma->getLocatie(),
									$_suma));
					}
					
					$total_firma->actualizeazaIncasari($_suma);
				}
				
				$this->addTotal($total_firma);
				$bilete->actualizeazaIncasari($total_firma->getIncasari());
			}		
		
		
		/* --------------- Adaugare totaluri ---------------- */
		
		$this->addTotal($bilete);	
	}
}