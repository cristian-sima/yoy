<?php

require_once "app/Procesare.php";
require_once "app/Aplicatie.php";

Login::permiteOperator();
Page::showHeader();
Page::showContent();


// for sql_injection for POST
foreach ($_POST as $index => $value)
{
	$_POST[$index] 	= mysql_real_escape_string($value);
}

// for sql_injection for GET
foreach ($_GET as $index => $value)
{
	$_GET[$index] 	= mysql_real_escape_string($value);
}



try
{
	$data			=   $_POST;

	Procesare::checkRequestedData(array('_to','valoare','explicatie','data','tip','auto','document'), $data, 'adauga_interval_taxa?type='.$_POST['tip']);


	/*
	 * Verificare valoare
	 */

	$data['valoare']  = str_replace(",",".",$data['valoare']);
	if(!is_numeric  ($data['valoare']))
	{
		throw new Exception("Valoare trebuie sa fie numerica. (Daca doriti sa scrieti cu zecimale folositi punctul");
	}

	if($data['valoare'] <= 0)
	{
		throw new Exception("Valoare trebuie sa fie mai mare sau egala cu 0");
	}


	/*
	 * Verificare data
	 */
	$_temp_data		= new DataCalendaristica($data['data']);


	// ne asiguram ca daca este operator este doar firma aia și ca nu a setat optiunea automata
	$data['_to']			= ((Aplicatie::getInstance()->getUtilizator()->isAdministrator())?($data['_to']):(Aplicatie::getInstance()->getUtilizator()->getIDFirma()));
	$data['auto']			= ((Aplicatie::getInstance()->getUtilizator()->isAdministrator())?($data['auto']):('nu'));

	// daca este operator adaugam la explicatie de cine a fost realizata (Realizata de Marinel Cristinel)
	$data['explicatie']		= ((Aplicatie::getInstance()->getUtilizator()->isAdministrator())?($data['explicatie']):($data['explicatie'].' (Realizata de '.Aplicatie::getInstance()->getUtilizator()->getNume().')'));

	$query = "INSERT INTO `dispozitie`
				(`explicatie`,
				 `data`,
				 `_to`,
				 `document`,
				 `tip`,
				 `valoare`)
		   VALUES
				 ('".$data['explicatie']."',
				 '".$data['data']."',
				 '".$data['_to']."',
				 '".$data['document']."',
				 '".$data['tip']."',
				 '".$data['valoare']."')";

	$result = mysql_query($query, Aplicatie::getInstance()->Database);



	if($data['auto'] == 'da')
	{
		if($data['tip'] == 'plata')
		{
			$data['tip'] = 'incasare';
		}
		else
		{
			$data['tip'] = 'plata';
		}


		$query = "INSERT INTO `dispozitie`
							(
							`explicatie`,
								`data`,
								`_to`,
								`document`,
								`tip`,
								`valoare`
							)
							VALUES (
								'".$data['explicatie']."',
								'".$data['data']."',
								'".$data['_to']."',
								'".$data['document']."',
								'".$data['tip']."',
								'".$data['valoare']."'
							)";

		$result = mysql_query($query, Aplicatie::getInstance()->Database);


		Page::showConfirmation('<span class="confirmation">Dispozițiile au fost scrise </span> <a href="vizualizati_dispozitii.php">Înapoi la dispoziții</a>');
	}
	else
	{
		if(Aplicatie::getInstance()->getUtilizator()->isAdministrator())
		{
			Page::showConfirmation('
			<big><span class="confirmation">Dispoziția a fost scrisă </span></big>
			<div style="margin-left:50px;">
				<ul >
					<li><a href="vizualizare_dispozitii.php">Înapoi la dispoziții</a></li>
					<li><a href="registru_firma_spatiu.php?id_firma='.$data['_to'].'&data='.date("Y-m-d").'">Înapoi la registrul de casă al firmei</a></li>
					<li><a href="situatie_mecanica.php?id_firma='.$data['_to'].'">Înapoi la situție zilnică</a></li>
				</ul>
			</div>');
		}
		else
		{
			Page::showConfirmation("Dispoziția a fost realizată cu suscces. <a href='situatie_mecanica_operator.php'>Înapoi la situație</a>");
		}
	}

}
catch(Exception $e)
{
	Page::showError($e->getMessage());
}

Page::showFooter();
