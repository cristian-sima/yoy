<?php 

	require_once "SituatieMecanicaGraficaCompleta.php";
	require_once "DataCalendaristica.php";

	
	/**
	 *
	 * Reprezinta o situatie mecanica completa. O situatie mecanica are un autor. Se bazeaza pe index-urile mecanice ale aparatelor. Ofera o situatie pentru o zi data (daca exista data) sau cea mai recenta completare din urma. Pune și aparatele care au fost adaugate astazi, sau sterse și transferate
	 * @author			Cristian Sima
	 * @data			12.02.2014
	 * @version			1.2
	 *
	 */
	class SituatieMecanicaGraficaCompletaAzi extends SituatieMecanicaGraficaCompleta
	{
		
		/**
		 *
		 * Realizeaza o noua situatie
		 * @param Firma $firma			Referinta spre obiectul firma despre care se face situatia [@Firma]
		 *
		 */
		public function __construct($firma)
		{
			parent::__construct(new DataCalendaristica(date("Y-m-d")), $firma);			
		}
	
		
		/**
		 *
		 * Face sfarsitul dispozitivelor gol -> pentru a fi completat
		 *
		 */
		private function deteleEndAparate()
		{
			$A		= &$this->aparate;
			
			if (is_array($A))
			{
				foreach ($A as &$aparat) 
				{
					$aparat['situatie']['end_intrari'] 	= "";
					$aparat['situatie']['end_iesiri'] 	= "";					
				}
			}
		}
		
		
		/**
		 *
		 * Proceseaza datele
		 *
		 */
		 protected function _processData()
		 {					
			parent::_processData();
		 
			if($this->isFake())
			{
				$this->deteleEndAparate();			
			}
		}
	}
		