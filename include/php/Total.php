<?php

/**
 *
 * Reprezinta un total contabil. Contine plati si incasari. Isi poate actualiza valorile
 * @author			Cristian Sima
 * @data			12.02.2014
 * @version			1.1
 *
 */
class Total
{
	private $nume		= "fara_nume";
	private $plati		= 0;
	private $incasari	= 0;
	
	/**
	 * Realizeaza un nou obiect Balanta si reseteaza toate totalurile la 0
	 * 
	 * @param stirng $nume				Numele totalului
	 * 
	 */
	public function Total($nume)
	{
		$this->incasari 	= 0;
		$this->plati    	= 0;
		$this->nume			= $nume;
	}	
	
	/**
	 * 
	 * Acutualizeaza incasarile folosind parametrul. Adauga la incasari suma indicata
	 * 
	 * @param int $incasari			Suma pentru incasari
	 */
	public function actualizeazaIncasari($incasari)
	{
		$this->incasari 	+= $incasari;
	}
	

	/**
	 * 
	 * Acutualizeaza platile. Adauga la plati suma indicata
	 * 
	 * @param float $plati			Suma pentru plati
	 */
	public function actualizeazaPlati($plati)
	{
		$this->plati		+= $plati;
	}
	
	
	/**
	 * 
	 * Acutualizeaza totalul
	 * 
	 * @param int $incasari			Suma pentru incasari
	 * @param int $plati			Suma pentru plati
	 */
	public function actualizeazaTotal($incasari, $plati)
	{
		$this->incasari 	+= $incasari;
		$this->plati		+= $plati;
	}
	
	
	/**
	 * Returneaza diferenta dintre plati si incasari
	 */
	public function getTotal()
	{
		return ($this->incasari - $this->plati);
	}
	
	
	/**
	 * Returneaza suma totala care reprezinta platile
	 */
	public function getPlati()
	{
		return $this->plati;
	}
	
	
	/**
	 * Returneaza totalul de incasari
	 */
	public function getIncasari()
	{
		return $this->incasari;
	}
	
	/**
	 * Numele totalului
	 */
	public function getNume()
	{
		return $this->nume;
	}
}