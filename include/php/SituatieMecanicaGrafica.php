<?php 

	require_once "Aparat.php";
	require_once "Utilizator.php";
	require_once "SituațieGrafica.php";
		
	
	/**
	 *
	 * Reprezinta o situatie mecanica. O situatie mecanica are un autor. Se bazeaza pe index-urile mecanice ale aparatelor. Contine informatii despre aparate. Aceasta data este folosita pentru a filtra aparatele care vor fi afisate pentru aceea perioada Aceasta data este folosita pentru a filtra aparatele care vor fi afisate pentru aceea perioada
	 *
	 * @author			Cristian Sima
	 * @data			12.02.2014
	 * @version			1.2
	 *
	 */
	class SituațieMecanicaGrafica extends SituațieGrafica
	{
		private $idCompletare		= null;
		private $filterFrom			= null;
		private $filterTo			= null;
		
		
		/**
		 *
		 *  Realizeaza o noua situatie, si initializeaza toate variabilele
		 * @param Firma $from						Data de inceput a situatiei [@DataCalendaristica]
		 * @param DataCalendaristica $to			Data de sfarsit a situatiei [@DataCalendaristica]
		 * @param DataCalendaristica $firma			Referinta spre obiectul firma despre care se face situatia [@Firma]
		 *
		 */
		public function __construct($from, $to, $firma)
		{
			
			$this->filterFrom		= $from;
			$this->filterTo			= $to;
			
			parent::__construct($from, $to, $firma, "mecanice");	
		}
		
		/**
		 * 
		 * Returneaza id-ul situatiei mecanice
		 * 
		 * @return				ID-ul situatie mecanice
		 */
		public function getIDCompletare()
		{
			return $this->idCompletare;
		}
		
		/**
		 * 
		 * Returneaza data de filtrare. Aceasta data filter este folosita pentru a sterge aparatele care nu mai sunt disponibile pentru ziua from
		 * 
		 * @return				Data filter
		 * 
		 */
		protected function getFilterDateFrom()
		{
			return $this->filterFrom;
		}
		
		/**
		 * 
		 * Seteaza filterul de inceput
		 * 
		 * @param DataCalendaristica $filter		Filterul
		 * 
		 */
		protected function setFilterFrom($filter)
		{
			$this->filterFrom = $filter;
		}
		
		
		/**
		 * 
		 * Seteaza filterul de final
		 * 
		 * @param DataCalendaristica $filter		Filterul
		 * 
		 */
		protected function setFilterTo($filter)
		{
			$this->filterTo = $filter;
		}
		
		
		/**
		 * 
		 * Returneaza data de filtrare. Aceasta data filter este folosita pentru a sterge aparatele care nu mai sunt disponibile pentru ziua from
		 * 
		 * @return				Data filter
		 * 
		 */
		protected function getFilterDateTo()
		{
			return $this->filterTo;
		}
		
		/**
		 *
		 * 	Calculeaza toate datele situatiei
		 *
		 */
		protected function _processData()
		{
			
			$autor 			= null;
			$activate		= (($this->getFrom() == $this->getTo())?true:false);
	
			$mysql = "	
						SELECT
								MIN(indexi.start_intrari) AS start_intrari,
								MAX(indexi.end_intrari) AS end_intrari,
								MIN(indexi.start_iesiri) AS start_iesiri,
								MAX(indexi.end_iesiri) AS end_iesiri,
								completare.autor,
								indexi.id_aparat,
								completare.id AS token
						FROM `index_mecanic` AS indexi
						LEFT JOIN `completare_mecanica` AS completare
							ON completare.id = indexi.id_completare						
						LEFT JOIN `aparat` AS aparat
							ON indexi.id_aparat = aparat.id
						WHERE 	
							  exists
								(
									SELECT id FROM istoric_aparat AS istoric
									WHERE  istoric.id_aparat = indexi.id_aparat    AND
										   istoric.id_firma  = completare.id_firma AND											
											 (
											 	(istoric.is_now='0' AND istoric.from_ <= '".$this->getFilterDateFrom()."' AND '".$this->getFilterDateTo()."' <= istoric.to_) OR
											 	(istoric.is_now='0' AND istoric.to_   <= '".$this->getFilterDateTo()."'   AND istoric.to_   >= '".$this->getFilterDateFrom()."') OR
											 	(istoric.is_now='0' AND istoric.from_ >= '".$this->getFilterDateFrom()."' AND istoric.from_ <= '".$this->getFilterDateTo()."') OR
											 	(istoric.is_now='1' AND istoric.from_ <= '".$this->getFilterDateTo()."'  )
											 )			
								) AND
								completare.id_firma = '".($this->getFirma()->getID())."' AND
								(completare.data_ >= '".$this->getFrom()."' AND completare.data_ <= '".$this->getTo()."')
						GROUP BY indexi.id_aparat
						ORDER by completare.data_,aparat.ordinea";	

			
			// echo "<pre>".$mysql.'</pre>';
			
			
			$result = mysql_query($mysql, Aplicatie::getInstance()->getMYSQL()->getResource()) or die(mysql_error());
			
			if(mysql_num_rows($result) != 0)
			{
				$this->isCompletata 	=  true;
			}
				
			while($situatie = mysql_fetch_array($result))
			{	
				$this->idCompletare = $situatie['token'];
							
				$aparat 			=  new Aparat($situatie['id_aparat']);
				$total_intrari		= ($aparat->getFactorMecanic() * ($situatie['end_intrari'] - $situatie['start_intrari'])) * $aparat->getPretImpuls();
				$total_iesiri		= ($aparat->getFactorMecanic() * ($situatie['end_iesiri'] - $situatie['start_iesiri'])) * $aparat->getPretImpuls();
								
				$dif1 	  			= $aparat->getFactorMecanic() * ($situatie['end_intrari'] - $situatie['start_intrari']);
				$dif2 	  			= $aparat->getFactorMecanic() * ($situatie['end_iesiri'] - $situatie['start_iesiri']);

				$this->addAparat(
									$aparat,
									array(
										"start_intrari" 	=> $situatie['start_intrari'],
										"end_intrari" 		=> $situatie['end_intrari'],
										"start_iesiri" 		=> $situatie['start_iesiri'],
										"end_iesiri" 		=> $situatie['end_iesiri'],
										"diferenta_1"		=> $dif1,
										"diferenta_2"		=> $dif2			
									)
								);	

				$this->calculeazaTotal($total_intrari, $total_iesiri);				
				$autor = $situatie['autor'];								
			}			
			try
			{	
				$this->autor		= new Utilizator($autor);
			}
			catch(Exception $e)
			{
				$this->autor		= null;
			}
		}
	}