<?php

	$infoPage = array(
			"currentMenu" 		=> "1",
			"connection" 		=> "true",
			"type" 				=> "admin",
			"focus" 			=> "undefined"
		);

	require 'include/php/functions.php';
	require_once('login/bazadb.php');
	require_once('login/login.php');

	check();
	checkData(array('from','to','idFirma','valoare'),'POST','main.php');
?>
<!DOCTYPE>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo_header(); ?>
</head>
<body>
	<div id="container">
		<div id="menu" style="background:white">
			<?php echo_menu();?>

		<div id="content" style="border-top: 1px solid gray;background:white;border:1px soliod gray">


			<?php

			$isNow=0;

			if($data['to'] ==""){
				$data['to'] = get_next_day(date("Y-m-d"));
				$isNow=1;
			}

			if(strtotime($data['from']) >= strtotime($data['to']))
				reportProblem("Prima data trebuie sa fie mai mare decat a doua !");



			//introdu firma
			$q = "INSERT INTO `procent`(`idFirma`, `_from`, `_to`, `valoare`,`isNow`) VALUES ('".$data['idFirma']."','".$data['from']."','".$data['to']."','".$data['valoare']."','".$isNow."')";
			
			$safeQuery = mysql_real_escape_string($q);

			$result = mysql_query($safeQuery, $conn);

			if(!$result)
				reportProblem("Contactati administratorul: ".mysql_error());




			//procent

			echo'<span class="confirmation">Perioada a fost creata</span> <a href="perioade.php?idFirma='.$data['idFirma'].' ">Înapoi</a>';


			?>


		</div>

	</div>
</body>
</html>
<script>
$(document).ready(function() {
    $('#example').dataTable({	"bJQueryUI": true,
					"sPaginationType": "full_numbers"});
} );
</script>
<?php endOfPage();?>
