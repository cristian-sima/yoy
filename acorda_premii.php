
<?php

require_once "include/php/Guvern.php";
require_once "include/php/Aplicatie.php";
require_once "include/php/Procesare.php";
require_once "include/php/FirmaSpatiu.php";
require_once "include/php/SelectSituatie_GUI.php";

Login::permiteOperator();
Page::showHeader();
Page::showContent();

Procesare::createEmptyFields($_GET, array ('id_firma', 'data'));

// forteaza utilizatorul sa aiba access doar la luna curenta și la firma lui
$_GET['id_firma'] 	=	((Aplicatie::getInstance()->getUtilizator()->isAdministrator())?$_GET['id_firma']:(Aplicatie::getInstance()->getUtilizator()->getIDFirma()));
$_GET['data'] 		=	((Aplicatie::getInstance()->getUtilizator()->isAdministrator())?$_GET['data']:(new DataCalendaristica(date("Y-m-d"))));



$data 						= new DataCalendaristica((($_GET['data']!="")?$_GET['data']:date("Y-m-d")));
$prag_de_impozitare 		= Guvern::getPragDeImpozitare($data);
$procent_impozitare			= Guvern::getProcentDeImpozitare($data);
$firma						= new FirmaSpatiu($_GET['id_firma']);


Page::showHeading('<img class="disp" src="img/icons/_premii.png" align="absmiddle" />Persoanele
				care au caştigat şi au de plătit impozit:', '');

/* ---------------- content ---------------------*/



?>
<table width="100%">
	<tr>
		<td style="text-align: right"><?php
		if(Aplicatie::getInstance()->getUtilizator()->isAdministrator())
		{
			?> <input type="button" class="disp" value="Tipărește"
			onclick="window.print()" />
			 <input class="disp" type="button" value="Înapoi la situație"		onclick="document.location='situatie_mecanica.php?id_firma=<?php echo$firma->getID();?>'" />
			<?php
		}
		else
		{
			?>
			 <input class="disp" type="button" value="Înapoi la situație"		onclick="document.location='situatie_mecanica_operator.php?id_firma=<?php echo$firma->getID();?>'" />
			<?php
		}
		?>
		</td>
	</tr>
</table>

<br />
<br />
<table width="350px" style="border: 1px solid #dfdfdf">
	<tr>
		<td width="50%">Firma:</td>
		<td width="50%"><b><?php echo$firma->getDenumire();?><b>

		</td>
	</tr>
	<tr>
		<td width="50%">Punct de lucru:</td>
		<td width="50%"><b><?php echo$firma->getLocatie();?><b>

		</td>
	</tr>
	<tr>
		<td width="50%">Data:</td>
		<td><span style="color: orange"><?php echo$data;?> </span></td>
	</tr>
</table>

<form action="acorda_premii_POST.php" id="formularul" method="POST">
<?php

/// listeaza daca are pers pentru astazi

echo'</br><br /><br />';

echo'<table id="example" class="display" width="100%"><tr class="pad head"><td class="smoke" style="border:1px solid #dfdfdf">Nume</td><td style="border:1px solid #dfdfdf" class="smoke">CNP</td><td class="smoke" style="border:1px solid #dfdfdf"> Suma</td><td class="smoke" style="border:1px solid #dfdfdf">Impozit stat</td><td style="border:1px solid #dfdfdf" class="smoke disp"> Opțiuni</td></tr>';


$totalI = 0;
$totalS = 0;






$q="SELECT 	i.id,
				i.data,
				i.nume AS numeJucator,
				f.nume AS numeFirma,
				i.CNP,
				i.suma
		FROM impozit AS i
		LEFT JOIN firma AS f ON f.id=i.idFirma
		WHERE 	i.idFirma='".$firma->getID()."' AND
				i.data='".$data."'
		ORDER BY data DESC";

$safeQuery = mysql_real_escape_string($q);


$result2 = mysql_query($safeQuery, Aplicatie::getInstance()->getMYSQL()->getResource());
while($premiu = mysql_fetch_array($result2))
{
	$impozit		= 0;
	$totalDePlata	= 0;

	if($premiu['suma'] > $prag_de_impozitare)
	{
		$impozit = ($premiu['suma'] - $prag_de_impozitare) * $procent_impozitare / 100;
	}

	$totalDePlata = $premiu['suma'] 	+ $impozit;

	$totalI		+= $impozit;
	$totalS		+= $premiu['suma'];

	if(Aplicatie::getInstance()->getUtilizator()->isAdministrator())
	{
		echo '<td >'.htmlspecialchars($premiu['numeJucator']).'</td><td>'.$premiu['CNP'].'</td><td>'.$premiu['suma'].' lei</td><td style="color:green">'.$impozit.' lei</td><td class="disp">
		<input type="button" value="Șterge"
			onclick="document.location='."'".'sterge_premiu_persoana.php?id_firma='.$firma->getID().'&id_premiu='.$premiu['id'].' '."'".'" />
			</td></tr>';

	}

}



echo'<tr class="disp pad"><td><input type="text" placeholder="Numele şi prenumele" id="nume" name="nume" /> </td><td><input type="text" placeholder="CNP-ul persoanei" id="CNP" name="CNP" /> </td><td><input type="text"  placeholder="Suma caştigată" class="impozitabil" id="suma" name="suma" /> </td><td id="impozit"></td><td ></td></tr>';
echo'<tr class="pad head"><td style="border:1px solid #dfdfdf">Total </td><td style="border:1px solid #dfdfdf"></td><td style="border:1px solid #dfdfdf">'.$totalS.' lei</td><td style="border:1px solid #dfdfdf" id="impozit">'.$totalI.' lei</td><td style="border:1px solid #dfdfdf" class="disp" ></td></tr>';
echo'</table>';


?>
	<br /> <Br />
		<input type="button" class="disp" onclick="trimite()"value="Trimite date" />
			 <input type="hidden" name="persoane" id="persoane" />
			 <input type="hidden" name="id_firma" id="idFirma" value="<?php echo$firma->getID();?>" />
			  <input type="hidden" name="data"	id="data" value="<?php echo$data;?>" />
			   <input type="hidden"	name="confirmation" id="confirmation" value="false" />

</form>

<br />



<script>

var cate = 1;
var persoane = {"0":"true"};
var impozit = <?php echo$procent_impozitare;?>;
var prag = <?php echo $prag_de_impozitare;?>;

/**
	@description Valideaza un CNP
	@params p_cnp Textul care va fi validat
	@return True daca textul este un CNP valid, false in caz contrar
*/
function validCNP(p_cnp)
{
    var i = 0,
        year = 0,
        hashResult = 0,
        cnp = [],
        hashTable = [2, 7, 9, 1, 4, 6, 3, 5, 8, 2, 7, 9];
    if (p_cnp.length !== 13) {
        return false;
    }
    for (i = 0; i < 13; i++) {
        cnp[i] = parseInt(p_cnp.charAt(i), 10);
        if (isNaN(cnp[i])) {
            return false;
        }
        if (i < 12) {
            hashResult = hashResult + (cnp[i] * hashTable[i]);
        }
    }
    hashResult = hashResult % 11;
    if (hashResult === 10) {
        hashResult = 1;
    }
    year = (cnp[1] * 10) + cnp[2];
    switch (cnp[0]) {
    case 1:
    case 2:
        {
            year += 1900;
        }
        break;
    case 3:
    case 4:
        {
            year += 1800;
        }
        break;
    case 5:
    case 6:
        {
            year += 2000;
        }
        break;
    case 7:
    case 8:
    case 9:
        {
            year += 2000;
            if (year > (parseInt(new Date().getYear(), 10) - 14)) {
                year -= 100;
            }
        }
        break;
    default:
        {
            return false;
        }
    }
    if (year < 1800 || year > 2099) {
        return false;
    }
    return (cnp[12] === hashResult);
}


function calculateAge(birthYear, birthMonth, birthDay)
{
  todayDate = new Date();
  todayYear = todayDate.getFullYear();
  todayMonth = todayDate.getMonth();
  todayDay = todayDate.getDate();
  age = todayYear - birthYear;

  if (todayMonth < birthMonth - 1)
  {
    age--;
  }

  if (birthMonth - 1 == todayMonth && todayDay < birthDay)
  {
    age--;
  }
  return age;
}

function countProperties(obj) {
    var count = 0;

    for(var prop in obj) {
        if(obj.hasOwnProperty(prop))
            ++count;
    }

    return count;
}

function trimite(){

	var ok = true;



	//check numele
		if($("#nume").val().length <=5 ){
			alert("Numele trebuie să fie mai mare de 5 caractere");
			$("#nume").focus();
			ok=false;
			return;
		}

		if($("#CNP").val().length <=5 ){
			alert("CNP-ul trebuie să fie mai mare de 5 caractere");
			$("#CNP").focus();
			ok=false;
			return;
		}

		if(!validCNP($("#CNP").val())){

			alert("Acest CNP: ["+$("#CNP").val()+"] nu reprezintă un CNP valid !");
			$("#CNP").focus();
			ok=false;
			return;

		}else
		{
			//verifica daca aceasta pers are mai mult de 18 ani
			//*930426******
			var CNP = $("#CNP").val();
			var an = CNP.substr(1,2);
			var luna = CNP.substr(3,2);
			var zi = CNP.substr(5,2);

			if(an>50)
				an = '19'+an;
			else
				an = '20'+an;


			var ani = calculateAge(an,luna,zi);

			if(ani<18)
			{
				alert('Această persoană are sub 18 ani !');
				$("#CNP").focus();
				ok=false;
				return;
			}


		}

		if($("#suma").val().length <1 ){
			alert("Completati suma");
			$("#suma").focus();
			ok=false;
			return;
		}else
		{
			var text = $("#suma").val();

			text = text.replace(",",".");

			if(!isNumeric(text)){
				alert("Suma trebuie să fie numerică");
				$("#suma").focus();
				ok=false;
				return;
			}


			if(parseFloat(text)<0){
				alert("Suma trebuie să fie pozitivă");
				$("#suma").focus();
				ok=false;
				return;
			}

		}




	if(ok != false)
	{

		$("#formularul").submit();
	}

}


function isNumeric(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}


function activate(){

$(".impozitabil").keyup(function(t){

	var id = $(this).attr("id");
	var e  = id.split('_');

	var text = $(this).val();


			var text = text.replace(",",".");

	if(text.length != 0)
	{
		if(!isNumeric(text)){
			$("#impozit_"+e[1]).html("Nu este numeric");
			$("#impozit_"+e[1]).css({"color":"red"});
		}
		else
		{
			$("#impozit_"+e[1]).html("");

			var s=0,imp=0;


			s = parseFloat(text);

			$("#impozit_"+e[1]).css({"color":"green"});

			if(s>prag)
			{
				imp = ((s-prag)*impozit)/100;

				$("#impozit_"+e[1]).html(roundToTwo(imp)+" lei");

			}
			else
				$("#impozit_"+e[1]).html("0 lei");

		}
	}
});

}

function roundToTwo(value) {
    return(Math.round(value * 100) / 100);
}

activate();
</script>
<style>
@page {
	size: portrait
}

.pad td {
	padding: 5px;
}

.head td {
	background: rgb(253, 241, 240);
	font-weight: bold;
}
</style>

<?php

Page::showFooter();
