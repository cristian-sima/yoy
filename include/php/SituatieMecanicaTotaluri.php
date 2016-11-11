<?php

	require_once "Situatie.php";

	/**
	 *
	 * Reprezinta o situatie mecanica doar cu totaluri. Este foarte rapida, dar nu contine detalii despre situatie. Daca doriti sa vedeti una compelta vezi SituatieMecanica.php
	 * @author			Cristian Sima
	 * @data			10.02.2014
	 * @version			1.1
	 *
	 */
	class SituatieMecanicaTotaluri extends Situație
	{
		private $numarulDeAparate	= 0;

		/**
		 *
		 * Realizeaza o noua situatie mecanica cu totaluri, și initializeaza toate variabilele
		 * @param DataCalendaristica $from		Data de inceput a situatiei [@DataCalendaristica]
		 * @param DataCalendaristica $to		Data de sfarsit a situatiei [@DataCalendaristica]
		 * @param Firma $firma					Referinta spre obiectul firma despre care se face situatia [@Firma]
		 *
		 */
		public function __construct(DataCalendaristica $from, DataCalendaristica $to, Firma $firma)
		{
			parent::__construct($from, $to, $firma);
		}



		/**
		 *
		 * Preia datele situatiei
		 *
		 */
		protected function _processData()
		{

			$mysql = "	SELECT
								sum(situatie.total_incasari) as total_incasari

						FROM  `completare_mecanica` AS situatie
						WHERE 	id_firma    =  '".$this->getFirma()->getID()."' AND
								data_		>= '".$this->getFrom()."' 			AND
								data_		<= '".$this->getTo()."'
						LIMIT 0,1";

			$result = mysql_query($mysql, Aplicatie::getInstance()->getMYSQL()->getResource()) or die(mysql_error());

			if(mysql_num_rows($result) != 0)
			{
				$this->isCompletata 	=  true;
			}


			while($situatie = mysql_fetch_array($result))
			{
				$this->calculeazaTotal($situatie['total_incasari']);
			}
		}
	}
