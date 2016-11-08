
<?php

require_once "Aparat.php";
require_once "Situație.php";
require_once "Utilizator.php";


/**
 *
 * Reprezinta o situatie grafica. Are metode pentru adaugat aparate, are autor. De asemenea contine lista de aparate
 * @author			Cristian Sima
 * @data			28.01.2014
 * @version			1.0
 *
 */
abstract class SituațieGrafica extends Situație
{
	protected 	$autor			=	null;
	protected 	$aparate		= 	array();
	protected 	$type			=   "";

	/**
	 *
	 * 						Realizeaza o noua situatie, si initializeaza toate variabilele
	 * @param DataCalendaristica $from			Data de inceput a situatiei [@DataCalendaristica]
	 * @param DataCalendaristica $to			Data de sfarsit a situatiei [@DataCalendaristica]
	 * @param Firma $firma						Referinta spre obiectul firma despre care se face situatia [@Firma]
	 * @param String $type						Tipul situatie - electronica sau mecanica
	 *
	 */
	protected function __construct($from, $to, $firma, $type)
	{
		parent::__construct($from, $to, $firma);

		$this->type					= $type;
	}

	/**
	 *
	 * Calculeaza numarul de aparate
	 * @return					Numarul de aparate
	 *
	 */
	public function getType()
	{
		return ($this->type);
	}

	/**
	 *
	 * Calculeaza numarul de aparate
	 * @return					Numarul de aparate
	 *
	 */
	public function getNumarulDeAparate()
	{
		return count($this->aparate);
	}

	/**
	 *
	 * Returneaza referinta spre array-ul cu aparate
	 * @return					Referinta spre array-ul cu aparate
	 *
	 */
	public function getAparate()
	{
		return $this->aparate;
	}

	/**
	 *
	 * Returneaza utilizatorul care a realizat situatia
	 * @return Utilizator		Referinta spre obiectul utlizatator care a realizat situatia
	 *
	 */
	public function getAutor()
	{
		return $this->autor;
	}

	/**
	 *
	 * Adauga un aparat in situatie. Daca aparatul exista, ii actualizeaza setarile si situatia
	 *
	 * @param Aparat $object			Referinta la obiectul Aparat
	 * @param array $situatie			Un array cu toate index-urile aparatului
	 *
	 */
	protected function addAparat($object, $situatie)
	{
		$exist 	= false;
	
		foreach ($this->aparate as &$aparat)
		{		
			if($aparat['data']->getID() == $object->getID())
			{
				$aparat['situatie'] = $situatie;
				$exist = true;
				return;
			}
		}
		
		if(!$exist)
		{
			array_push($this->aparate, array(	 "data"		=> $object,
												 "situatie" => $situatie));
		}	
	}
}