<?php 

	require_once "SituatieMecanicaGraficaCompleta.php";
	require_once "DataCalendaristica.php";

	
	/**
	 *
	 * Aceasta situatie contine toate aparatele care mai exista in firma in prezent și adauga aparatul primit prin parametrii	 * @author			Cristian Sima
	 * @data			12.02.2014
	 * @version			1.2
	 *
	 */
	class SituatieMecanicaGraficaCompletaAparatNou extends SituatieMecanicaGraficaCompleta
	{
		/**
		 *
		 * Realizeaza o noua situatie. Aceasta situatie contine toate aparatele care mai exista in firma in prezent și adauga aparatul primit prin parametrii
		 * 
		 * @param Firma $firma				Referinta spre obiectul firma despre care se face situatia [@Firma]
		 * @param Aparat $aparat			Referinta spre aparat
		 * @param string $index_intrare		Index-ul de intrare pentru aparat
		 * @param string $index_intrare		Index-ul de iesire pentru aparat
		 * 
		 *
		 */
		public function __construct(Firma $firma, Aparat $referinta_aparat, $index_intrare, $index_iesire)
		{				
			$situatie		= array(
									"start_intrari" 	=> $index_intrare,
									"end_intrari" 		=> $index_intrare,
									"start_iesiri"		=> $index_iesire,
									"end_iesiri"		=> $index_iesire
							);
										
			parent::__construct(new DataCalendaristica(date("Y-m-d")), $firma);	
			parent::addAparat($referinta_aparat, $situatie);				
		}		
	}		