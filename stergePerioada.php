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
	checkData(array('idFirma','idPerioada'),'GET','main.php');
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
			
			//creates if they not exist
			createFields(array("com","dc"));
			
			//introdu firma
			$q = "DELETE from procent WHERE id='".$data['idPerioada']."' ";
		
			$result = mysql_query($q,$conn);
			
			if(!$result)
				reportProblem("Contactati administratorul: ".mysql_error());
			
			//procent
			
			echo'<span class="confirmation">Perioada a fost stearsa</span> <a href="perioade.php?id='.$data['idFirma'].'">Înapoi la perioade</a>';
			
			
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
