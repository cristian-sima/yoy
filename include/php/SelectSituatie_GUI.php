<?php

require_once "Login.php";
require_once "FirmaOrganizatoare.php";

class SelectSituatie_GUI {
	private $data = null;
	private $type_ = "Situație";
	private $id_firma = "";
	private $adresaButon = "";
	private $select = null;
	private $afiseazaAni = true;
	private $afiseazaLuni = true;
	private $afiseazaButon = true;
	private $afiseazaFirme = true;
	private $afiseazaDescriere = true;
	private $afiseazaToateFirmele = true;
	private $afiseazaDoarFirmeActive = false;
	private $afiseazaDoarFirmeInactive = false;
	public function SelectSituatie_GUI($data, $id_firma) {
		if ($data == "")
		$data = date("Y-m-d");
		try {
			$this->data = new DataCalendaristica($data);
		}
		catch (Exception $e) {
			Page::complain($e->getMessage());
		}
		$this->id_firma = $id_firma;
		$this->select   = array();
	}
	public function adaugaCampSelect($select) {
		Procesare::createEmptyFields($_GET, array(
			$select['id']
		));
		array_push($this->select, $select);
	}
	public function afiseazaButon($value) {
		$this->afiseazaButon = $value;
	}
	public function setAdresaButon($adresa) {
		$this->adresaButon = $adresa;
	}
	public function afiseazaDescriere($valoare) {
		$this->afiseazaDescriere = $valoare;
	}
	public function setTypeOfDocument($type_) {
		$this->type_ = $type_;
	}
	public function getDataCurenta() {
		return $this->data;
	}
	public function afiseazaToateFirmele($value) {
		$this->afiseazaToateFirmele = $value;
	}
	public function afiseazaAni($value) {
		$this->afiseazaAni = $value;
	}
	public function afiseazaLuni($value) {
		$this->afiseazaLuni = $value;
	}
	public function getValoareOptiune($opt) {
		return (('' == $_GET[$opt]) ? false : $_GET[$opt]);
	}
	public function afiseazaFirme($value) {
		$this->afiseazaFirme = $value;
		if ($value == false) {
			$this->id_firma = "";
		}
	}
	public function afiseazaDoarFirmeActive($value) {
		$this->afiseazaDoarFirmeActive = $value;
	}
	public function afiseazaDoarFirmeInactive($value) {
		$this->afiseazaDoarFirmeInactive = $value;
	}
	public function display() {
		try {
			echo '
			<script>

			function goTo(where)
			{


				$("#select_options").append(' . "'" . '<input type="hidden" value="' . "'" . '+$("#an").val()+' . "'" . '-' . "'" . '+$("#luna").val()+' . "'" . '-01" name="data" />' . "'" . ');


				var datastring = $("#select_options").serialize();

				document.location = where+"?"+datastring;
			}
			</script>';
			echo '<div id="control_panel" class="hide_prt"><form id="select_options">';
			if ($this->afiseazaAni) {
				echo 'Anul <select id="an" name="an">';
				for ($an = 2013; $an <= 2020; $an++) {
					echo '<option value="' . $an . '" ' . (($an == $this->data->getAnul()) ? ("selected") : "") . '>' . $an . "</option>";
				}
				echo "</select>";
			}
			if ($this->afiseazaLuni) {
				echo '&nbsp;&nbsp;&nbsp;&nbsp;Luna <select name="luna" id="luna">';
				for ($luna = 1; $luna <= 12; $luna++) {
					echo '<option value="' . $luna . '" ' . (($luna == $this->data->getLuna()) ? ("selected") : "") . ' >' . DataCalendaristica::getNumeleLunii($luna) . "</option>";
				}
				echo "</select>";
			}
			if ($this->afiseazaFirme) {
				echo '&nbsp;&nbsp;&nbsp;&nbsp;Firma
				<select name="id_firma" id="firma">
				';
				if ($this->afiseazaToateFirmele) {
					echo '<option value="" style="background:white">Toate firmele</option>';
				}
				$preferinte = '';
				if ($this->afiseazaDoarFirmeActive) {
					$preferinte = "WHERE activa = '1'";
				} else if ($this->afiseazaDoarFirmeInactive) {
					$preferinte = "WHERE activa = '0'";
				}

				$db = Aplicatie::getInstance()->Database;

				$query = (
					"SELECT nume,id,activa
					FROM firma
					" . $preferinte . "
					ORDER BY activa DESC,nume ASC"
				);

				$stmt = $db->query($query);

				foreach($stmt as $company)  {
					echo '<option value="' . $company['id'] . '" ' . (($company['id'] == $this->id_firma) ? ('selected') : "") . '  ' . (($company['activa'] == "0") ? ('style= "background:#FF5050" ') : "") . ' >' . ($company['nume']) . "</option>";
				}
				
				echo "</select>";
			}
			foreach ($this->select as $select) {
				echo '&nbsp;&nbsp;' . $select['denumire'] . ' <select name="' . $select['id'] . '">';
				foreach ($select['optiuni'] as $optiune) {
					echo '<option ' . (($_GET[$select['id']] == $optiune['valoare']) ? ("selected") : "") . ' value="' . $optiune['valoare'] . '" >' . $optiune['denumire'] . '</option>';
				}
				echo '</select>';
			}
			if ($this->afiseazaButon) {
				echo '&nbsp;&nbsp;&nbsp;&nbsp;<input value="Vizualizați" type="button" onclick="goTo(' . "'" . $this->adresaButon . '' . "'" . ')" />';
			}
			echo '</form></div>';
			if ($this->afiseazaDescriere) {
				if ($this->id_firma != '') {
					$firma_ = new FirmaSpatiu($_GET['id_firma']);
				}
				echo '<div style="text-align:center">';
				echo '<big>' . $this->type_ . ' din <b>' . $this->getDataCurenta()->getFirstDayOfMonth() . '</b> la <b>' . $this->getDataCurenta()->getLastDayOfMonth() . '</b> ' . (($this->afiseazaFirme) ? ("pentru <b>" . (($this->id_firma != '') ? ($firma_->getDenumire()) : ("toate firmele") . '</b>')) : ("")) . '</big><br /><br />';
				echo '</div>';
			}
		}
		catch (Exception $e) {
			Page::complain($e->getMessage());
			die();
		}
	}
}
