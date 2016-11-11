<?php
require_once 'include/php/Aplicatie.php';

if(Aplicatie::getInstance()->getUtilizator()->isAdministrator()) {
  header("Location: pagina_principala.php");
} else {
  header("Location: situatie_mecanica_operator.php");
}
