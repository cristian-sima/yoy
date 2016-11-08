<?php 
	$infoPage = array();
	$infoPage = array(
			"currentMenu" 		=> "1",
			"connection" 		=> "true",
			"type" 				=> "normal",
			"focus" 			=> "nume"
		);
	
	require 'include/php/functions.php';
	include('login/bazadb.php');
	include('login/login.php');
	
	
	if(!isset($_GET['data']))
		$_data 			= date("Y-m-d");
	else		
		$_data 			= $_GET['data'];
		
		
	if($infoUser['type'] == 'normal'){
		$_GET['idFirma'] = $_GET['id'] = $infoUser['idFirma'];
		$_data 			= date("Y-m-d");
	}
	
	$ok = false;
	$q = "SELECT * from firma WHERE id = '".$_GET['id']."'";
	$result = mysql_query($q,$conn);
	while($row = mysql_fetch_array($result)){
		$firma['activa'] 		= $row['activa'];
		$id				 		=	$firma['id'] = $_GET['id'];
		$firma['nume'] 			= $row['nume'];
		$firma['localitate']	= $row['localitate'];
		$ok 					= true;
	}

	if($firma['activa'] == '0')
		$posEditare1=false;
	else
		$posEditare1=true;

	if($ok== false)
		reportProblem("Aceasta firma nu se poate administra");
	
		
			
			
	$q="SELECT i.data,i.idFirma,f.nume AS numeFirma FROM incasare AS i LEFT JOIN firma AS f ON f.id=i.idFirma WHERE idFirma='".$firma['id']."' AND data='".$_data."' ORDER BY data DESC";
	
			
	$result2 = mysql_query($q,$conn);
			
	if(mysql_num_rows($result2) == 0)
	{
		
		$firma['id']		= $_GET['id'];
		

		$q="SELECT nume,localitate FROM firma WHERE id='".$firma['id']."'";
		$result = mysql_query($q,$conn);
		while($f = mysql_fetch_array($result))
		{		
			$firma['nume'] =	$f['nume'];
			$firma['localitate'] =	$f['localitate'];
		}
	}
	else
	{
		while($i = mysql_fetch_array($result2)){
			
			$firma['id'] 	= $i['idFirma'];
			$firma['nume'] 	= $i['numeFirma'];
		}
	}
		$__c = explode("-",$_data);
		
		$from = $__c[0].'-'.$__c[1].'-01';
		
	$chestii = explode("-",$from);
	$to = get_prev_day($chestii[0]."-".((intval($chestii[1])==12)?0:(intval($chestii[1])+1)).'-01');
		
		
$A = "SELECT valoare from taxa WHERE tip='suma' AND ( ( isNow='0' AND _from>='".$from."' AND _to <= '".$to."') OR ( isNow='1' AND '".$from."'>=_from )) LIMIT 1";					

$prag = 0;

$result = mysql_query($A, $conn) or die(mysql_error());				
while($taxa = mysql_fetch_array($result)){						
	$prag = $taxa['valoare'];					
}


		
$A = "SELECT valoare from taxa WHERE tip='procent' AND ( ( isNow='0' AND _from>='".$from."' AND _to <= '".$to."') OR ( isNow='1' AND '".$from."'>=_from )) LIMIT 1";					

$_impozit = 0;

$result = mysql_query($A, $conn) or die(mysql_error());				
while($taxa = mysql_fetch_array($result)){						
	$_impozit = $taxa['valoare'];	
	
}
	
	
	check();
?>
<!DOCTYPE>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo_header(); ?>
	<link rel="stylesheet" type="text/css" href="include/css/print.css" media="print">
	<style>
	 @page {size: portrait}	 
	 </style>
	 <style>
	.incasari{
		color:rgb(121, 192, 56)
	}
	
	.premii{
		color:rgb(56, 132, 192)
	}
	</style>
	 
</head>
<body>
	<div id="container">
		<div id="menu" style="background:white">
			<?php echo_menu();?>
 
		<div id="content" style="border-top: 1px solid gray;background:white;border:1px soliod gray">
			
			<table width="100%">
			<tr>
			<td>
		<big>Încasări:</big>
			</td>
			<td style="text-align:right">
				<?php 
				if($infoUser['type'] == 'admin')
				{
					?>
					<input type="button" class="disp" value="Tipărește" onclick="window.print()"/>
			
			<input type="button" class="disp" value="Înapoi la incasari" onclick="document.location='incasari.php?id=<?php echo$firma['id'];?>'" />
					
					<?php 
				}
				
				 ?>				
				
			</td>
			</tr>
			</table><br /><br />
			<table width="350px" style="border:1px solid #dfdfdf">
				<tr><td width="50%">Firma:</td><td width="50%"> <b><?php echo$_firma['nume'];?><b></td></tr>
				<tr><td width="50%">Punct de lucru:</td><td width="50%"> <b><?php echo$firma['localitate'];?><b></td></tr>
				<tr><td width="50%">Data:</td><td><span style="color:orange"><?php echo$_data;?></span></td></tr>
			</table>
			
			<div class="content">
				
				<?php 
				
				/// listeaza daca are pers pentru astazi
			
			echo'</br><br /><br />';
			
				
				$totalI = 0;
				$totalS = 0;
				
				
				
			
				
				
				$q="SELECT * from incasare WHERE idFirma='".$firma['id']."' AND  data='".$_data."'  LIMIT 0,1";
					
				
				$result2 = mysql_query($q,$conn);
				
				
					
				while($incasare = mysql_fetch_array($result2)){
					
					echo ''.htmlspecialchars_decode ($incasare['_interval']).'<hr><br />';
					
					echo '<b>Indici aparate: </b> <br />'.htmlspecialchars_decode ($incasare['indici']).'<br /><br /><br /><hr><br />';
					echo '<b>Serii bilete: </b> <br />'.substr($incasare['serii'],0,14).'<br />'.substr($incasare['serii'],14,16).'<hr><br />';
					echo '<b>Numar bilete: </b> '.$incasare['bilete'].' bilete<br />';
					echo '<b>Taxa bilete: </b> '.$incasare['taxaBilete'].' lei<br />';
					echo '<b>Taxa autorizare aparate </b>: '.$incasare['taxaAparate'].' lei<br /><br /><hr><br />';
					echo '<b>Încasări aparate: </b> '.$incasare['incasari'].' lei<br />';
					echo '<b>Premii aparate: </b>'.$incasare['plati'].' lei<br /><br /><br /><hr><br />';
					echo '<b>Procent partener: </b> '.$incasare['procent'].'%<br /><br />';
					echo '<b>Restanta din urma: </b> '.$incasare['restanta'].' lei<br /><br /><hr><br />';
					echo '<b>Total retinut = [ (Încasări + Taxa Bilete + Taxa autorizare - Premii ) x '.(100-$incasare['procent']).']/100 + Restanta =  : </b> <big style="color:orange"> '.$incasare['total'].' lei</big><br />';
					
					echo '<b>Cat a platit de fapt: </b> <big style="color:green">  '.$incasare['catAplatit'].' lei</big><br />';
					
					
					
				}
					
			
				?>
				
				
				<br /><br /><br />
			</div>
			</div>
		
	</div>
</body>
</html>
<script>

var cate = 1;
var persoane = {"0":"true"};
var impozit = <?php echo$_impozit;?>;
var prag = <?php echo$prag;?>;

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
			alert("Numele persoanelor trebuie sa fie mai mari de 5 caractere");
			$("#nume").focus();
			ok=false;
			return;
		}
		
		if($("#CNP").val().length <=5 ){
			alert("CNP-ul persoanelor trebuie sa fie mai mari de 5 caractere");
			$("#CNP").focus();
			ok=false;
			return;
		}
		
		if(!validCNP($("#CNP").val())){
		
			alert("Acest CNP: ["+$("#CNP").val()+"] nu reprezinta un CNP valid !");
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
				alert('Aceasta persoana are sub 18 ani !');
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
				alert("Suma trebuie sa fie numerica");
				$("#suma").focus();
				ok=false;
				return;
			}
			

			if(parseFloat(text)<0){
				alert("Suma trebuie sa fie pozitiva");
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
<?php endOfPage();?>