<?php 
		
	require_once "Situatie.php";
	
	/**
	 *
	 * Reprezinta o situatie mecanica. Se bazeaza pe index-urile mecanice ale aparatelor
	 * @author			Cristian Sima
	 * @data			10.02.2014
	 * @version			1.1
	 *
	 */
	class SituatieMecanica extends Situație
	{
		private $numarulDeAparate	= 0;
		private $id;
		
		/**
		 *
		 * Realizeaza o noua situatie mecanica, și initializeaza toate variabilele
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
		 * Calculeaza toate datele situatiei
		 *
		 */
		protected function _processData()
		{
			
			$mysql = "	SELECT
								MIN(indexi.start_intrari) AS start_intrari,
								MAX(indexi.end_intrari) AS end_intrari,
								MIN(indexi.start_iesiri) AS start_iesiri,
								MAX(indexi.end_iesiri) AS end_iesiri, 
								aparat.factor_mecanic,
								aparat.id AS id_aparat,
								aparat.pret_impuls,
								indexi.id_aparat,
								completare.id AS id_situatie
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
											 	(istoric.is_now='0' AND istoric.from_ <= '".$this->getFrom()."' AND '".$this->getTo()."' <= istoric.to_) OR
											 	(istoric.is_now='0' AND istoric.to_   <= '".$this->getTo()."'   AND istoric.to_   >= '".$this->getFrom()."') OR
											 	(istoric.is_now='0' AND istoric.from_ >= '".$this->getFrom()."' AND istoric.from_ <= '".$this->getTo()."') OR
											 	(istoric.is_now='1' AND istoric.from_ <= '".$this->getTo()."'  )
											 )			
								) AND
								completare.id_firma = '".($this->getFirma()->getID())."' AND
								(completare.data_ >= '".$this->getFrom()."' AND completare.data_ <= '".$this->getTo()."')
								
						GROUP BY indexi.id_aparat
						ORDER by completare.data_";	

		
			$result = mysql_query($mysql, Aplicatie::getInstance()->Database) or die(mysql_error());
			
			if(mysql_num_rows($result) != 0)
				$this->isCompletata 	=  true;

			$nr_de_aparate				= 0;
				
			while($index = mysql_fetch_array($result))
			{
			
				$this->id 			= $index["id_situatie"]; 
				
				$total_intrari		= ($index['factor_mecanic'] * ($index['end_intrari'] - $index['start_intrari']))*$index['pret_impuls'];
				$total_iesiri		= ($index['factor_mecanic'] * ($index['end_iesiri'] - $index['start_iesiri']))*$index['pret_impuls'];
			
				$this->calculeazaTotal($total_intrari, $total_iesiri);

				$nr_de_aparate++;
			}
			
			$this->numarulDeAparate = $nr_de_aparate;
		}
		
		/**
		  * Returneaza id-ul situatie
		  * @return (number) The id-ul situatiei
		  */
		public function getId(){
			return $this->id;
		}
		
		
		/**
		 * Returneaza numarul de aparate din situatie
		 * @return 				Numarul de aparate
		 */
		public function getNumarulDeAparate()
		{
			return $this->numarulDeAparate;
		}
		
		
		/**
		 * Returneaza ultima situatie mecanica completata mai mica sau egala decat data pentru o firma
		 *
		 * @param Firma $firma					Firma pentru care se doreste situatia
		 * @param DataCalendaristica $data		Data pentru care se doreste situatia
		 * 
		 * @return								Data cand a fost completata o situatie mecanica care este mai mica sau egala decat data
		 */
		public static function getUltimaCompletare(Firma $firma, DataCalendaristica $data)
		{
			return self::getZiuaCompletata($firma, $data, "<=", "DESC");			
		}
		
	
		
		/**
		 * Returneaza ultima situatie mecanica completata mai mica decat data data pentru o firma data
		 *
		 * @param Firma $firma					Firma pentru care se doreste situatia
		 * @param DataCalendaristica $data		Data pentru care se doreste situatia
		 * 
		 * @return								Data cand a fost completata o situatie mecanica care este mai mica decat data data
		 */
		public static function getUltimaCompletareStrict(Firma $firma, DataCalendaristica $data)
		{
			return self::getZiuaCompletata($firma, $data, "<", "DESC");			
		}
		
		
			
		/**
		 * Returneaza prima situatie mecanica completata mai mare sau egala decat data pentru o firma
		 *
		 * @param Firma $firma					Firma pentru care se doreste situatia
		 * @param DataCalendaristica $data		Data pentru care se doreste situatia
		 * 
		 * @return								Data cand a fost completata o situatie mecanica care este mai mare sau egala decat data
		 */
		public static function getUrmatoareaCompletare(Firma $firma, DataCalendaristica $data)
		{			
			return self::getZiuaCompletata($firma, $data, ">=", "DESC");
		}
		
		
		/**
		 * Returneaza prima situatie mecanica completata mai mare decat data pentru o firma
		 *
		 * @param Firma $firma					Firma pentru care se doreste situatia
		 * @param DataCalendaristica $data		Data pentru care se doreste situatia
		 * 
		 * @return								Data cand a fost completata o situatie mecanica care este mai mare decat data
		 */
		public static function getUrmatoareaCompletareStrict(Firma $firma, DataCalendaristica $data)
		{	
			return self::getZiuaCompletata($firma, $data, ">", "ASC");	
		}
		
		/**
		 * Returneaza o data pentru o situatie completata in
		 *
		 * @param Firma $firma							Firma pentru care se doreste situatia
		 * @param DataCalendaristica $data				Data calendaristica
		 * @param string $semn							>, <, >=, <=
		 * @param string $order							ASC sau DESC
		 */
		private static function getZiuaCompletata($firma, $data, $semn, $order)
		{
			$data_ = null;
			
			$mysql = "	SELECT
								data_
						FROM `completare_mecanica` 				
						WHERE 	
								id_firma = '".($firma->getID())."' AND
								data_".$semn."'".$data."'
						ORDER by data_ ".$order." LIMIT 0,1";				
					
			$result = mysql_query($mysql, Aplicatie::getInstance()->Database) or die(mysql_error());
		
			while($situatie = mysql_fetch_array($result))
			{	
				$data_ 	= $situatie['data_'];
			}
			
			return $data_;
		}
	}