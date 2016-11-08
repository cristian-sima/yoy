<?php

require_once "MYSQL.php";
require_once "Login.php";
require_once "FirmaOrganizatoare.php";

/**
 *
 * Reprezinta obiectul afiseaza un chooser de situatie. Afișează lunile, anii și toate firme (inactive și active)
 * @author			Cristian Sima
 * @data			21.02.2014
 * @version			1.5
 *
 */
class SelectSituatie_GUI
{
	private $data							= null;
	private $type_							= "Situație";
	private $id_firma						= "";
	private $adresaButon					= "";
	private $select							= null;

	private $afiseazaAni 					= true;
	private $afiseazaLuni					= true;
	private $afiseazaButon					= true;
	private $afiseazaFirme					= true;
	private $afiseazaDescriere				= true;
	private $afiseazaToateFirmele			= true;
	private $afiseazaDoarFirmeActive		= false;
	private $afiseazaDoarFirmeInactive		= false;


	/**
	 * Constructorul realizeaza o noua data și salveza id-ul firmei
	 *
	 * @param string $data					Data calendaristica pentru situatie sau "" pentru data curenta
	 * @param string $id_firma				ID-ul firmei de spatiu
	 */
	public function SelectSituatie_GUI($data, $id_firma)
	{
		if($data == "")
		$data = date("Y-m-d");

		try
		{
			$this->data 		= new DataCalendaristica($data);
		}
		catch(Exception $e)
		{
			Page::complain($e->getMessage());
		}

		$this->id_firma		= $id_firma;
		$this->select		= array();
	}


	/**
	 * Adauga un nou camp select. De exemplu
	 * @exemple 	array(	"denumire" 	=> "Salveaza",
	 * 			"id"		=> "afiseaza_box"
	 * 			"optiuni" 	=> array(
	 * 			 	array("denumire" => "DA", "valoare" => 1),
	 * 				array("denumire" => "NU", "valoare" => 0)
	 * 			)
	 * 		 )
	 *
	 * @param array $select			O matrice care contine câmpul select
	 */
	public function adaugaCampSelect($select)
	{
		Procesare::createEmptyFields($_GET, array($select['id']));
	
		array_push($this->select, $select);
	}

	/**
	 *
	 * Afișează un button care duce la link-ul specificat ca parametru
	 *
	 * @param String $link			Unde se va duce procesarea selectorului
	 */
	public function afiseazaButon($value)
	{
		$this->afiseazaButon = $value;
	}


	/**
	 * Seteaza adresa butonului
	 *
	 * @param String $adresa		Pagina unde se va duce dupa apasarea butonului
	 *
	 */
	public function setAdresaButon($adresa)
	{
		$this->adresaButon = $adresa;
	}


	/**
	 *
	 * Specifica daca se afiseaza sau nu descrierea
	 *
	 * @param boolean $valoare		True daca se afiseaza, false in caz contrar
	 */
	public function afiseazaDescriere($valoare)
	{
		$this->afiseazaDescriere = $valoare;
	}


	/**
	 * Seteaza tipul titlului.
	 *
	 * @param string $type_			Tipul documentului care apare in titlu
	 */
	public function setTypeOfDocument($type_)
	{
		$this->type_	 = $type_;
	}

	/**
	 *
	 * Returneaza data curenta a paginii
	 * @return DataCalendaristica			Data calendaristica
	 */
	public function getDataCurenta()
	{
		return $this->data;
	}


	/**
	 *
	 * Specifica daca se afiseaza sau nu optiunea de toate firmele
	 *
	 * @param String $link			Unde se va duce procesarea selectorului
	 */
	public function afiseazaToateFirmele($value)
	{
		$this->afiseazaToateFirmele = $value;
	}

	/**
	 *
	 * Seteaza daca se afiseaza sau nu anii
	 *
	 * @param boolean $value			True daca se afiseaza anii, false daca nu
	 *
	 */
	public function afiseazaAni($value)
	{
		$this->afiseazaAni		= $value;
	}


	/**
	 *
	 * Seteaza daca se afiseaza sau nu luniile
	 *
	 * @param boolean $value		True daca se afiseaza luniile, false daca nu
	 *
	 */
	public function afiseazaLuni($value)
	{
		$this->afiseazaLuni		= $value;
	}


	/**
	 * Optine valoare default pentru un selector adaugat
	 *
	 * @param string $opt			ID-ul selectorului
	 * @return						Valoarea selectorului sau false daca nu este nimic
	 */
	public function getValoareOptiune($opt)
	{
		return ((''==$_GET[$opt])?false:$_GET[$opt]);
	}

	/**
	 *
	 * Seteaza daca se afiseaza sau nu firmele
	 *
	 * @param boolean $value		True daca se afiseaza firmele (indiferent daca sunt active sau inactive), false daca nu
	 *
	 */
	public function afiseazaFirme($value)
	{
		$this->afiseazaFirme	= $value;
		
		if($value == false)
		{			
			$this->id_firma = "";	
		}
	}



	/**
	 *
	 * Seteaza daca se afiseaza sau nu doar firmele active
	 *
	 * @param boolean $value		True daca se afiseaza doar firmele active
	 *
	 */
	public function afiseazaDoarFirmeActive($value)
	{
		$this->afiseazaDoarFirmeActive	= $value;
	}

	/**
	 *
	 * Seteaza daca se afiseaza sau nu doar firmele inactive
	 *
	 * @param boolean $value		True daca se afiseaza doar firmele inactive
	 *
	 */
	public function afiseazaDoarFirmeInactive($value)
	{
		$this->afiseazaDoarFirmeInactive	= $value;
	}

	/**
	 *
	 * Afișează chooser-ul de situatie
	 *
	 */
	public function display()
	{
		try {
			
		
		echo '
		<script>
		
			function goTo(where)
			{			
			

				$("#select_options").append('."'".'<input type="hidden" value="'."'".'+$("#an").val()+'."'".'-'."'".'+$("#luna").val()+'."'".'-01" name="data" />'."'".');
							
							
			var datastring = $("#select_options").serialize();
			
				document.location = where+"?"+datastring;
			}	
		</script>';

		echo'<div id="control_panel" class="hide_prt"><form id="select_options">';


		if($this->afiseazaAni)
		{
			echo 'Anul <select id="an" name="an">';
			for( $an=2013; $an <= 2020; $an++)
			{
				echo'<option value="'.$an.'" '.(($an == $this->data->getAnul())?("selected"):"").'>'.$an."</option>";
			}
			echo"</select>";
		}

		if($this->afiseazaLuni)
		{
			echo'&nbsp;&nbsp;&nbsp;&nbsp;Luna <select name="luna" id="luna">';

			for( $luna=1; $luna <= 12; $luna++)
			{
				echo'<option value="'.$luna.'" '.(($luna == $this->data->getLuna())?("selected"):"").' >'.DataCalendaristica::getNumeleLunii($luna)."</option>";
			}
			echo"</select>";
		}

		

		if($this->afiseazaFirme)
		{
			echo'&nbsp;&nbsp;&nbsp;&nbsp;Firma
			<select name="id_firma" id="firma">
			';

			if($this->afiseazaToateFirmele)
			{
				echo '<option value="" style="background:white">Toate firmele</option>';
			}

			$preferinte = '';

			if($this->afiseazaDoarFirmeActive)
			{
				$preferinte = "WHERE activa = '1'";
			}
			else
			if($this->afiseazaDoarFirmeInactive)
			{
				$preferinte = "WHERE activa = '0'";
			}

			$result = mysql_query("SELECT nume,id,activa
								FROM firma 
								".$preferinte."
								ORDER BY activa DESC,nume ASC", 
			Aplicatie::getInstance()->getMYSQL()->getResource());

			while($firma = mysql_fetch_array($result))
			{
				echo'<option value="'.$firma['id'].'" '.(($firma['id'] == $this->id_firma)?('selected'):"").'  '.(($firma['activa']=="0")?('style= "background:#FF5050" '):"").' >'.($firma['nume'])."</option>";
			}
			echo"</select>";
		}
		
		foreach ($this->select as $select)
		{
			
			echo '&nbsp;&nbsp;'.$select['denumire'].' <select name="'.$select['id'].'">';
				
			foreach ($select['optiuni'] as $optiune)
			{
				echo '<option '.(($_GET[$select['id']] == $optiune['valoare'])?("selected"):"").' value="'.$optiune['valoare'].'" >'.$optiune['denumire'].'</option>';
			}
				
			echo '</select>';
		}
		
		
		if($this->afiseazaButon)
		{
			echo '&nbsp;&nbsp;&nbsp;&nbsp;<input value="Vizualizați" type="button" onclick="goTo('."'".$this->adresaButon.''."'".')" />';
		}

		echo '</form></div>';

		if($this->afiseazaDescriere)
		{
			if($this->id_firma != '')
			{
				$firma_		= new FirmaSpatiu($_GET['id_firma']);
			}
			echo '<div style="text-align:center">';

			echo '<big>'.$this->type_.' din <b>'.$this->getDataCurenta()->getFirstDayOfMonth().'</b> la <b>'.$this->getDataCurenta()->getLastDayOfMonth().'</b> '.(($this->afiseazaFirme)?("pentru <b>".(($this->id_firma != '')?($firma_->getDenumire()):("toate firmele").'</b>')):("")).'</big><br /><br />';

			echo '</div>';
		}
	
		}
		catch(Exception $e)
		{
			Page::complain($e->getMessage());
			die();
		}
	}
}