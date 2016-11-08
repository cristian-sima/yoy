<?php

	require_once "include/php/Registru.php";
	
/**
 *
 * Reprezinta un continut de situatie tip registru. Aceasta situatie are totaluri, posibilitatea de adauga campuri
 * @author			Cristian Sima
 * @data			16.02.2014
 * @version			1.4
 *
 */
abstract class RegistruGrafic extends Registru
{	
	private $rows					= null;
	private $title					= "";
	private $columns 				= null;
	private $totaluri				= null;
	private $primulRand				= "";
	private $indexNewRow			= 1;	
	private $columnsWeights			= null;
	private $total_sum_columns		= array();
	
	protected $totalTitleColumn		= 0;
	protected $sumeRaportate		= null;
	protected $sum_columns			= array();

	/**
	 * Apeleaza contructorul parinte și initializeaz campurile. Apeleaza metoda care proceseaza și adauga date in situatie
	 * @param DataCalendaristica		Data registrului
	 */
	public function RegistruGrafic(DataCalendaristica $data)
	{				
		$this->rows				= array();
		$this->columns			= array();
		$this->totaluri			= array();	
		
		parent::__construct($data);
	}
	
	/**
	 * Actualizeaza suma totală pentru o coloana
	 *
	 * @param int $coloana			ID-ul coloanei pentru care se va actualiza suma
	 * @param int $suma				Suma care se va adauga la cea existenta
	 */
	public function actualizeazaSumaTotalaColoana($coloana, $suma)
	{
		$this->total_sum_columns[$coloana] += $suma;
	}
	
	/**
	 * Returneaza totalul pentru o coloana
	 *
	 * @param int $coloana			ID-ul coloanei pentru care se doreste totalul
	 * @return 						Suma totală pentru o coloana pana in momentul de fata
	 */
	public function getSumaTotalaColoana($coloana)
	{
		return $this->total_sum_columns[$coloana];
	}	
	
	/**
	 * Seteaza  coloane care se vor totaliza pe fiecare pagina
	 *	
	 * @param array $col			O matrice cu toate sumele pentru fiecare coloana. Trebuie sa aiba aceas numar de elemente ca și numarul de coloane
	 */
	public function setColoaneTotalizate(array $col)
	{
		$this->sum_columns 		= $col;
		
		foreach ($col as $coloana) {
			$this->total_sum_columns[$coloana] = 0;
		}
	}
	
	/**
	 * Seteaza sumele pentru coloanele totalizate
	 *	
	 * @param array $new			O matrice cu toate sumele pentru fiecare coloana. Trebuie sa aiba aceas numar de elemente ca și numarul de coloane
	 */
	public function setSumeColoaneTotalizate(array $new)
	{
	 	$this->total_sum_columns = $new;
	}
	
	/**
	 * Returneaza ID-ul coloanei unde se vor afișa "SOLD RAPORTAT" și "TOTAL PAGINĂ"
	 * @return 			ID-ul coloanei unde se vor afișa "SOLD RAPORTAT" și "TOTAL PAGINĂ"		
	 */
	public function getTotalTitleColumn()
	{
		return $this->totalTitleColumn;
	}	

	/**
	 * Returneaza o matrice cu toate sumele totale ale coloanelor.
	 * @return 			O matrice cu sumele totale ale coloanelor
	 */
	public function getTotaluriColoane()
	{
		return $this->sum_columns;
	}	
	
	/**
	 * Returneaza index-ul pentru noul rand adaugat
	 * @return 			Index-ul pentru noul rand adaugat
	 */
	protected function getIndexNewRow()
	{
		return $this->indexNewRow;
	}	
	
	/**
	 * Incrementeaza index-ul pentru noul rand
	 */
	protected function incrementIndexNewRow()
	{
		$this->indexNewRow++;
	}	

	/**
	 * Returneaza numarul de randuri ale tabelului
	 * 
	 * @return int				Numarul de randuri ale tabelului
	 */
	public function getNumarulDeRanduri()
	{
		return count($this->rows);
	}

	/**
	 * Seteaza ID-ul coloanei unde se vor afișa "SOLD RAPORTAT" și "TOTAL PAGINĂ"
	 *
	 * @param int $value			ID-ul coloanei unde se vor afișa "SOLD RAPORTAT" și "TOTAL PAGINĂ"
	 */
	protected function setTotalTitleColumn($value)
	{
		$this->totalTitleColumn = $value;
	}	

	/**
	 *
	 *	Returneaza numarul de coloane ale tabelului
	 *
	 * @return				Numarul de coloane ale situatiei
	 */
	public function getNumarulDeColoane()
	{
		return count($this->columns);
	}	

	/**
	 * Schimba toate coloanele deja existente cu cele primite
	 *
	 * @param array $colums		Matricea cu noile coloane
	 */
	public function setColumns(array $columns)
	{
		$this->columns = $columns;	
	}	
	
	/**
	 * Adauga o noua colana in situatie
	 *
	 * @param array $column			Coloana care se va adauga
	 * @throws Exception			Daca coloana care trebuie adaugata nu este de tip coloana
	 */
	protected function addColumn(array $column)
	{
		array_push($this->columns, $column);		
	}
			
	/**
	 * Adauga un nou rand in situatie. Numarul de elemente ale randului trebuie sa fie egal cu numarul de coloane
	 *
	 * @param array $new_row			Matricea care contine toate campurile pentru noul rand
	 * @throws Exception				Atunci cand noul rand nu este de tip array	
	 * 
	 */
	public function addRow(array $new_row)
	{		
		array_push($this->rows, $new_row);		
		$this->incrementIndexNewRow();		
	}	
	
	/**
	 * 
	 * Returneaza un rand in functie de id-ul lui
	 *
	 * @param int $id			ID-ul randurilui care se doreste returnat.
	 * @return array			Un array care este realizat din n coloane.
	 * 
	 */
	public function getRow($id)
	{
		$row = $this->rows[$id];
		return $row;
	}	
	
	/**
	 * 
	 * Returneaza o coloana in functie de id-ul ei
	 *
	 * @param int $id			ID-ul coloanei care se doreste returnat.
	 * @return 					O matrice care contine informatii despre coloana
	 * 
	 */
	public function getColoana($id)
	{
		return $this->rows[$id];
	}
		
	/**
	 * Returneaza coloanele situatiei
	 * @return 					Returneaza toate coloanele
	 */
	public function getColoane()
	{
		return $this->columns;
	}
	
	/**
	 * 
	 * Obtine primul rand/prima coloana pentru header
	 * 
	 */
	public function getPrimulRand()
	{
		return $this->primulRand;
	}	
	
	/**
	 * 
	 * Seteaza primul rand pentru header
	 *
	 * @param String $primulRand			Propozitia afisata in primul rand prima coloana de la header
	 */
	public function setPrimulRand($primulRand)
	{
		$this->primulRand = $primulRand;
	}	
	
	/**
	 * 
	 * Seteaza titlul-ul tabelului
	 *
	 * @param String $title			Titlul tabelului
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}	
	
	/**
	 * Obtine titlul tabelului
	 * @return	 				Titlul tabelului
	 */
	public function getTitle()
	{
		return $this->title;
	}	
	
	
	
	/**
	 * Returneaza un string care reprezinta data situatie
	 * 
	 * @return					Un string care descrie data situatiei (eg. Ianuarie 2013)
	 * 
	 */
	public function getDateTitle()
	{
		return DataCalendaristica::getNumeleLunii(parent::getFrom()->getLuna()).' '.parent::getFrom()->getAnul();
	}	
	

	/** 
	 * Adauga un nou total in matricea de totaluri a situatiei
	 * @param Total $total				Referinta la obiectul @see Total care va fi adaugat
	 */
	protected function addTotal(Total $total)
	{
		array_push($this->totaluri, $total);			
	}		
	
	/** 
	 * Returneaza o matrice cu toate totalurile și sumele pentru acestea 
	 * @return				Matricea care contine totalurile situatiei 
	 */
	public function getTotaluri()
	{
		return $this->totaluri;	
	}	
}