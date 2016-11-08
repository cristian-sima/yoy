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
	checkData(array('toate','idFirma'),'POST','index.php');
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
			
			$toateApp = explode('_',$data['toate']);
			
			for($i=1;$i<count($toateApp);$i++){
				
				$q = "UPDATE indexElectronic SET a='0',b='0'
				WHERE idAparat = '".$toateApp[$i]."' AND data='".date("Y-m-d")."';";
				
				
				$result = mysql_query($q,$conn) or die(mysql_error());
			
				if(!$result)
					reportProblem("Contactati administratorul: ".mysql_error());
			
			}
			echo'<span class="confirmation">Aparatele au fost resetate cu succes la 0 </span> <a href="mobile.php">Inapoi la firme</a>';
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
