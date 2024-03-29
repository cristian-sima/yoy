<?php

require_once "Firma.php";
require_once "SituatieGrafica.php";


/**
*
* Reprezinta o situatie vizuala
*
* @author			Cristian Sima
* @data			12.02.2014
* @version			1.2
*
*/
class Situatie_GUI
{
	// referinte
	private $situatie;
	private $firma;

	private $html = '';

	// grafic settings
	private $displayAutor 			= true;
	private $isInteractive			= true;
	private $isPaper			= false;

	// content
	private $nrTotalRows 			= 15;
	private $completedRows			= 0;
	private $perioada	 			= "";
	private $textTitle				= "";


	/**
	* Afișează o situatie grafica pentru o firma intr-o anumita perioada. Aceasta situatie este compusa din situatia index-urilor
	*
	* @param SituatieGrafica $situatie				Situatia grafica pentru firma respectiva
	* @param Firma $firma							Referinta spre obiectul firmei de spatiu
	*/
	public function __construct(SituatieGrafica $situatie, Firma $firma)
	{
		$this->situatie					= $situatie;
		$this->firma					= $firma;

		if($situatie->getFrom() == $situatie->getTo())
		{
			$this->perioada				= "<span class='bold'>".$situatie->getFrom()->romanianFormat().'</span>';
			$this->textTitle			= "ZILNICE";
		}
		else
		{

			$this->perioada				= "De la <span class='bold'>".$situatie->getFrom()->romanianFormat()."</span> la <span class='bold'>".$situatie->getTo()->romanianFormat().'</span>';
			$this->textTitle			= "";
		}

		$this->completedRows			= $this->getSituație()->getNumarulDeAparate();
	}



	/**
	*
	* 	Seteaza daca situatie este interactiva sau nu
	* @param $boolean			True daca situatia este interactiva false daca nu
	*
	*/
	public function isInteractiva($boolean)
	{
		$this->isInteractive = $boolean;
	}


	public function isPaper()
	{
		$this->isPaper = true;
	}

	/**
	*
	* Seteaza daca autorul se va afișa sau nu
	* @param $boolean			True daca autorul se afiseaza false daca nu
	*
	*/
	public function displayAutor($boolean)
	{
		$this->displayAutor = $boolean;
	}


	/**
	*
	* Returneaza titlul
	*
	*/
	public function getTextTitle()
	{
		return $this->textTitle;
	}


	/**
	*
	* Seteaza numarul total de randuri
	* @return					Numarul total de randuri
	*
	*/
	public function setTotalRows($new)
	{
		$this->nrTotalRows = $new;
	}

	/**
	*
	* Returneaza referinta spre firma
	* @return					Firma
	*
	*/
	public function getFirma()
	{
		return $this->firma;
	}

	/**
	*
	* Returneaza referinta spre situatie
	* @return					Situatia
	*
	*/
	public function getPerioada()
	{
		return $this->perioada;
	}


	/**
	*
	*  Returneaza referinta spre situatie
	* @return SituatieMecanicaGraficaCompleta		Situatia
	*
	*/
	public function getSituație()
	{
		return $this->situatie;
	}



	/**
	*
	* Afișează intreaga situatie in format PDF
	*
	*/
	public function display()
	{
		echo $this->getHTML();
	}

	public function getHTML(){
		// HEADER
		$this->_getHTML();
		return $this->html;
	}


	private function _getHTML(){
		$this->displayStart();
		$this->displayInformation();
		$this->displayTitle();


		// CONTENT
		$this->displayHeaderData();
		$this->displayData();



		// FOOTER
		$this->displayHeader();


		$this->displayEnd();
	}

	/**
	*
	* Afișează inceputul situatiei și deschide formularul
	*
	*/
	private function displayStart()
	{

		$this->html.= '

		<script type="text/javascript" src="public/js/situatie_GUI.js" ></script>

		<div class="situatie_div privateDesign_prt">
		<form id="formular_situatie" action="modifica_situatie.php" method="POST">

		<input type="hidden" name="aparate_" value="" id="aparate_" />
		<input type="hidden" value="'.$this->getFirma()->getID().'" 		name="id_firma" />
		<input type="hidden" value="'.$this->getSituație()->getFrom().'" 	name="from" />


		<link href="public/css/situatie_GUI_'.(($this->isPaper) ? 'paper' : 'web') .'.css" rel="stylesheet" type="text/css"/>
		';
	}


	/**
	*
	* Afișează informatii sumative despre situatie. Acestea include data, organizatorul, etc
	*
	*/
	private function displayInformation()
	{

		$this->html.= '
		<table class="situatie_info">
		<colgroup>
		<col class="situatie_info_first_column"></col>
		<col class="yellow white_prt" ></col>
		</colgroup>
		<tr>
		<td >Organizator</td>
		<td class="haft" style="font-weight:bold">'.Aplicatie::getInstance()->getFirmaOrganizatoare()->getDenumire().'</td>
		</tr>
		<tr>
		<td >Deținător spațiu</td>
		<td class="haft">'.$this->getFirma()->getDenumire().'</td>
		</tr>
		<tr>
		<td >Locație sală</td>
		<td >'.$this->getFirma()->getLocatie().'</td>
		</tr>
		<tr>
		<td >Perioada</td>
		<td>'.$this->getPerioada().'</td>
		</tr>
		</table>';
	}

	/**
	*
	* Afișează titlul și subtitlul pentru situatie
	*
	*/
	private function displayTitle()
	{
		$this->html.= '
		<div class="situatie_titlu">SITUAȚIA ÎNCASĂRILOR '.$this->getTextTitle().'</div>

		<div class="situatie_subTitlu">pentru activitatea de exploatare a mașinilor
		electronice cu câștiguri (Lei)</div>';
	}

	/**
	*
	* Afișează headerul pentru tabel. Acesta include capetele de tabel.
	*
	*/
	private function displayHeaderData()
	{
		$this->html.= '
		<table class="situatie_data" id="situatie_data">
		<colgroup>
		<col class="situatie_data_col_1" ></col>
		<col class="situatie_data_col_2" ></col>
		<col class="situatie_data_col_3" ></col>
		<col class="situatie_data_col_4" ></col>
		<col class="situatie_data_col_5" ></col>
		<col class="situatie_data_col_6" ></col>
		<col class="situatie_data_col_7" ></col>
		<col class="situatie_data_col_8" ></col>
		<col class="situatie_data_col_9" ></col>
		<col class="situatie_data_col_10" ></col>
		<col class="situatie_data_col_11" ></col>
		<col class="situatie_data_col_12" ></col>
		<col class="situatie_data_col_13" ></col>
		<col class="situatie_data_col_14" ></col>
		<col class="situatie_data_col_15" ></col>
		<col class="situatie_data_col_16" ></col>
		<col class="situatie_data_col_17" ></col>
		</colgroup>
		<thead>
		<tr>
		<th> Nr. <br/> Crt.</th>
		<th> Seria <br />mașinii</th>
		<th colspan="3">Indexul contoarelor la<br/>început (Si)</th>
		<th colspan="3">Indexul contoarelor la<br/>sfârșit (Sf)</th>
		<th colspan="3">Factor de<br/>multiplicare (F)</th>
		<th colspan="3">Diferență indexuri contoare<br />(D) = (Sf-Si)xF</th>
		<th> Sold<br/>impulsuri</th>
		<th> Preț/<br/>impuls</th>
		<th> Încasări</th>
		</tr>
		<tr>
		<th></th>
		<th></th>
		<th>I</th>
		<th>Ej</th>
		<th>Ej</th>
		<th>I</th>
		<th>Ej</th>
		<th>Ej</th>
		<th>I</th>
		<th>Ej</th>
		<th>Ei</th>
		<th>I</th>
		<th>Ej</th>
		<th>Ei</th>
		<th>=11-12-13</th>
		<th>lei</th>
		<th>lei</th>
		</tr>
		</thead>
		';
	}



	/**
	*
	* Afișează datele despre aparate in tabelul pentru situatie
	*
	*/
	private function displayData()
	{
		$row = 1;

		$this->html.= '<tbody>';

		$this->displayRow("numbers", array("",1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,"16 =14 x 15"));

		$aparate = $this->situatie->getAparate();


		foreach($aparate as $aparat)
		{


			$this->displayRow($row, array(
				$row,
				$aparat['data']->getSerie(),
				$aparat['situatie']['start_intrari'],
				"<input type='hidden' value='".$aparat['situatie']['start_intrari']."' name='aparat_".$aparat['data']->getID()."_start_intrari' />",
				$aparat['situatie']['start_iesiri'],
				(($this->isInteractive)?("<input name='aparat_".$aparat['data']->getID()."_end_intrari' type='text' class='completare_index transparent_prt' value='".$aparat['situatie']['end_intrari']."' />"):($aparat['situatie']['end_intrari'])),

				"<input type='hidden' value='".$aparat['situatie']['start_iesiri']."' name='aparat_".$aparat['data']->getID()."_start_iesiri' />",
				(($this->isInteractive)?("<input name='aparat_".$aparat['data']->getID()."_end_iesiri' type='text' class='completare_index transparent_prt' value='".$aparat['situatie']['end_iesiri']."' />"):($aparat['situatie']['end_iesiri'])),
				$aparat['data']->getFactorMecanic(),

				"<input type='hidden' value='".$aparat['data']->getID()."' name='aparat_".$aparat['data']->getID()."_id' />",
				$aparat['data']->getFactorMecanic(),
				$aparat['situatie']['diferenta_1'],
				"",
				$aparat['situatie']['diferenta_2'],
				$aparat['situatie']['diferenta_2'] - $aparat['situatie']['diferenta_1'],
				$aparat['data']->getPretImpuls(),
				($aparat['situatie']['diferenta_1'] - $aparat['situatie']['diferenta_2'])*$aparat['data']->getPretImpuls().' lei'
			));

			$row++;
		}

		$this->displayEmptyRows();
		$this->displayTotalRow();

		$this->html.= '</tbody>';
		$this->html.= '</table>';
	}


	/**
	*
	* Completeaza situatia cu randuri goale
	*
	*/
	private function displayEmptyRows()
	{
		for($row = $this->completedRows+1; $row <= $this->nrTotalRows; $row++)
		{
			$this->displayRow("empty", array($row,"","","","","","","","","","","","","","","",""));
		}
	}


	/**
	*
	* Completeaza situatia cu randuri goale
	*
	*/
	private function displayTotalRow()
	{
		$t = $this->getSituație()->getTotal();
		$this->displayRow("total", array("","<span class='bold'>TOTAL</span>","","","","","","","","","","","","","","",'<span id="total_bani">'.$t['sertar'].'</span> lei'));

	}


	/**
	*
	* Afișează un rand in situatie. Acest rand este compus din 17 campuri
	*
	* @param int $id					ID-ul randului
	* @param array $columns			O matrice cu 17 campuri pentru toate coloanele
	*
	*/
	private function displayRow($id, $columns)
	{
		$this->html.= '<tr id="situatie_data_row_'.$id.'">';
		for($i = 0; $i <= 16; $i++)
		{
			$this->html.= '<td>'.$columns[$i].'</td>'	;
		}
		$this->html.= '</tr>';
	}


	/**
	*
	* Afișează header-ul. Acesta include persoana care a completat situatia, situatia de bilite și toate totalurile
	*
	*/
	private function displayHeader()
	{
		$this->html.= '<table class="situatie_footer" style="width:100%">';
		$this->html.= '<tr>';
		$this->html.= '<td style="width:33%" >';
		$this->_displayAutor();
		$this->html.= '</td>';

		$this->html.= '<td style="text-align:center;width:33%">';
		$this->html.= '</td>';


		$this->html.= '<td style="width:33%;text-align:right">';
		$this->_displayTotaluri();
		$this->html.= '</td>';

		$this->html.= '</tr></table>';
	}


	/**
	*
	* Afișează persoana care a completat situatia
	*
	*/
	private function _displayAutor()
	{
		if($this->displayAutor && ($this->getSituație()->getAutor() != null))
		{
			$this->html.= '<div class="situatie_autor">
			Întocmit, <br />
			<span class="situatie_nume_autor">&nbsp;&nbsp;&nbsp;';
			$this->html.= htmlspecialchars($this->getSituație()->getAutor()->getNume());
			$this->html.= '&nbsp;&nbsp;&nbsp;</span></div>';
		}
	}

	/**
	*
	* Afișează tabelul cu totaluri. Acesta include totalul de incasari, de plati și ceea ce ramane in sertar
	*
	*/
	private function _displayTotaluri()
	{
		$t = $this->getSituație()->getTotal();

		$this->html.= '	<table class="situatie_totaluri">
		<colgroup>
		<col class="yellow white_prt" ></col>
		<col class="bold" ></col>
		</colgroup>
		<tr>
		<td>Total încasări</td>
		<td><span id="incasari">'.$t['incasari'].'</span> lei</td>
		</tr>
		<tr>
		<td>Total în sertar</td>
		<td><span id="sertar">'.$t['sertar'].'</span> lei</td>
		</tr>
		</table>';
	}


	/**
	*
	* 	Afișează inchiderea formularului și a situatiei (div)
	*
	*/
	private function displayEnd()
	{
		$this->html.= '<a id="jump_salveaza" class="hidden-print" href="" style="color:white">Salvează</a></form>';
		$this->html.= '</div>';
	}
}
