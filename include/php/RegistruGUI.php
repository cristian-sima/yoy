<?php

require_once "include/php/Romanian.php";
require_once "include/php/RegistruGrafic.php";
require_once "include/php/DataCalendaristica.php";

class RegistruGUI {
  private $content = null;
  private $numarulDeRanduriPePag = 20;
  private $numarulTotalDeRanduri = 0;
  private $afiseazaSoldInceput = true;
  private $afiseazaSoldFinal = true;
  private $afiseazaTotalPagina = true;
  private $afiseazaTotalurile = true;
  private $afiseazaSemnaturi = true;
  public function RegistruGUI(RegistruGrafic $content) {
    $this->content               = $content;
    $this->numarulTotalDeRanduri = $content->getNumarulDeRanduri();
  }
  public function afiseazaSoldInceput($value) {
    $this->afiseazaSoldInceput = $value;
  }
  public function afiseazaSoldFinal($value) {
    $this->afiseazaSoldFinal = $value;
  }
  public function afiseazaTotalurile($value) {
    $this->afiseazaTotalurile = $value;
  }
  public function afiseazaSemnaturi($value) {
    $this->afiseazaSemnaturi = $value;
  }
  public function afiseaza() {
    $this->numarulDePagini = ceil($this->numarulTotalDeRanduri / $this->numarulDeRanduriPePag);
    if ($this->numarulDePagini == 0) {
      echo '<span style="color:red"> Se pare că nu există date pentru <span style="color:black">' . $this->content->getTitle() . '</span><span style="color:red"> din </span><span style="color:black">' . $this->content->getDateTitle() . '</span>';
    } else {
      $this->afiseazaInceput();
      for ($pagina = 1; $pagina <= $this->numarulDePagini; $pagina++) {
        $this->afiseazaPagina($pagina);
      }
      $this->afiseazaDIVTotaluri();
    }
  }
  private function afiseazaInceput() {
    echo '<link href="include/css/tabel_GUI.css" rel="stylesheet" type="text/css"/>';
  }
  private function afiseazaHeader() {
    echo '<table class="tabel_header"><tr>';
    echo '<td class="table_header_first_row">';
    echo 'UNITATEA: ' . Aplicatie::getInstance()->getFirmaOrganizatoare()->getDenumire() . '<br />';
    echo $this->content->getPrimulRand();
    echo '</td><td class="table_header_title">';
    echo $this->content->getTitle();
    echo '</td><td class="table_header_date">';
    echo $this->content->getDateTitle();
    echo '</td></tr></table>';
  }
  private function afiseazaDIVTotaluri() {
    if ($this->afiseazaTotalurile) {
      echo '<div class="table_div privatePage_prt">
				 <table class="table_div_totaluri"><tr><th width="50%">Denumire total</th><th width="50%">Suma</th></tr>';
      foreach ($this->content->getTotaluri() as $total) {
        if ($total->getTotal() != 0) {
          echo '<tr><td width="50%">' . $total->getNume() . '</td><td width="50%">' . Romanian::currency($total->getTotal()) . '</td></tr>';
        }
      }
      echo '</table></div>';
    }
  }
  private function afiseazaPagina($i) {
    echo '<div class="table_page_div privatePage_prt">';
    $this->afiseazaPaginarea($i);
    $this->afiseazaHeader();
    $this->afiseazaHeaderPagina();
    if ($this->afiseazaSoldInceput) {
      $this->afiseazaSoldPrecendent();
    }
    $this->afiseazaRanduri($i);
    if ($this->afiseazaSoldFinal) {
      $this->afiseazaSoldPrecendent();
    }
    $this->afiseazaTabelSemnaturi();
    $this->afiseazaFooterPagina();
    echo '</div>';
  }
  private function afiseazaPaginarea($pagina) {
    echo '<table class="tabel_pagina_paginare" ><tr><td style="width:100%;text-align:right">Pagina <span class="bold">' . $pagina . '</span> din ' . $this->numarulDePagini . '</td></tr></table>';
  }
  private function afiseazaHeaderPagina() {
    echo '<table class="table_page">';
    echo '<thead><tr>';
    foreach ($this->content->getColoane() as $coloana) {
      echo '<th style="width:' . $coloana['width'] . '" >' . $coloana['content'] . '</th>';
    }
    echo '</tr><thead>';
  }
  private function afiseazaSoldPrecendent() {
    $row                                        = $this->getEmptyRow();
    $row[$this->content->getTotalTitleColumn()] = "<span class='bold'>SOLD RAPORTAT</span>";
    foreach ($this->content->getTotaluriColoane() as $coloana) {
      $row[$coloana] = "<span class='bold'>" . Romanian::currency($this->content->getSumaTotalaColoana($coloana)) . '</span>';
    }
    $this->afiseazaRand("sold_total", $row);
  }
  private function afiseazaRanduri($i) {
    $id_ultimul_rand       = ($i) * $this->numarulDeRanduriPePag;
    $nr_total_de_randuri   = $this->content->getNumarulDeRanduri();
    $total_pagina_incasari = $total_pagina_plati = 0;
    $pagina_sum_colons     = array();
    foreach ($this->content->getTotaluriColoane() as $coloana) {
      $pagina_sum_colons[$coloana] = 0;
    }
    echo '<tbody>';
    if ($id_ultimul_rand < $nr_total_de_randuri) {
      $limit = $id_ultimul_rand;
    } else {
      $limit = $nr_total_de_randuri;
    }
    for ($row = (($i - 1) * $this->numarulDeRanduriPePag) + 1; $row < $limit + 1; $row++) {
      $this_row = $this->content->getRow($row - 1);
      foreach ($this->content->getTotaluriColoane() as $coloana) {
        $pagina_sum_colons[$coloana] += $this_row[$coloana];
        $this_row[$coloana] = Romanian::currency($this_row[$coloana]);
      }
      $this->afiseazaRand("empty", $this_row);
    }
    if ($nr_total_de_randuri < $id_ultimul_rand) {
      $randuri_libere = $id_ultimul_rand - $nr_total_de_randuri;
      $rand_gol       = array();
      $rand_gol       = $this->getEmptyRow();
      for ($row = 1; $row <= $randuri_libere; $row++) {
        $this->afiseazaRand("empty", $rand_gol);
      }
    }
    echo '</tbody>';
    if ($this->afiseazaTotalPagina) {
      $row                                        = $this->getEmptyRow();
      $row[$this->content->getTotalTitleColumn()] = "<span class='bold'>TOTAL PAGINĂ</span>";
      foreach ($this->content->getTotaluriColoane() as $coloana) {
        $row[$coloana] = "<span class='bold'>" . Romanian::currency($pagina_sum_colons[$coloana]) . '</span>';
        $this->content->actualizeazaSumaTotalaColoana($coloana, $pagina_sum_colons[$coloana]);
      }
      $this->afiseazaRand("total_pagina", $row);
    }
  }
  private function getEmptyRow() {
    $rand_gol = array();
    $nr_cols  = $this->content->getNumarulDeColoane();
    for ($coloana = 0; $coloana < $nr_cols; $coloana++) {
      array_push($rand_gol, "");
    }
    return $rand_gol;
  }
  private function afiseazaRand($class, $row) {
    $nr_cols = $this->content->getNumarulDeColoane();
    echo '<tr class="' . $class . '">';
    for ($coloana = 0; $coloana < $nr_cols; $coloana++) {
      echo '<td>' . $row[$coloana] . '</th>';
    }
    echo '<tr>';
  }
  private function afiseazaTabelSemnaturi() {
    if ($this->afiseazaSemnaturi) {
      echo '<table class="table_semnaturi">';
      echo '<tr><td width="50%">Casier</td><td style="width:50%;">Compartiment financiar contabil</td></tr>';
      echo '<tr><td width="50%"><span class="completare">...........................</span></td><td style="width:50%;"><span class="completare">...........................</span></td></tr>';
      echo '</table>';
    }
  }
  private function afiseazaFooterPagina() {
    echo '</table>';
  }
}
