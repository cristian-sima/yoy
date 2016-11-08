<?php
	
	require_once "DataCalendaristica.php";
		
	/**
	 *	@description 			Encapsuleaza informatiile despre o aparat
	 *
	 *  @author					Cristian Sima
	 *  @date					31.01.2014
	 *  @version				1.1
	 *
	 */
	class Aparat
	{
		private $id;
		private $nume;
		private $serie;
		private $firma;
		private $activ;
		private $factor_mecanic;
		private $pret_impuls;
		private $data_inspectie;
		private $data_autorizatie;
		private $inDepozit;
		
		/**
		 *
		 * @description				Realizeaza un nou aparat
		 * @param String $id		ID-ul aparatul
		 * @throw Exception			Atunci cand aparatul nu exista
		 *
		 */
		public function Aparat($id)
		{
			GLOBAL $conn;
			
			$q = "SELECT in_depozit As inDepozit, ordinea,observatii,serie,nume,factor_mecanic,pret_impuls,id_firma,data_inspectie,data_autorizatie,activ  FROM `aparat` WHERE `id`='".$id."'";
			
			$result = mysql_query($q, Aplicatie::getInstance()->getMYSQL()->getResource());
			
			if(mysql_num_rows($result) == 0)
			{
				throw new Exception("NoSuchDevice");
			}
				
			while($db = mysql_fetch_array($result))
			{
				$this->serie 			= $db['serie'];
				$this->nume				= $db['nume'];
				$this->factor_mecanic	= $db['factor_mecanic'];
				$this->pret_impuls		= $db['pret_impuls'];
				$this->firma			= $db['id_firma'];
				$this->data_inspectie	= $db['data_inspectie'];
				$this->data_autorizatie	= $db['data_autorizatie'];
				$this->activ			= $db['activ'];
				$this->observatii		= $db['observatii'];
				$this->ordinea			= $db['ordinea'];
				$this->inDepozit		= $db['inDepozit'];			
			}
			
			$this->id = $id;
		}
		
		/**
		 *
		 * @description			Returneaza observatiile despre aparat
		 * @return String		Observatiile despre aparat
		 *
		 */
		public function getObservatii()
		{
			return $this->observatii;
		}
		
		/**
		 *
		 * @description			Returneaza ID-ul aparatului
		 * @return in 			ID-ul aparatului
		 *
		 */
		public function getID()
		{
			return $this->id;
		}
		
		
		/**
		 *
		 * @description			Returneaza ordinea aparatului. Aceasta valoare este folosita pentru a afișa aparatele intr-o anumita ordine
		 * @return int			Ordinea aparatului stabilita de administrator.
		 *
		 */
		public function getOrdinea()
		{
			return $this->ordinea;
		}
		
		
		
		/**
		 *
		 * @description			Returneaza factorul mecanic
		 * @return float		Factorul mecanic
		 *
		 */
		public function getFactorMecanic()
		{
			return $this->factor_mecanic;
		}
		
		/**
		 *
		 * @description			Returneaza numele aparatului
		 * @return String		Numele aparatului
		 *
		 */		
		public function getNume()
		{
			return $this->nume;
		}
		
		
		/**
		 *
		 * @description			Returneaza seria aparatului
		 * @return String		Seria aparatului
		 *
		 */		
		public function getSerie()
		{
			return $this->serie;
		}
		
		/**
		 *
		 * @description			Returneaza pretul pe impuls al aparatului
		 * @return				Pretul pe impuls al aparatului
		 *
		 */		
		public function getPretImpuls()
		{
			return $this->pret_impuls;
		}
			
		/**
		 *
		 * @description			Returneaza firma aparatului unde se afla in prezent
		 * @return				Firma aparatului unde se afla in prezent [@String]
		 *
		 */		
		public function getFirmaCurenta()
		{
			return $this->firma;
		}
		
		/**
		 *
		 * @description			Verifica daca aparatul este activ sau nu
		 * @return				True daca apratul e activ, false otherwise
		 *
		 */
		 public function isActiv()
		{
			return (($this->activ == '1')?true:false);
		}
		
		
		/**
		 *
		 * @description			Verifica daca aparatul este activ sau nu
		 * @return boolean		True daca apratul e activ, false otherwise
		 *
		 */
		 public function isInDepozit()
		{
			return (($this->inDepozit == '1')?true:false);
		}
		
		
		/**
		 *
		 * @description			Returneaza data cand expira autorizatia aparatului
		 * @return				Data cand expira autorizatia aparatului
		 *
		 */
		 public function getDataAutorizatie()
		{
			return $this->data_autorizatie;
		}
		
		
		/**
		 *
		 * @description			Returneaza data cand expira inspectia
		 * @return				Data cand expira inspectia aparatului
		 *
		 */
		 public function getDataInspectie()
		{
			return $this->data_inspectie;
		}
		
		
		/**
		 *
		 * @description				Returneaza o descriere a aparatului
		 * @return Stirng			O descriere a aparatului
		 *
		 */		 
		public function __toString()
		{
			return '[@Aparat, id:'.$this->getID().', '.$this->getNume().' - '.$this->getSerie().']';
		}
	}