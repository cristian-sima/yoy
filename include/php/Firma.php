<?php
abstract class Firma {
  protected $id;
  protected $denumire;
  protected $locatie;
  public function getID() {
    return $this->id;
  }
  public function getLocatie() {
    return $this->locatie;
  }
  public function getDenumire() {
    return $this->denumire;
  }
}
