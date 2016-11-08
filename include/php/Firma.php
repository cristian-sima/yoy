<?php

	/**
	 *	Encapsuleaza informatiile despre o firma
	 *
	 *  @author					Cristian Sima
	 *  @date					12.02.2014
	 *  @version				1.1
	 *
	 */
	abstract class Firma
	{
		protected $id;
		protected $denumire;
		protected $locatie;
		
		
		/**
		 *
		 * Returneaza ID-ul firmei
		 * @return				ID-ul firmei
		 *
		 */
		public function getID()
		{
			return $this->id;
		}
		
		
		/**
		 *
		 * Returneaza locatia firmei
		 * @return				Locatia firmei
		 *
		 */		
		public function getLocatie()
		{
			return $this->locatie;
		}
		
		/**
		 *
		 * Returneaza denumirea firmei
		 * @return				Denumirea firmei
		 *
		 */		
		public function getDenumire()
		{
			return $this->denumire;
		}
		
		/**
		 *
		 * Returneaza o descriere a firmei
		 * @return					O descriere a firmei
		 *
		 */		 
		public function __toString()
		{
			return '[@Firma, id:'.$this->getID().', '.$this->getDenumire().' - '.$this->getLocatie().']';
		}
	}