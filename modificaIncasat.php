<?php 

	$infoPage = array(
			"currentMenu" 		=> "1",
			"connection" 		=> "true",
			"type" 				=> "normal",
			"mobile"			=> "true",
			"focus" 			=> "undefined"
		);
	
	require 'include/php/functions.php';
	require_once('login/bazadb.php');
	require_once('login/login.php');
	
	check();
	checkData(array('idFirma','suma1','suma2'),'POST','index.php');
?>
<!DOCTYPE>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo_headerMobile(); ?>
</head>
<body>
	<div id="container">
		<div id="menu" style="background:white">
			<?php echo_menuMOBILE();?>
 
		<div id="content" style="border-top: 1px solid gray;background:white;border:1px soliod gray">
			
			
			<?php 
			
			//creates if they not exist
			createFields(array("com","dc"));
			
			//introdu firma
			$q = "UPDATE  firma  SET restanta='".($data['suma1']-$data['suma2'])."' WHERE id='".$data['idFirma']."' ";
		
			$result = mysql_query($q,$conn);
			
			if(!$result)
				reportProblem("Contactati administratorul: ".mysql_error());
			
			
			//introdu firma
			$q = "UPDATE  `incasare`  SET catAplatit='".($data['suma2'])."' WHERE  data='".date("Y-m-d")."' AND idFirma='".$data['idFirma']."' ";
		
			$result = mysql_query($q,$conn);
			
			if(!$result)
				reportProblem("Contactati administratorul: ".mysql_error());
			
			
			
			//procent
			
			echo'<span class="confirmation">Restanta a fost memorata</span> <a href="index.php ">Înapoi la firme</a>';
			
			
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
