<?php

require_once "Bilete.php";

/**
 *	@description 			Reprezinta obiectul cu toate biletele din perioada respectiva. Poate livra carnetele numarul de bilete
 *
 *  @author					Cristian Sima
 *  @date					10.02.2014
 *  @version				1.2
 *
 */
class BileteGrafice extends Bilete
{
	private $default_ 		= array();
	private $enable_first	= true;

	/**
	 *
	 * @description 							Constructorul creaza un nou obiect Bilete si activeaza functia care calculeaza carnetele
	 * @param DataCalendaristica $from			Data de inceput a perioadei
	 * @param DataCalendaristica $to			Data de sfarsit a perioadei
	 * @param int $firma						Id-ul firmei
	 *
	 */
	public function BileteGrafice (DataCalendaristica $from, DataCalendaristica $to, $firma)
	{
		parent::__construct($from, $to, $firma);

		$this->_process();
	}


	/**
	 *
	 * @description				Returneaza default
	 * @return					Bilete default pentru aceea zi
	 *
	 */
	public function getDefault()
	{
		return $this->default_;
	}

	/**
	 *
	 * @description				Returneaza true daca prima seria a carnetului default se poate edita
	 * @return					true sau false
	 *
	 */
	public function getEnableFirst()
	{
		return $this->enable_first;
	}

	/**
	 *
	 * @description				Proceseaza datele
	 *
	 */
	public function _process()
	{
		GLOBAL $conn;
		$ultima_serie 		= "";
			
		$mysql = "	SELECT b.end
						FROM carnete_bilete AS b
						LEFT JOIN completare_bilete AS c
						ON c.id = b.id_completare
						WHERE c.data_<'".$this->getFrom()."' AND c.id_firma='".$this->getFirma()->getID()."'
						ORDER by c.data_ DESC, b.id DESC LIMIT 0,1";				

		$result = mysql_query($mysql, Aplicatie::getInstance()->getMYSQL()->getResource()) or die(mysql_error());
			
			
		while($situatie = mysql_fetch_array($result))
		{
			$ultima_serie = $situatie['end'];
		}


		if($this->getCarnete() == null)
		{
			if(($ultima_serie != "") && (CarnetDeBilete::checkSameSeria((intval($ultima_serie)+1),(intval($ultima_serie)))))
			{
				$this->default_ = array(($ultima_serie+1), "");
			}
			else
				$this->default_ = array("","");
		}
		else
		{
			$first_carnet		= array_shift($this->carnete);
			$this->default_ 	= array($first_carnet->getStart(), $first_carnet->getEnd());
		}
	
		if($ultima_serie != "")
			$this->enable_first = (!(CarnetDeBilete::checkSameSeria((intval($ultima_serie)+1),(intval($ultima_serie)))));
	}
}