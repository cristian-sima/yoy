<?php

require_once "include/php/Aparat.php";
require_once "include/php/FirmaSpatiu.php";
require_once "include/php/Aplicatie.php";
require_once "include/php/SituatieMecanicaTotaluri.php";
require_once "include/php/SituatieMecanicaGraficaCompletaAparatNou.php";


Page::showHeader();
Page::showContent();

try
{
	$data				= $_POST;
	$aparat_			= new Aparat($data['id_aparat']);

	/*
	 * Verifica daca aparatul se poate muta
	 */
	if(!$aparat_->isActiv())
	{
		Page::complain("Aparatul a fost deja scos din uz !");
	}
	else
	{
		$today		= new DataCalendaristica(date("Y-m-d"));


		/*
		 * Modifica istoricul aparatului
		 */
		$q = "UPDATE  `istoric_aparat` SET `is_now`	= '0',
											`to_`	= '".$today."'
											 WHERE id_aparat = '".$data['id_aparat']."' AND is_now ='1' ";
		$result = mysql_query($q, Aplicatie::getInstance()->Database) or die(mysql_error());



		/*
		 *
		 * --------------------------- Adauga o noua situatie
		 *
		 */


		$firma		= new FirmaSpatiu($data['id_firma_noua']);
		$data_		= new DataCalendaristica(date("Y-m-d"));
		$situatie 	= new SituatieMecanicaGraficaCompletaAparatNou($firma, $aparat_, $data['mecanic_intrare'], $data['mecanic_iesire']);

		$aparate    = $situatie->getAparate();


		$id_completare_situatie = null;


		/*
		 *  Daca este completata astazi, adaugam doar noul aparat
		 */

		if($situatie->isCompletata())
		{
			$id_completare_situatie 	= $situatie->getIDCompletare();
			$id_completare 				= $situatie->getIDCompletare();

			/*----------------- Adauga index  ---------------*/

			$mysql		= "INSERT INTO index_mecanic
								(`id_aparat`,
								`id_completare`,
								`start_intrari`,
								`end_intrari`,
								`start_iesiri`,
								`end_iesiri`)
								VALUES ('".$aparat_->getID()."','".$id_completare."', '".$data['mecanic_intrare']."', '".$data['mecanic_intrare']."', '".$data['mecanic_iesire']."', '".$data['mecanic_iesire']."' );";

			$result = mysql_query($mysql, Aplicatie::getInstance()->Database);

			/*------------- Adauga interval aparat -------------*/

			 $query = "INSERT INTO `istoric_aparat`
									(`id_firma`,
									 `id_aparat`,
									 `from_`,
									 `to_`,
									 `is_now`
									 )

							VALUES 	 ('".$firma->getID()."',
									  '".$aparat_->getID()."',
									  '".$data_."',
									  '".$data_."',
									  '1'
									  )";

			$result = mysql_query($query, Aplicatie::getInstance()->Database);
		}
		else
		{
			// die();
			 /*------------------  Adaugare o noua situatie ------------ */

			 $query = "INSERT INTO `completare_mecanica`
									(`id_firma`,
									 `data_`,
									 `autor`
									 )

							VALUES 	 ('".$firma->getID()."',
									  '".$data_."',
									  '".Aplicatie::getInstance()->getUtilizator()->getID()."'
									  )";

			$result = mysql_query($query, Aplicatie::getInstance()->Database);


			/*-------------------- Obtine ID-ul completarii -----------------*/

			$q = "SELECT id FROM completare_mecanica WHERE id_firma= '".$firma->getID()."' AND data_='".$data_."' AND autor='".Aplicatie::getInstance()->getUtilizator()->getID()."' ";
			$result = mysql_query($q, Aplicatie::getInstance()->Database);


			while($completare = mysql_fetch_array($result))
			{
				$id_completare_situatie = $completare['id'];
			}


			/*----------------- Creeaza SQL ---------------*/

			$mysql		= "INSERT INTO index_mecanic
								(`id_aparat`, `id_completare`, `start_intrari`, `end_intrari`, `start_iesiri`, `end_iesiri`)
								VALUES ";

			foreach ($aparate as $aparat)
			{
				$mysql .= "('".$aparat['data']->getID()."','".$id_completare_situatie."', '".$aparat['situatie']['start_intrari']."', '".$aparat['situatie']['end_intrari']."', '".$aparat['situatie']['start_iesiri']."', '".$aparat['situatie']['end_iesiri']."' ),";
			}

			$mysql = rtrim($mysql, ",").';';

			$result = mysql_query($mysql, Aplicatie::getInstance()->Database);

			/*------------- Adauga interval aparat -------------*/

			 $query = "INSERT INTO `istoric_aparat`
									(`id_firma`,
									 `id_aparat`,
									 `from_`,
									 `to_`,
									 `is_now`
									 )

							VALUES 	 ('".$firma->getID()."',
									  '".$aparat_->getID()."',
									  '".$data_."',
									  '".$data_."',
									  '1'
									  )";

			$result = mysql_query($query, Aplicatie::getInstance()->Database);
		}


		$q = "UPDATE  `aparat` SET `id_firma`	= '".$firma->getID()."' WHERE id = '".$data['id_aparat']."'  ";
		$result = mysql_query($q, Aplicatie::getInstance()->Database) or die(mysql_error());


		/*
		 *
		 *	 MODIFICARE TOTALURI SITUATIE MECANICA folosind indexurile din baza de date
		 *
		 *
		 *
		 */



		// in felul asta se verifica daca avem situatie
		$situatie_mecanica =  new SituatieMecanica($data_, $data_, $firma);


		// actualizare totaluri
		$mysql	= "UPDATE `completare_mecanica`
					SET 	`total_incasari` = '".$situatie_mecanica->getTotalIncasari()."'
					WHERE `id` = '".$id_completare_situatie."' ";
		mysql_query($mysql, Aplicatie::getInstance()->Database);



		Page::showConfirmation("Aparatul a fost mutatat la noua firma ! <a href='situatie_mecanica.php?id_firma=".$data['id_firma_noua']."'>Înapoi la Firmă nouă</a>");


	}
}
catch(Exception $e)
{
	Page::showError($e->getMessage());
}

Page::showFooter();
