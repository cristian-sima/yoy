<?php

require_once "include/php/FirmaSpatiu.php";
require_once "include/php/Procesare.php";
require_once "include/php/Aplicatie.php";
require_once "include/php/Utilizator.php";
require_once "include/php/Guvern.php";

Login::permiteOperator();

Page::showHeader();
Page::showContent();


// functia care valideaza datele primite și returneaza erorile daca sunt
function is_cnp_valid($cnp_primit)
    {

    $cnp['cnp primit'] = $cnp_primit;
    // prima cifra din cnp reprezinta sexul și nu poate fi decat 1,2,5,6 (pentru cetatenii romani)
    // 1, 2 pentru cei nascuti între anii 1900 și 1999
    // 5, 6 pentru cei nsacuti dupa anul 2000
    $cnp['sex'] = $cnp['cnp primit']{0};
    // cifrele 2 și 3 reprezinta anul nasterii
    $cnp['an'] = $cnp['cnp primit']{1}.$cnp['cnp primit']{2};
    // cifrele 4 și 5 reprezinta luna (nu poate fi decat între 1 și 12)
    $cnp['luna']    = $cnp['cnp primit']{3}.$cnp['cnp primit']{4};
    // cifrele 6 și 7 reprezinta ziua (nu poate fi decat între 1 și 31)
    $cnp['zi']    = $cnp['cnp primit']{5}.$cnp['cnp primit']{6};
    // cifrele 8 și 9 reprezinta codul judetului (nu poate fi decat între 1 și 52)
    $cnp['judet'] = $cnp['cnp primit']{7}.$cnp['cnp primit']{8};
    // cifrele 10,11,12 reprezinta un nr. poate fi între 001 și 999.
    // Numerele din acest interval se impart pe judete,
    // birourilor de evidenta a populatiei, astfel inct un anumit numar din acel
    // interval sa fie alocat unei singure persoane intr-o anumita zi.

    // cifra 13 reprezinta cifra de control aflata in relatie cu
    // toate celelate 12 cifre ale CNP-ului.
    // fiecare cifra din CNP este inmultita cu cifra de pe aceeasi pozitie
    // din numarul 279146358279; rezultatele sunt insumate,
    // iar rezultatul final este impartit cu rest la 11. Daca restul este 10,
    // atunci cifra de control este 1, altfel cifra de control este egala cu restul.
    $cnp['suma de control'] = $cnp['cnp primit']{0} * 2 + $cnp['cnp primit']{1} * 7 +
        $cnp['cnp primit']{2} * 9 + $cnp['cnp primit']{3} * 1 + $cnp['cnp primit']{4} * 4 +
        $cnp['cnp primit']{5} * 6 + $cnp['cnp primit']{6} * 3 + $cnp['cnp primit']{7} * 5 +
        $cnp['cnp primit']{8} * 8 + $cnp['cnp primit']{9} * 2 + $cnp['cnp primit']{10} * 7 +
        $cnp['cnp primit']{11} * 9;
    $cnp['rest'] = fmod($cnp['suma de control'], 11);
    // setarea variabilei de erori, in cazul in care nu exista erori
    // sa returneze un array gol (altfel ar da eroare)
    $erori = array();

    if (empty($cnp['cnp primit']))
        $erori[] = 'Câmpul CNP este gol!<br>';

    else
        {
        if (! is_numeric($cnp['cnp primit']))
            $erori[] = 'CNP-ul este format doar din cifre!<br>';

        if (strlen($cnp['cnp primit']) != 13)
            {
            $cifre = strlen($cnp['cnp primit']);
            $erori[] = 'CNP-ul trebuie sa aiba 13 numere, cel introdus are doar '.$cifre.' !<br>';
            }
        if($cnp['sex'] != 1 && $cnp['sex'] != 2 && $cnp['sex'] != 5 && $cnp['sex'] != 6)
            $erori[] = 'Prima cifra din CNP - eronata!<br>';

        if($cnp['luna'] > 12 || $cnp['luna'] == 0 )
            $erori[] = 'Luna este incorecta!<br>';

        if($cnp['zi'] > 31 || $cnp['zi'] == 0)
            $erori[] = 'Ziua este incorecta!<br>';

        if ( is_numeric($cnp['luna']) && is_numeric($cnp['zi']) && is_numeric($cnp['an']) )
        {
            if (! checkdate($cnp['luna'],$cnp['zi'],$cnp['an']))
                $erori[] = 'Data de nastere specificata este incorecta<br>';
        }

        if($cnp['judet'] > 52 || $cnp['judet'] == 0)
            $erori[] = 'Codul judetului este eronat!<br>';

        if (($cnp['rest'] < 10 && $cnp['rest'] != $cnp['cnp primit']{12})
            || ($cnp['rest'] >= 10 && $cnp['cnp primit']{12} != 1))
            $erori[] = 'Cifra de control este gresita! (CNP-ul nu este valid)<br>';
        }

     if (count($erori) != 0)
     {
     	return false;
     }
     else
     {
     	return true;
     }
}




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

	$data 						= $_POST;

	// Page::representVisual($data);

	Procesare::checkRequestedData(  array('persoane','id_firma','data','suma','nume','CNP','confirmation'),$data,'acorda_premii.php');


	$firma 						= new FirmaSpatiu($_POST['id_firma']);
	$today						= new DataCalendaristica($data['data']);
	$prag_de_impozitare 		= Guvern::getPragDeImpozitare($today);
	$procent_impozitare			= Guvern::getProcentDeImpozitare($today);

	/*
	 * Verificare valoare
	 */

	$data['CNP'] = trim($data['CNP']);

	if(! is_cnp_valid($data['CNP']))
	{
		throw new Exception("CNP-ul nu este numeric");
	}

	$data['suma']  = str_replace(",",".",$data['suma']);
	if(!is_numeric  ($data['suma']))
	{
		throw new Exception("Valoare trebuie sa fie numerica. (Daca doriti sa scrieti cu zecimale folositi punctul");
	}

	if($data['suma'] <= 0)
	{
		throw new Exception("Valoare trebuie sa fie mai mare sau egala cu 0");
	}



	if(Aplicatie::getInstance()->getUtilizator()->isOperator() &&
	($data['id_firma'] != Aplicatie::getInstance()->getUtilizator()->getIDFirma()) )
	{
		throw new Exception("Access respins");
	}

	$suma_premiu 		= 0;

	$mysql = "SELECT suma
			  FROM `impozit`
			  WHERE 	`CNP`='".$data['CNP']."' AND
			  		 	`idFirma`='".$data['id_firma']."' AND
			  		 	`data`='".$data['data']."'";
	$result = mysql_query($mysql, Aplicatie::getInstance()->getMYSQL()->getResource());
	while($premiu = mysql_fetch_array($result))
	{
		$suma_premiu = $premiu['suma'];
	}


	$castigat = $suma_premiu + intval($data['suma']);

	$diferenta = 0;

	if($suma_premiu < $prag_de_impozitare)
	{
		$diferenta = $prag_de_impozitare - $suma_premiu;
	}


	// daca nu a mai fost in baza de date
	// daca nu are confirmare
	// daca are de plata impozit

	$impozit = ($data['suma']-$diferenta) * $procent_impozitare / 100;

	if(($suma_premiu != 0) && ($data['confirmation'] == 'false') && ($castigat> $prag_de_impozitare) )
	{
		echo'<span style="font-size:20px;color:orange"><img src="img/atentie.png" align="absmiddle" /> Atenție !</span><br /> <span style="color:red">Această persoană a mai jucat astăzi și a depășit pragul de impozitare. </span><br />
			<br />
			<table width="400px">
			<tr><td width="50%">Sumă totală câștigată:</td><td width="50%"> <b>'.$castigat.'</b> <span class="smoke">lei</span></td></tr>
			<tr><td width="50%">
			Sumă câștigată acum: </td><td width="50%"><b>'.($data['suma']).'</b> <span class="smoke"> lei</span></td>
			<tr><td width="50%">
			Sumă impozitată: <hr></td><td width="50%"><b>'.($data['suma'] - $diferenta).'</b>  <span class="smoke">lei</span><hr></td></tr>
			<tr><td width="50%">Impozit: <hr></td><td width="50%"><b>'.$impozit.'</b>  <span class="smoke">lei</span><hr></td></tr>
			<tr><td width="50%">Sumă restituită clientului:</td> <td width="50%"><b style="color:orange">'.($data['suma'] - $impozit).'</b>  <span class="smoke">lei</span></td></tr>
			</table>
			<br />
			<center>
			<big style="color:blue;font-size:20px">
			Validați datele ?</big>
			<br />';?>

<form action="acorda_premii_POST.php" method="POST">
	<input type="button" class="disp" value="Înapoi !"
		onclick="document.location='acorda_premii.php?id_firma=<?php echo$data['id_firma'];?>'" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="submit" style="background: green; color: white"
		value="Validez !" /> <input type="hidden" name="persoane"
		id="persoane" value="<?php echo$data['persoane']?>" /> <input
		type="hidden" name="id_firma" id="id_firma"
		value="<?php echo$data['id_firma'];?>" /> <input type="hidden"
		name="suma" id="suma" value="<?php echo$data['suma'];?>" /> <input
		type="hidden" name="nume" id="nume" value="<?php echo$data['nume'];?>" />
	<input type="hidden" name="CNP" id="CNP"
		value="<?php echo$data['CNP'];?>" /> <input type="hidden" name="data"
		id="data" value="<?php echo$data['data'];?>" /> <input type="hidden"
		name="confirmation" id="confirmation" value="true" />
</form>
</center>
		<?php

	}
	else
	{
		$castigat = $suma_premiu + $data['suma'];

		$diferenta = 0;

		if($suma_premiu < $prag_de_impozitare)
		{
			$diferenta = $prag_de_impozitare - $suma_premiu;
		}

		if($castigat < $prag_de_impozitare)
		{
			$impoz =0;
			$imp=0;
		}
		else
		{
			$impoz = $data['suma'] - $diferenta;
			$imp = ($data['suma'] - $diferenta) * $procent_impozitare / 100;
		}

		echo'<br />
					<table width="400px">
					<tr><td width="50%">
					Sumă totală câștigată:</td><td width="50%"> <b>'.$castigat.'</b> <span class="smoke">lei</span></td></tr>
					<tr><td width="50%">
					Sumă câștigată acum: </td><td width="50%"><b>'.($data['suma']).'</b> <span class="smoke"> lei</span></td>
					<tr><td width="50%">
					Sumă impozitată: <hr></td><td width="50%"><b>'.($data['suma']-$diferenta).'</b>  <span class="smoke">lei</span><hr></td></tr>
					<tr><td width="50%">Impozit: <hr></td><td width="50%"><b>'.$imp.'</b>  <span class="smoke">lei</span><hr></td></tr>
					<tr><td width="50%">Sumă restituită clientului:</td> <td width="50%"><b style="color:orange">'.($data['suma']-$imp).'</b>  <span class="smoke">lei</span></td></tr>
					</table>
					<br /><br />';



		$mysql = "SELECT suma from impozit WHERE `CNP`='".$data['CNP']."' AND `idFirma`='".$data['id_firma']."' AND `data`='".$data['data']."'";
		$result = mysql_query($mysql, Aplicatie::getInstance()->getMYSQL()->getResource());


		if(mysql_num_rows($result) != 0)
		{
			$mysql = "UPDATE impozit SET suma=suma+".$data['suma']." WHERE `CNP`='".$data['CNP']."' AND `idFirma`='".$data['id_firma']."' AND `data`='".$data['data']."'";
			$result = mysql_query($mysql, Aplicatie::getInstance()->getMYSQL()->getResource());
		}
		else
		{
			$mysql = "INSERT into impozit(`data`,`idFirma`,`CNP`,`nume`,`suma`) VALUES('".$data['data']."', '".$data['id_firma']."','".$_POST['CNP']."','".$_POST['nume']."','".$_POST['suma']."')";
			$result = mysql_query($mysql, Aplicatie::getInstance()->getMYSQL()->getResource());
		}


		Page::showConfirmation('<span class="confirmation">Datele au fost introduse cu succes în baza de date</span> <a href="acorda_premii.php?id_firma='.$data['id_firma'].'" style="color:blue"> Înapoi la pagina cu premii</a>');
	}

}
catch(Exception $e)
{
	Page::showError($e->getMessage());
}

Page::showFooter();
?>
