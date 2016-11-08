<?php
	
	require_once "include/php/Aparat.php";
	require_once "include/php/Procesare.php";
	require_once "include/php/Aplicatie.php";
	require_once "include/php/FirmaSpatiu.php";
	require_once "include/php/SituatieMecanicaGraficaCompletaAparatNou.php";
	
	Page::showHeader();
	Page::showContent();
	
	try
	{
		$data			=   $_POST;
	
		Procesare::checkRequestedData(
										array('firma_id', 'in_depozit', 'serie', 'nume', 'factor_mecanic', 'pret_impuls', 'data_autorizatie', 'data_inspectie', 'observatii', 'ordinea'),
										$data,
											'adauga_aparat.php');	
	
										
	
		//introdu aparatul
		$query = "INSERT INTO `aparat`
									(`ordinea`,
									 `data_inspectie`,
									 `data_autorizatie`,
									 `nume`,
									 `serie`,
									 `factor_mecanic`,
									 `pret_impuls`,
									 `observatii`,
									 `id_firma`,
									 `in_depozit`
									 )

							VALUES 	 ('".$data['ordinea']."',
									  '".$data['data_inspectie']."',
									  '".$data['data_autorizatie']."',
									  '".$data['nume']."',
									  '".$data['serie']."',
									  '".$data['factor_mecanic']."',
									  '".$data['pret_impuls']."',
									  '".$data['observatii']."',
									  '".$data['firma_id']."',
									  '".$data['in_depozit']."'
									  )";
	
		$result = mysql_query($query, Aplicatie::getInstance()->getMYSQL()->getResource());
	
		$q = "SELECT id FROM aparat ORDER BY id DESC LIMIT 0,1";
		$result = mysql_query($q, Aplicatie::getInstance()->getMYSQL()->getResource());
		while($aparat = mysql_fetch_array($result))
		{
			$id = $aparat['id'];
		}
		
		$aparat_	= new Aparat($id);
		$data_		= new DataCalendaristica(date("Y-m-d"));
	
	
		/*------------- Adauga interval aparat -------------*/
			
			 $query = "INSERT INTO `istoric_aparat`
									(`id_firma`,
									 `id_aparat`,
									 `from_`,
									 `to_`,
									 `is_now`
									 )

							VALUES 	 ('".$data['firma_id']."',
									  '".$aparat_->getID()."',
									  '".$data_."',
									  '".$data_."',
									  '1'
									  )";
			
			$result = mysql_query($query, Aplicatie::getInstance()->getMYSQL()->getResource());		
		
		/*
		 * Daca se adauga in depozit nu e nevoie de nicio situatie
		 *
		 */
		 
		if($data['firma_id'] == "0")
		{
			Page::showConfirmation('<span class="confirmation">Aparatul a fost adaugat in depozit</span> <a href="aparate_din_depozit.php">Înapoi la depozit</a>');			
		}
		else
		{
			// adaugare la o firma de spatiu
		
			
			$firma		= new FirmaSpatiu($data['firma_id']);
			$situatie 	= new SituatieMecanicaGraficaCompletaAparatNou($firma, $aparat_, $data['mecanic_intrare'], $data['mecanic_iesire']);
			
			$aparate    = $situatie->getAparate();
			
			
			
			// Page::representVisual($situatie);
		
			/*
			 *  Daca este completata astazi, adaugam doar noul aparat
			 */
			if($situatie->isCompletata())
			{		
				$id_completare = $situatie->getIDCompletare();
				
				/*----------------- Adauga index  ---------------*/
				
				$mysql		= "INSERT INTO index_mecanic
									(`id_aparat`, 
									`id_completare`, 
									`start_intrari`, 
									`end_intrari`, 
									`start_iesiri`, 
									`end_iesiri`)
									VALUES ('".$aparat_->getID()."','".$id_completare."', '".$data['mecanic_intrare']."', '".$data['mecanic_intrare']."', '".$data['mecanic_iesire']."', '".$data['mecanic_iesire']."' );";
		
				$result = mysql_query($mysql, Aplicatie::getInstance()->getMYSQL()->getResource());
				
				
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

								VALUES 	 ('".$data['firma_id']."',
										  '".$data_."',
										  '".Aplicatie::getInstance()->getUtilizator()->getID()."'
										  )";

				$result = mysql_query($query, Aplicatie::getInstance()->getMYSQL()->getResource());
				
				
				/*-------------------- Obtine ID-ul completarii -----------------*/
				
				$q = "SELECT id FROM completare_mecanica WHERE id_firma= '".$data['firma_id']."' AND data_='".$data_."' AND autor='".Aplicatie::getInstance()->getUtilizator()->getID()."' ";
				$result = mysql_query($q, Aplicatie::getInstance()->getMYSQL()->getResource());
				
				$id_completare = null;
				
				while($completare = mysql_fetch_array($result))
				{
					$id_completare = $completare['id'];
				}	
				
				
				/*----------------- Creeaza SQL ---------------*/
				
				$mysql		= "INSERT INTO index_mecanic
									(`id_aparat`, `id_completare`, `start_intrari`, `end_intrari`, `start_iesiri`, `end_iesiri`)
									VALUES ";
				
				foreach ($aparate as $aparat) 
				{
					$mysql .= "('".$aparat['data']->getID()."','".$id_completare."', '".$aparat['situatie']['start_intrari']."', '".$aparat['situatie']['end_intrari']."', '".$aparat['situatie']['start_iesiri']."', '".$aparat['situatie']['end_iesiri']."' ),";
				}
				
				$mysql = rtrim($mysql, ",").';';
				
				$result = mysql_query($mysql, Aplicatie::getInstance()->getMYSQL()->getResource());
				
				
			}		
			
			if($data['firma_id'] != "0")
			{	
				Page::showConfirmation('<span class="confirmation">Aparatul a fost adaugat la firma !</span> <a href="aparate.php?id='.$_POST['firma_id'].' ">Înapoi</a>');
			}
		}
	}
	catch(Exception $e)
	{
		Page::showError($e->getMessage());
	}
	
	Page::showFooter();
?>