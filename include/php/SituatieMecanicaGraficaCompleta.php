<?php 

	require_once "SituatieMecanica.php";
	require_once "SituatieMecanicaGrafica.php";

	
	/**
	 *
	 * Reprezinta o situatie mecanica completa. O situatie mecanica are un autor. Se bazeaza pe index-urile mecanice ale aparatelor. Ofera o situatie pentru o zi data (daca exista data) sau cea mai recenta completare din urma. Daca nu gaseste nicio situatie in urma nu afiseaza nimic.
	 * @author			Cristian Sima
	 * @data			12.02.2014
	 * @version			1.2
	 *
	 */
	class SituatieMecanicaGraficaCompleta extends SituatieMecanicaGrafica
	{
		private $isFake				= false;
		
		/**
		 *
		 * Realizeaza o noua situatie, si initializeaza toate variabilele
		 * 
		 * @param DataCalendaristica $from		Data de inceput a situatiei
		 * @param Firma $firma					Referinta spre obiectul firma despre care se face situatia [@Firma]
		 *
		 */
		public function __construct(DataCalendaristica $data, Firma $firma)
		{
			parent::__construct($data, $data, $firma);			
		}
		
		/**
		 *
		 *  Reseteaza totalul incasari, totalul premii si totalul sertar la 0
		 *
		 */
		protected function resetTotaluri()
		{					
			$this->total['incasari']	= 0;
			$this->total['premii']		= 0;
			$this->total['sertar']		= 0;
		}
		
		
		/**
		 * Verifica daca situatia a fost completata in ziua respectiva sau este doar o situatie preluata din trecut
		 */
		public function isFake()
		{
			return $this->isFake;
		}
		
		/**
		 *
		 * 	Schimba ziua situatiei
		 * @param DataCalendaristica $from				Noua data a situatiei
		 *
		 */
		protected function changeDay($data)
		{
			$this->from 		=	$data;
			$this->to			= 	$data;			
		}
		
		
		
		
		/**
		 *
		 * 		Sterge orice fel de incasari la toate aparatele
		 *
		 */
		private function makeNoProfit()
		{
			$A = 	&$this->aparate;
			
			if (is_array($A))
			{
				foreach ($A as &$aparat) 
				{			
					$situatie					= &$aparat['situatie'];
					$situatie['start_intrari'] 	= $situatie['end_intrari'];
					$situatie['start_iesiri'] 	= $situatie['end_iesiri'];
					$situatie['diferenta_1']	= 0;
					$situatie['diferenta_2']	= 0;
				}
			}
		}
		
	
		/**
		 *
		 * Metoda sterge autorul situatiei.
		 * 
		 */
		protected function deleteAutor()
		{
			$this->autor = null;
		}
		
		
		/**
		 *
		 * Proceseaza datele
		 *
		 */
		 protected function _processData()
		 {
		 	
			$cea_mai_recenta_data 	= SituatieMecanica::getUltimaCompletare($this->getFirma(), $this->getFrom());
			
			//cho 'a: '.$cea_mai_recenta_data.'<br />';
	
			try
			{
				if($cea_mai_recenta_data == null)
					throw new Exception("No exista situatie");

				$_temp = new DataCalendaristica($this->getFrom().'');
				
				/*
				 * Force to create a clone
				 */
				$data1 = $this->getFrom()->__toString() ;
				$data2 = new DataCalendaristica($cea_mai_recenta_data);
					
				
				if(strtotime($data1) !=  strtotime($cea_mai_recenta_data))
				{
					$this->changeDay($data2);		

					/*
					 * 
					 * Schimb filterul pentru ca sa eliminam aparatele care au fost scoase sau mutate de la ultima completare pana in ziua ceruta
					 * 
					 */
					$this->setFilterTo($data1);
					$this->setFilterFrom($data1);				
				}
				
				
				parent::_processData();
			
				if(strtotime($data1) !=  strtotime($cea_mai_recenta_data))
				{
					$this->isFake = true;
					$this->deleteAutor();
					$this->changeDay(new DataCalendaristica($_temp));
					$this->makeNoProfit();
					$this->resetTotaluri();	
				}
			}
			catch(Exception $e)
			{
				// echo $e->getMessage();
				// to avoid the fact there is no situatie
			}
		}
	}
		