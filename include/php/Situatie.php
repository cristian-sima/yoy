<?php

require_once "DataCalendaristica.php";
require_once "Firma.php";

/**
 *
 *
 * Reprezinta o situatie. Aceasta situatie contine aparatele si totalurile
 *
 *
 * @author			Cristian Sima
 * @data			31.01.2014
 * @version			1.1
 *
 */
abstract class Situație
{
	private   $firma		= null;
	protected $from			= null;
	protected $to			= null;
	protected $total		= null;
	protected $isCompletata	= null;

	/**
	 *
	 *
	 * Realizeaza o noua situatie, si initializeaza toate variabilele
	 *
	 * @param DataCalendaristica $from				Data de inceput a situatiei [@DataCalendaristica]
	 * @param DataCalendaristica $to				Data de sfarsit a situatiei [@DataCalendaristica]
	 * @param Firma $firma							Referinta spre obiectul firma despre care se face situatia [@Firma]
	 *
	 */
	protected function Situație(DataCalendaristica $from, DataCalendaristica $to, Firma $firma)
	{
		$this->from 				= $from;
		$this->to					= $to;
		$this->firma				= $firma;
		$this->isCompletata		 	= false;
			
		$this->total				= array();
		$this->total['incasari']	= 0;
		$this->total['premii']		= 0;
		$this->total['sertar']		= 0;

		$this->_processData();
	}


	/**
	 *
	 * Returneaza data de inceput a intervalului
	 * @return DataCalendaristica			Data de inceput a intervalului
	 *
	 */
	public function getFrom()
	{
		return $this->from;
	}


	/**
	 *
	 * Returneaza data de inceput a intervalului
	 * @return boolean					Data de inceput a intervalului
	 *
	 */
	public function isCompletata()
	{
		return $this->isCompletata;
	}

	/**
	 *
	 * Returneaza data de sfarsit a intervalului
	 * @return DataCalendaristica					Data de sfarsit a intervalului
	 *
	 */
	public function getTo()
	{
		return $this->to;
	}

	/**
	 *
	 * Returneaza referinta spre obiectul firma
	 * @return Firma					Referinta spre obiectul firma
	 *
	 */
	public function getFirma()
	{
		return $this->firma;
	}



	/**
	 *
	 * Returneaza o descriere a situatiei
	 * @return					O descriere a situatiei
	 *
	 */
	public function __toString()
	{
		return "[@Situație, ".$this->getFrom().' -> '.$this->getTo().' for '.$this->getFirma().']';
	}

	/**
	 *
	 * Actualizeaza totalul
	 * @param int $intrari		Suma pentru incasari
	 * @param int $iesiri		Suma pentru premii
	 *
	 */
	protected function calculeazaTotal($intrari, $iesiri)
	{
		$this->total['incasari'] 	+= $intrari;
		$this->total['premii'] 		+= $iesiri;
		$this->total['sertar'] 		= $this->total['incasari'] - $this->total['premii'];
	}


	/**
	 *
	 * Returneaza totalul de bani ramasi in sertar (diferenta intre incasari si premii)
	 * @return int				Totalul de bani ramas in sertar
	 *
	 */
	public function getTotalInSertar()
	{
		return $this->total['sertar'];
	}

	/**
	 *
	 * Returneaza totalul de premii
	 * @return int				Totalul de premii
	 *
	 */
	public function getTotalPremii()
	{
		return $this->total['premii'];
	}

	/**
	 *
	 * Returneaza totalul de incasari
	 * @return int				Totalul de incasari
	 *
	 */
	public function getTotalIncasari()
	{
		return $this->total['incasari'];
	}

	/**
	 *
	 * Returneaza referinta spre array-ul cu totaluri
	 * @return					Referinta spre array-ul cu totaluri
	 *
	 */
	public function getTotal()
	{
		return $this->total;
	}

	/**
	 *
	 * Calculeaza toate datele situatiei
	 *
	 */
	protected abstract function _processData();
}