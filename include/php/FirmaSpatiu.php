<?php

	require_once "Firma.php";

	/**
	 *	Encapsuleaza informatiile despre o firma care detine spatiul
	 *
	 *  @author					Cristian Sima
	 *  @date					27.01.2014
	 *  @version				1.0
	 *
	 */
	class FirmaSpatiu extends Firma
	{
		private $activa;
		private $comentarii;
		private $dateContact;

		/**
		 *
		 * Realizeaza o noua firma și stocheaza datele despre ea.
		 * @param string $id				ID-ul firmei
		 * @throw Exception					Atunci cand firma nu exista
		 *
		 */
		public function FirmaSpatiu($id)
		{

			$q = "SELECT dateContact, comentarii, nume AS denumire,localitate AS locatie,activa from `firma` WHERE `id`='".$id."'";
			$result = mysql_query($q, Aplicatie::getInstance()->getMYSQL());

			if(mysql_num_rows($result) == 0)
				throw new Exception("Nu exista aceasta companie cu id-ul [".$id.']');

			while($firma = mysql_fetch_array($result))
			{
				$this->denumire 	= $firma['denumire'];
				$this->locatie 		= $firma['locatie'];
				$this->activa 		= $firma['activa'];
				$this->comentarii	= $firma['comentarii'];
				$this->dateContact  = $firma['dateContact'];
			}

			$this->id = $id;
		}


		/**
		 *
		 * Verifica daca firma este activa sau nu
		 * @return				True daca firma e activa, false otherwise
		 *
		 */
		 public function isActiva()
		{
			return (($this->activa == '1')?true:false);
		}

		/**
		 *
		 * Returneaza datele de contact ale firmei
		 * @return				Datele de contact ale firmei
		 *
		 */
		 public function getDateContact()
		{
			return $this->dateContact;
		}



		/**
		 *
		 * Returneaza comentariile despre firma
		 * @return				Comentariile despre firma
		 *
		 */
		 public function getComentarii()
		{
			return $this->comentarii;
		}

		/**
		 *
		 * Aduce procentul firmei pentru o anumita data
		 * @param DataCalendaristica $data			Procentul pentru o anumita data
		 * @return									Procentul pentru o anumita data
		 *
		 */
		 public function getProcentFirma(DataCalendaristica $data)
		{
			$valoare		= 0;
			$exista			= false;


			$A2 = "SELECT valoare from procent WHERE idFirma='".$this->id."' AND  (( isNow='0' AND '".$data->getFirstDayOfMonth()."'>=_from AND  '".$data->getLastDayOfMonth()."<=_to ') OR ( isNow='1' AND '".$data->getFirstDayOfMonth()."'>=_from))  LIMIT 1";
			$result = mysql_query($A2, Aplicatie::getInstance()->getMYSQL()) or die(mysql_error());
			if(mysql_num_rows($result)==0)
			{
				echo'<br /><span style="color:red">Eroare: !!!! Firma nu are un procent stabilit !</span><br />';
				die();
			}
			while($p = mysql_fetch_array($result))
			{
				$valoare		= $p['valoare'];
				$exista			= true;
			}

			if(!$exista)
				throw new Exception("Nu exista procent impus pentru ".$this.' la data '.$data);

			return $valoare;
		}
	}
