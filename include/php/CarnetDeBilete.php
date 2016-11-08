<?php
	
	/**
	 *	Reprezinta un carnet de bilete. Acest carnet nu este complet si poate să conțină de la 1 la 100 de bilete. Carnetul incepe de la valoarea 01 si se termina la 000 cu alta serie. Seria carnetului se obtine din sfarsit sau inceput minus 1.
	 *  
	 *  @example
	 *
	 *	Un token este un numar de 5 cifre.
	 *  Seria reprezinta primele 3 cifre din (inceputul carnetului -1 sau sfarsitul carnetului - 1)
	 *
	 *  De exemplu pentru CARNETUL [12301 -> 12400]
	 *
	 *	Token inceput: 12301
	 *  Token sfarsit: 12400
	 *  Seria: 123
	 *	-------------------------------------
	 *  @author					Cristian Sima
	 *  @date					10.02.2014
	 *  @version				1.2
	 *
	 */
	class CarnetDeBilete 
	{
		private $seria;
		private $start;
		private $end;
		
		/**
		 *  Contructorul ia incaputul si sfarsitul si construieste un nou carnet. Daca seria nu este aceeasi, arunca o exceptie
		 *  
		 *  @param string $start					Token-ul de start al carnetului
		 *  @param string $end						Token-ul de sfarsit al carnetului
		 *  @throw Exception						Daca seria nu este aceeasi sau daca cel de inceput mai mare ca sfarsitul
		 */		
		public function CarnetDeBilete($start, $end)
		{	
			
			if((strlen($start)!=5) || (strlen($end) !=5))
			{
				throw new Exception ("Carnetul are token-uri cu format neacceptat (sub 5 cifre)");
			}
			
			
			if(!$this->checkSameSeria($start, $end))
			{
				throw new Exception("The start and end does not have the same seria. ".$start.' -> '.$end);
			}

			if($start > $end)
			{
				throw new Exception ("The start token should be smaller than the end token ".'['.$start.'->'.$end.']');
			}
			
			$this->seria = floor(intval($start-1)/100);
			$this->start = intval($start);
			$this->end   = intval($end);
		}
		
		
		
		
		/**
		 *
		 * @description				Returneaza seria carnetului
		 * @return					Seria carnetului este formata din primele 3 cifre
		 *
		 */
		public function getSeria()
		{
			return $this->seria;
		}
		
		/** 
		 *
		 * @description				Returneaza primul bilet din carnet (5 cifre)
		 * @return					Primul bilet din carnet
		 *
		 */
		public function getStart()
		{
			return $this->start;
		}
		
		/**
		 *
		 * @description				Returneaza token-ul de sfarsit (5 cifre)
		 * @return					Token-ul de sfarsit
		 *
		 */
		public function getEnd()
		{
			return $this->end;
		}
	
		/**
		 *
		 * @description				Returneaza token-ul de inceput
		 * @return					Token-ul de inceput
		 *
		 */
		public function getNumarulDeBilete()
		{
			return ($this->end - $this->start + 1);
		}
		
		
		/**
		 *
		 * @description				Returneaza o descriere a carnetului sub forma START -> SFARSIT
		 * @return 					O descriere a carnetului
		 *
		 */		 
		public function __toString()
		{
			return substr($this->start,0,3)."<b>".substr($this->start,3)."</b> -> ".substr($this->end,0,3).'<b>'.substr($this->end,3).'</b>';
		}
		


		/**
		 *
		 * @description					Verifica daca doua serii de bilete sunt la fel de la 2 token-uri
		 * @param String $start			Token-ul de start al carnetului [eg. 11101]
		 * @param String $end			Token-ul de sfarsit [eg. 11200]
		 * @return						False daca biletele nu au aceeasi serie, true in caz contrar
		 *
		 */		
		public static function checkSameSeria($start, $end)
		{
			$serie_start = floor(intval($start-1)/100);
			$serie_end = floor(intval($end-1)/100);
		
			return ($serie_end == $serie_start);				
		}	



		/**
		 *
		 * @description						Metoda combina carnetul de biltul cu un altul. De exemplu combinarea carnetelor [22201 -> 22290] cu [22291 -> 22293] produce carnetul [22201 -> 22293]
		 * @param CarneteDeBilete new		Noul carnet care va prelungi pe cel existent
		 * @throw Exception					Noul carnet trebuie sa fie de tip CarnetDeBilete
		 * @throw Exception					Carnetele trebuie sa aiba aceeasi serie
		 * @throw Exception					Carnetele nu trebuie sa se suprapuna
		 *
		 */
		public function extend($new)
		{
			if(get_class($new) != get_class($this))
				throw new Exception ("Exceptie: Noul carnet trebuie sa fie de tip CarnetDeBilete !");
			
			if($this->getSeria() != $new->getSeria())
				throw new Exception ("Carnetul ".$this.' si carnetul '.$new.' trebuie sa aiba aceeasi serie pentru a se putea extinde.');
				
			if(! ($this->getEnd()+1 == $new->getStart()))
				throw new Exception ("Noul carnet trebuie sa aiba o seria potrivita pentru a se putea exitinde. End1 ".$this->getEnd().', Start2: '.$new->getStart());
				
			$this->end = $new->getEnd();
		}			
	}