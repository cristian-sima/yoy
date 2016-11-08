<?php
	
	require_once "Firma.php";
		
	/**
	 *	Encapsuleaza informatiile despre firma organizatoare
	 *
	 *  @author					Cristian Sima
	 *  @date					27.01.2014
	 *  @version				1.0
	 *
	 */
	class FirmaOrganizatoare extends Firma
	{		
		private $patron			= null;
		
		
		/**
		 *
		 * Realizeaza o noua firma organizatoare și stocheaza datele despre ea.
		 * @param $MYSQL			Link spre resursa MYSQL
		 * @param $id				ID-ul firmei
		 * @throw Exception			Atunci cand firma nu exista
		 *
		 */
		public function FirmaOrganizatoare($MYSQL, $id)
		{
			
			$q = "SELECT nume AS denumire,patron, localitate AS locatie from `firma_organizatoare` WHERE id='".$id."' LIMIT 0,1";
			$result = mysql_query($q, $MYSQL->getResource());
			
			if(mysql_num_rows($result) == 0)
				throw new Exception("NoSuchFirmaOrganizatoare");
				
			while($firma = mysql_fetch_array($result))
			{
				$this->denumire 	= $firma['denumire'];
				$this->locatie 		= $firma['locatie'];			
				$this->patron 		= $firma['patron'];			
			}
			
			$this->id = $id;
		}
		
		
		
		/**
		 * 
		 * Returneaza patronul firmei organizatoare
		 * @return string			Patronul firmei organizatoare
		 * 
		 */
		public function getPatron()
		{
			return $this->patron;
		}
	}