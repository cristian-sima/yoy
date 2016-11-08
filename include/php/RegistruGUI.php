<?php

require_once "include/php/Romanian.php";
require_once "include/php/RegistruGrafic.php";
require_once "include/php/DataCalendaristica.php";

/**
 *
 * Se ocupa cu afisarea unei situatii de tip registru pe ecran. Are un tabelul este structurat pe pagini. La final se afiseaza totalurile
 * @author			Cristian Sima
 * @data			16.02.2014
 * @version			1.3
 *
 */
class RegistruGUI
{
	// referinta spre obiectul care contine toate datele despre tabel
	private $content						= null;


	// setari GUI
	private $numarulDeRanduriPePag			= 20;
	private $numarulTotalDeRanduri			= 0;

	// optiuni de afisare
	private $afiseazaSoldInceput			= true;
	private $afiseazaSoldFinal				= true;
	private $afiseazaTotalPagina			= true;
	private $afiseazaTotalurile				= true;
	private $afiseazaSemnaturi				= true;


	/**
	 *
	 * Realizeaza un nou obiect Table_GUI. Verifica daca argumentul este de tip Tabel_API, apoi salveaza referinta, calculeaza numarul de randuri
	 *
	 * @param RegistruGrafic $data			Referinta spre obiectul cu toate datele
	 *
	 */
	public function RegistruGUI(RegistruGrafic $content)
	{
		$this->content		 			= $content;
		$this->numarulTotalDeRanduri	= $content->getNumarulDeRanduri();
	}

	/*
	 * ---------------------- SETTERI ------------------------------
	 */

	/**
	 * Se seteaza daca se afiseaza sau nu soldul raportat de la inceputul paginii
	 *
	 * @param boolean $value			True daca se afiseaza sold-ul la inceputul paginii, false in caz contrar
	 */
	public function afiseazaSoldInceput($value)
	{
		$this->afiseazaSoldInceput = $value;
	}


	/**
	 * Se seteaza daca se afiseaza sau nu soldul raportat de la finalul paginii
	 *
	 * @param boolean $value			True daca se afiseaza sold-ul la finalul paginii, false in caz contrar
	 */
	public function afiseazaSoldFinal($value)
	{
		$this->afiseazaSoldFinal = $value;
	}



	/**
	 * Se seteaza daca se afiseaza sau nu totalurile
	 *
	 * @param boolean $value			True daca se afiseaza casuta cu totaluri, false in caz contrar
	 */
	public function afiseazaTotalurile($value)
	{
		$this->afiseazaTotalurile = $value;
	}

	/**
	 * Seteaza daca se afiseaza sau nu semnaturile pentru casier și contabilitate pe fiecare pagina
	 *
	 * @param boolean $value			True daca se afiseaza, false in caz contrar
	 */
	public function afiseazaSemnaturi($value)
	{
		$this->afiseazaSemnaturi = $value;
	}

	/*
	 * ------------------------- INCEPE AFISAREA -----------------------
	 */

	/**
	 *
	 * Afișează intregul datele sub forma de tabel numerotand randuri. Afișează headerul documentului (data, titlul) și footerul. Afișează toate totalurile la final
	 *
	 */
	public function afiseaza()
	{
		$this->numarulDePagini = ceil($this->numarulTotalDeRanduri/$this->numarulDeRanduriPePag);

		if($this->numarulDePagini == 0 )
		{
			echo '<span style="color:red"> Se pare că nu există date pentru <span style="color:black">'.$this->content->getTitle().'</span><span style="color:red"> din </span><span style="color:black">'.$this->content->getDateTitle().'</span>';

		}
		else
		{
			$this->afiseazaInceput();

			for($pagina=1; $pagina<=$this->numarulDePagini; $pagina++)
			{
				$this->afiseazaPagina($pagina);
			}
			$this->afiseazaDIVTotaluri();
		}
	}

	/**
	 *
	 *  Afișează resursa CSS care este necesara pentru tabel
	 *
	 */
	private function afiseazaInceput()
	{
		echo '<link href="include/css/tabel_GUI.css" rel="stylesheet" type="text/css"/>';
	}

	/**
	 *
	 *  Afișează headerul situatiei. Acest header este compus din unitatea firmei care organizeaza, titlul tabelului și data.
	 *
	 */
	private function afiseazaHeader()
	{
		echo '<table class="tabel_header"><tr>';
		echo '<td class="table_header_first_row">';
		echo 'UNITATEA: '.Aplicatie::getInstance()->getFirmaOrganizatoare()->getDenumire().'<br />';
		echo $this->content->getPrimulRand();
		echo '</td><td class="table_header_title">';
		echo $this->content->getTitle();
		echo '</td><td class="table_header_date">';
		echo $this->content->getDateTitle();
		echo '</td></tr></table>';
	}

	/**
	 *
	 * Afișează un tabel cu toate totalurile și denumirea lor. Tabelul se afiseaza pe o pagina separata
	 *
	 */
	private function afiseazaDIVTotaluri()
	{
		if($this->afiseazaTotalurile)
		{
			echo '<div class="table_div privatePage_prt">
				 <table class="table_div_totaluri"><tr><th width="50%">Denumire total</th><th width="50%">Suma</th></tr>';
			foreach ($this->content->getTotaluri() as $total)
			{
				if($total->getTotal() != 0)
				{
					echo '<tr><td width="50%">'.$total->getNume().'</td><td width="50%">'.Romanian::currency($total->getTotal()).'</td></tr>';
				}
			}

			echo '</table></div>';
		}
	}


	/*
	 * ----------------------------- AFISARE PAGINA ----------------------------
	 */

	/**
	 *
	 * Afișează o noua pagina pentru tabel. O pagina are un footer, continutul și un header. Footerul este realizat din capetele de coloane, continutul este bazat pe continutul tabelului și headerul adreseaza subtotalul pentru incasari și iesiri dar și totalul acumulat pana in acel punct
	 *
	 * @param int $i					Numarul paginii
	 */
	private function afiseazaPagina($i)
	{
		echo '<div class="table_page_div privatePage_prt">';
		$this->afiseazaPaginarea($i);
		$this->afiseazaHeader();
		$this->afiseazaHeaderPagina();
		if($this->afiseazaSoldInceput)
		{
			$this->afiseazaSoldPrecendent();
		}

		$this->afiseazaRanduri($i);

		if($this->afiseazaSoldFinal)
		{
			$this->afiseazaSoldPrecendent();
		}
		$this->afiseazaTabelSemnaturi();
		$this->afiseazaFooterPagina();
		echo '</div>';
	}

	/**
	 * Afișează paginarea pentru o pagina. Acesta este compusa din nr paginii și numarul total de pagini
	 *
	 * @param int $pagina				Nr paginii
	 */
	private function afiseazaPaginarea($pagina)
	{
		echo '<table class="tabel_pagina_paginare" ><tr><td style="width:100%;text-align:right">Pagina <span class="bold">'.$pagina.'</span> din '.$this->numarulDePagini.'</td></tr></table>';
	}

	/**
	 *
	 * Afișează headerul unei pagini. Acesta este compus din captele de tabel ale tabelului și suma acumulata pana in momentul de fata pentru incasari și pierderi
	 *
	 */
	private function afiseazaHeaderPagina()
	{
		echo '<table class="table_page">';
		echo '<thead><tr>';
		foreach ($this->content->getColoane() as $coloana)
		{
			echo '<th style="width:'.$coloana['width'].'" >'.$coloana['content'].'</th>';
		}
		echo '</tr><thead>';
	}

	/**
	 * Afișează soldul precendent pentru o pagina
	 */
	private function afiseazaSoldPrecendent()
	{
		$row	= $this->getEmptyRow();
		$row[$this->content->getTotalTitleColumn()] = "<span class='bold'>SOLD RAPORTAT</span>";

		foreach ($this->content->getTotaluriColoane() as $coloana)
		{
			$row[$coloana] = "<span class='bold'>".Romanian::currency($this->content->getSumaTotalaColoana($coloana)).'</span>';
		}

		$this->afiseazaRand("sold_total", $row);
	}

	/**
	 * Afișează randurile paginii. Aceste randuri incepe de la $i*$numaruldePagini.
	 *
	 * @param int $i				Numarul paginii
	 */
	private function afiseazaRanduri($i)
	{
		$id_ultimul_rand 		= ( $i)*$this->numarulDeRanduriPePag;
		$nr_total_de_randuri	= $this->content->getNumarulDeRanduri();
		$total_pagina_incasari	= $total_pagina_plati = 0;
		$pagina_sum_colons		= array();

		foreach ($this->content->getTotaluriColoane() as $coloana)
		{
			$pagina_sum_colons[$coloana] = 0;
		}

		echo '<tbody>';

		if( $id_ultimul_rand < $nr_total_de_randuri)
		{
			$limit = $id_ultimul_rand;
		}
		else
		{
			$limit = $nr_total_de_randuri;
		}

		for($row = (($i-1)*$this->numarulDeRanduriPePag)+1; $row < $limit+1; $row++)
		{
			$this_row = $this->content->getRow($row-1);

			foreach ($this->content->getTotaluriColoane() as $coloana)
			{
				$pagina_sum_colons[$coloana] 	+= $this_row[$coloana];
				$this_row[$coloana]				= Romanian::currency($this_row[$coloana]);
			}


			$this->afiseazaRand("empty", $this_row);

		}

		/* --- Completeaza cu randuri libere ---- */

		if($nr_total_de_randuri < $id_ultimul_rand)
		{
			$randuri_libere = $id_ultimul_rand - $nr_total_de_randuri;

			$rand_gol = array();


			$rand_gol = $this->getEmptyRow();

			for($row = 1; $row<= $randuri_libere; $row++)
			{
				$this->afiseazaRand("empty", $rand_gol);
			}
		}
		echo '</tbody>';

	//	$this->content->getSumeRaportate()->actualizeazaIncasari($total_pagina_incasari);
	//	$this->content->getSumeRaportate()->actualizeazaPlati($total_pagina_plati);

		if($this->afiseazaTotalPagina)
		{
			$row	= $this->getEmptyRow();
			$row[$this->content->getTotalTitleColumn()] = "<span class='bold'>TOTAL PAGINĂ</span>";

			foreach ($this->content->getTotaluriColoane() as $coloana)
			{
				$row[$coloana] = "<span class='bold'>".Romanian::currency($pagina_sum_colons[$coloana]).'</span>';
				$this->content->actualizeazaSumaTotalaColoana($coloana,$pagina_sum_colons[$coloana]);
			}

			$this->afiseazaRand("total_pagina", $row);
		}
	}

	/**
	 * Returneaza un rand gol, care contine toate coloanele
	 * @return array 				Un rand gol
	 */
	private function getEmptyRow()
	{
		$rand_gol = array();
		$nr_cols = $this->content->getNumarulDeColoane();

		for ($coloana = 0; $coloana < $nr_cols; $coloana++)
		{
			array_push($rand_gol, "");
		}
		return $rand_gol;
	}

	/**
	 * Afișează un rand din tabel. Randul este afisat pentru toate coloanele
	 *
	 * @param array $row			O matrice care contine toate coloanele randului
	 */
	private function afiseazaRand($class, $row)
	{
		$nr_cols = $this->content->getNumarulDeColoane();

		echo '<tr class="'.$class.'">';
		for ($coloana = 0; $coloana < $nr_cols; $coloana++)
		{
			echo '<td>'.$row[$coloana].'</th>';
		}
		echo '<tr>';
	}

	/**
	 * Afișează locurile pentru semnaturile casierului și a contabilitatii
	 */
	private function afiseazaTabelSemnaturi()
	{
		if($this->afiseazaSemnaturi)
		{
			echo '<table class="table_semnaturi">';
			echo '<tr><td width="50%">Casier</td><td style="width:50%;">Compartiment financiar contabil</td></tr>';
			echo '<tr><td width="50%"><span class="completare">...........................</span></td><td style="width:50%;"><span class="completare">...........................</span></td></tr>';
			echo '</table>';
		}
	}

	/**
	 *
	 * Afișează foterul paginii. Acesta este compus din totalul pentru pagina curenta și totalul de pana acum
	 *
	 */
	private function afiseazaFooterPagina()
	{
		echo '</table>';
	}
}
