<?php

$yearStart = 2013;
$yearEnd = 2020;

function localeDate($date) {
	$_exp = explode("-", $date);
	$year = intval($_exp[0]);
	$month = ((intval($_exp[1]) < 10) ? ("0" . (intval($_exp[1]))) : (intval($_exp[1])));
	$day = ((intval($_exp[2]) < 10) ? ("0" . (intval($_exp[2]))) : (intval($_exp[2])));
	return $day.".".$month.".".$year;
}

function getMonthName ($number) {
  switch (intval($number)) {
    case 1:
    return "Ianuarie";
    break;
    case 2:
    return "Februarie";
    break;
    case 3:
    return "Martie";
    break;
    case 4:
    return "Aprilie";
    break;
    case 5:
    return "Mai";
    break;
    case 6:
    return "Iunie";
    break;
    case 7:
    return "Iulie";
    break;
    case 8:
    return "August";
    break;
    case 9:
    return "Septembrie";
    break;
    case 10:
    return "Octombrie";
    break;
    case 11:
    return "Noiembrie";
    break;
    case 12:
    return "Decembrie";
    break;

    default:
    # code...
    break;
  }
}
?>
