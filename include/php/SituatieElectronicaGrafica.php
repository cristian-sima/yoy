<?php 

	require_once "Aparat.php";
	require_once "Utilizator.php";
	require_once "SituatieGrafica.php";
		
	
	/**
	 *
	 * Reprezinta o situatie electronica. O situatie electronica are un autor. Se bazeaza pe index-urile mecanice ale aparatelor. Contine informatii despre aparate
	 * @author			Cristian Sima
	 * @data			12.02.2014
	 * @version			1.2
	 *
	 */
	class SituatieElectronicaGrafica extends SituatieGrafica
	{
		
		/**
		 *
		 * Realizeaza o noua situatie, si initializeaza toate variabilele
		 * @param $from				Data de inceput a situatiei [@DataCalendaristica]
		 * @param $to				Data de sfarsit a situatiei [@DataCalendaristica]
		 * @param $firma			Referinta spre obiectul firma despre care se face situatia [@Firma]
		 *
		 */
		public function __construct($from, $to, $firma)
		{
			parent::__construct($from, $to, $firma, "electronice");			
		}
		
		
		/**
		 *
		 *	Calculeaza toate datele situatiei
		 *
		 */
		protected function _processData()
		{
			$autor 			= null;
			$activate		= (($this->getFrom() == $this->getTo())?true:false);
			
			$mysql = "	SELECT
								MIN(indexi.start_intrari) AS start_intrari,
								MAX(indexi.end_intrari) AS end_intrari,
								MIN(indexi.start_iesiri) AS start_iesiri,
								MAX(indexi.end_iesiri) AS end_iesiri,
								completare.autor,
								indexi.id_aparat
						FROM `index_mecanic` AS indexi
						LEFT JOIN `completare_electronica` AS completare
							ON completare.id = indexi.id_completare	
						LEFT JOIN `aparat` AS aparat
							ON indexi.id_aparat = aparat.id
						WHERE 	
								completare.id_firma = '".($this->getFirma()->getID())."' AND
								(completare.data_ >= '".$this->getFrom()."' AND completare.data_ <= '".$this->getTo()."')
						GROUP BY indexi.id_aparat
						ORDER by completare.data_,aparat.ordinea";	
		
			$result = mysql_query($mysql, Aplicatie::getInstance()->getMYSQL()->getResource()) or die(mysql_error());
			
			if(mysql_num_rows($result) != 0)
				$this->isCompletata 	=  true;
			
			while($situatie = mysql_fetch_array($result))
			{		
			
				$aparat 			=  new Aparat($situatie['id_aparat']);
				$total_intrari		= ($aparat->getFactorMecanic() * ($situatie['end_intrari'] - $situatie['start_intrari'])) * $aparat->getPretImpuls();
				$total_iesiri		= ($aparat->getFactorMecanic() * ($situatie['end_iesiri'] - $situatie['start_iesiri'])) * $aparat->getPretImpuls();
				
				
				$this->addAparat(array(
										"enable"	=>	false,
										"intrare"	=>	false,
										"iesire"	=>	false,
										"transfer"	=>	false
										),
									$aparat,
									array(
										"start_intrari" 	=> $situatie['start_intrari'],
										"end_intrari" 		=> $situatie['end_intrari'],
										"start_iesiri" 		=> $situatie['start_iesiri'],
										"end_iesiri" 		=> $situatie['end_iesiri']				
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