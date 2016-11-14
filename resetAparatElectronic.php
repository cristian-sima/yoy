<?php 

	$infoPage = array(
			"currentMenu" 		=> "1",
			"connection" 		=> "true",
			"type" 				=> "admin",
			"focus" 			=> "nume"
		);
	
	require 'app/functions.php';
	require_once('login/bazadb.php');
	require_once('login/login.php');
	
	check();
	checkData(array('idAparat','idFirma'),'GET','aparate.php?id='.$_GET['idFirma']);
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
		
			
			//introdu firma
			$q = "UPDATE aparat SET off='1' WHERE id='".$data['idAparat']."'";
		
			$result = mysql_query($q,$conn);
			
			if(!$result)
				reportProblem("Contactati administratorul: ".mysql_error());
			
			
			
			//introdu firma
			$q = "UPDATE indexMecanic SET a='*',b='*' WHERE idFirma = '".$data['idFirma']."' AND idAparat='".$data['idAparat']."' AND data = '".get_prev_day(date("Y-m-d"))."'";
		
			$result = mysql_query($q,$conn);
			
			if(!$result)
				reportProblem("Contactati administratorul: ".mysql_error());
			
			
			
			
			echo'<span class="confirmation">Aparatul a fost sters</span> <a href="aparate.php?id='.$data['idFirma'].' ">Înapoi</a>';
			
			
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