<?php

require_once "CarnetDeBilete.php";

/**
 *	@description 			Reprezinta obiectul cu toate biletele din perioada respectiva. Poate livra carnetele numarul de bilete
 *
 *  @author					Cristian Sima
 *  @date					10.02.2014
 *  @version				1.1
 *
 */
class Bilete
{
	protected 	$carnete = array();
	private 	$from;
	private 	$to;
	private 	$firma;


	/**
	 *
	 * @description 				Constructorul creaza un nou obiect Bilete si activeaza functia care calculeaza carnetele
	 * @param DataCalendaristica	$from				Data de inceput a perioadei
	 * @param DataCalendaristica	$to				Data de sfarsit a perioadei
	 * @param $id_firma				Id-ul firmei
	 *
	 */
	public function Bilete (DataCalendaristica $from, DataCalendaristica $to, $firma)
	{
		// FIXME schimbat din id firma in referinta firma
		
		if($from > $to)
		throw new Exception("Start [$from] trebuie sa fie mai mic decat sfarsit [$to]");
			
		$this->from 	= $from;
		$this->to		= $to;
		$this->firma 	= $firma;
			
		$this->process();
	}

	/**
	 *
	 * @description				Printeaza o descrie a tuturor carnetelor
	 * @return					Descrierea biletele petru perioada respectiva
	 *
	 */
	public function getDescription()
	{
		$colors = array("ED4545", "ED9945", "4556ED", "A645ED", "8545ED", "E345ED", "45C2ED", "45ED99", "ED45B4", "BBED45", "49ED45"); // 11
		$string 	= "";
		$nr 		= 0;
			
		foreach ($this->carnete as $carnet)
		{
			$string.= ("<span style='color:".$colors[$nr%11]."' class='bk_wt_prt'>".$carnet.'</span><br />');
			$nr++;
		}
			
		return $string;
	}

	/**
	 *
	 * @description				Returneaza numarul de bilete din perioada respectiva
	 * @return					Returneaza numarul de bilete
	 *
	 */
	public function getNumarulDeBilete()
	{
		$numar = 0;
			
		foreach ($this->carnete as $carnet)
		{
			$numar += $carnet->getNumarulDeBilete();
		}
			
		return $numar;
	}

	/**
	 *
	 * @description				Returneaza data de inceput a intervalului
	 * @return					Data de inceput a intervalului
	 *
	 */
	public function getFrom()
	{
		return $this->from;
	}

	/**
	 *
	 * @description				Returneaza data de sfarsit a intervalului
	 * @return					Data de sfarsit a intervalului
	 *
	 */
	public function getTo()
	{
		return $this->to;
	}

	/**
	 *
	 * @description				Returneaza firma
	 * @return					Firma
	 *
	 */
	public function getFirma()
	{
		return $this->firma;
	}

	/**
	 *
	 * @description				Returneaza carnetele pentru aceasta perioada si firma
	 * @return Array			Array cu carnete
	 *
	 */
	public function getCarnete()
	{
		return $this->carnete;
	}


	/**
	 *
	 * @description				Calculeaza carnetele de bilete si le pastreaza in variabila carnete
	 *
	 */
	private function process()
	{
			
		$c 			= null;
		$current 	= null;
			
		$query = "
						SELECT b.start, b.end
						FROM carnete_bilete AS b
						LEFT JOIN completare_bilete AS c
						ON c.id = b.id_completare
						WHERE c.data_>='".$this->getFrom()."' AND c.data_<='".$this->getTo()."' AND c.id_firma='".$this->firma->getID()."'
						ORDER by c.data_ ASC, b.id ASC";

		$response = mysql_query($query, Aplicatie::getInstance()->getMYSQL()->getResource()) or die(mysql_error());
			
		while($carnet_db = mysql_fetch_array($response))
		{
			if($c == null)
			{
				$c = new CarnetDeBilete($carnet_db['start'], $carnet_db['end']);
			}
			else
			{
				$current = new CarnetDeBilete($carnet_db['start'], $carnet_db['end']);
				try
				{
					$c->extend($current);
				}
				catch (Exception $e)
				{
					array_push($this->carnete, $c);
					$c = $current;
				}
			}
		}
		if($c != null)
		array_push($this->carnete, $c);
	}
}