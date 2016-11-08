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
	checkData(array('idAparat','idFirma'),'GET','index.php');
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
			
			$q = "SELECT id FROM `indexElectronic`
				WHERE idAparat = '".$data['idAparat']."'
				ORDER BY id desc
				LIMIT 1";
				
				$result = mysql_query($q,$conn) or die();
					while($row = mysql_fetch_array($result)){
					
						$lastIndex = $row['id'];
					}
		
			
			//introdu firma
			$q = "UPDATE indexElectronic SET a='0',b='0'
			WHERE id = '".$lastIndex."';
			";
			
		
		
			$result = mysql_query($q,$conn) or die(mysql_error());
			
			if(!$result)
				reportProblem("Contactati administratorul: ".mysql_error());
			
			
		
		
		
			//procent
			
			echo'<span class="confirmation">Contoarele au fost resetate fortat la 0.</span> <a href="electronic.php?id='.$data['idFirma'].' ">Inapoi la situatii</a>';
			
			
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
