<?php
require_once 'include/php/Aplicatie.php';

Login::permiteOperator();

if(Aplicatie::getInstance()->getUtilizator()->isAdministrator()) {
  header("Location: space_companies.php");
} else {
  header("Location: situatie_mecanica_operator.php");
}
