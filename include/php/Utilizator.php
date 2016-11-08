<?php
	
	/**
	 *	Encapsuleaza informatiile despre un utilizator
	 *  @author					Cristian Sima
	 *  @date					12.02.2014
	 *  @version				1.1
	 *
	 */
	class Utilizator
	{
		private $id;
		private $utilizator;
		private $nume;
		private $tipCont;
		private $id_firma;
		private $activ;
		private $tip_operator;
		
		/**
		 *
		 * Realizeaza un nou utilizator și stocheaza datele despre el.
		 * @param string $id		ID-ul utilizatorului [@String]
		 * @throw Exception			Atunci cand utilizatorul nu exista
		 *
		 */
		public function Utilizator($id)
		{
			GLOBAL $Aplicatie;
			
			$q = "SELECT user AS utilizator, nume, tipCont, idFirma as id_firma, activ, tipOperator FROM `utilizator` WHERE `id`='".$id."'";
			$result = mysql_query($q, Aplicatie::getInstance()->getMYSQL()->getResource());
						
			if(mysql_num_rows($result) == 0)
				throw new Exception("NoSuchUser");
				
			while($db = mysql_fetch_array($result))
			{
				$this->utilizator 	= $db['utilizator'];
				$this->nume 		= $db['nume'];
				$this->activ 		= $db['activ'];			
				$this->tipCont 		= $db['tipCont'];			
				$this->id_firma 	= $db['id_firma'];			
				$this->tip_operator	= $db['tipOperator'];			
			}
			$this->id = $id;
		}
	
		
		/**
		 *
		 * @description			Returneaza ID-ul utilizatorului
		 * @return				ID-ul utilizatorului
		 *
		 */
		public function getID()
		{
			return $this->id;
		}
		
		
		/**
		 *
		 * @description			Returneaza userul
		 * @return				Userul [@String]
		 *
		 */		
		public function getUtilizator()
		{
			return $this->utilizator;
		}
		
		/**
		 *
		 * @description			Returneaza numele real al utilizatorului
		 * @return				Numele real al utilizatorului
		 *
		 */		
		public function getNume()
		{
			return $this->nume;
		}
		
		/**
		 *
		 * @description			Verifica daca utilizatorul este activ sau nu
		 * @return				1 daca este, 0 daca nu
		 *
		 */		
		public function isActiv()
		{
			return $this->activ;
		}
		
		/**
		 *
		 * @description			Returneaza tip-ul contului
		 * @return				admin sau normal
		 *
		 */		
		public function getTipCont()
		{
			return $this->tipCont;
		}
		
		
		/**
		 *
		 * @description			Returneaza firma utilizatorului
		 * @return				Firma utilizatourlui [@String]
		 *
		 */		
		public function getIDFirma()
		{
			return $this->id_firma;
		}
		
		/**
		 *
		 * @description			Returneaza tipul operatorului
		 * @return				null pentru admin sau desktop și mobil
		 *
		 */		
		public function getTipOperator()
		{
			return $this->tip_operator;
		}
		
		/**
		 *
		 * @description			Verifica daca utilizatorul este administrator sau nu
		 * @return				true daca este administrator false daca nu
		 *
		 */		
		public function isAdministrator()
		{
			return ( $this->tipCont == "admin");
		}
		
		/**
		 *
		 * @description			Verifica daca utilizatorul este desktop sau nu
		 * @return				true daca este desktop false daca nu
		 *
		 */	
		public function isDesktop()
		{			
			return ($this->tip_operator == "desktop");	
		}
		
		
		/**
		 *
		 * @description			Verifica daca utilizatorul este operator sau nu
		 * @return				True daca este false daca nu
		 *
		 */		
		public function isOperator()
		{
			return ( $this->tipCont != "admin");
		}
		
		/**
		 *
		 * Returneaza o descriere a utilizatorului
		 * @return					O descriere a utilizatorului
		 *
		 */		 
		public function __toString()
		{
			return '[@Utilizator, id:'.$this->getID().', '.$this->getUtilizator().' - '.$this->getNume().']';
		}
	}