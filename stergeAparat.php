<?php 

	$infoPage = array(
			"currentMenu" 		=> "1",
			"connection" 		=> "true",
			"type" 				=> "admin",
			"focus" 			=> "nume"
		);
	
	require 'include/php/functions.php';
	require_once('login/bazadb.php');
	require_once('login/login.php');
	
	check();
	checkData(array('idAparat','idFirma','index1','index2','e1','e2'),'GET','aparate.php?id='.$_GET['idFirma']);
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
			// a fost in depozit
			
			
			
			//introdu firma
			$q = "UPDATE aparat SET off='1' WHERE id='".$data['idAparat']."'";
		
			$result = mysql_query($q,$conn);
			
			if(!$result)
				reportProblem("Contactati administratorul: ".mysql_error());
			
			
			
			
			
			if($data['idFirma'] ==0){
			
				
		
			
			
			
				echo'<span class="confirmation">Aparatul a fost scos din uz</span> <a href="depozit.php">Inapoi la depozit</a>';
			
			
			}
			else
			{
			
			//gaseste unde a fost folosit ultima data
			
			
			
			
			
			
			// sterge toate datele de azi
			
			$q = "DELETE FROM `indexMecanic` WHERE idAparat='".$data['idAparat']."' AND idFirma='".$data['idFirma']."'  AND data ='".date("Y-m-d")."'";
		
		
			$result = mysql_query($q,$conn);
			
			if(!$result)
				reportProblem("Contactati administratorul: ".mysql_error());
			
				
			// introdu azi mecanic
				$q = "INSERT INTO `indexMecanic`(`type`,`idFirma`,`idAparat`, `a`, `b`, `data`) VALUES ('3','".$data['idFirma']."','".$data['idAparat']."','".intval($data['index1'])."','".intval($data['index2'])."','".((date('Y-m-d')))."')";
		
			$result = mysql_query($q,$conn);
			
			if(!$result)
				reportProblem("Contactati administratorul: ".mysql_error());
			
			
			
			// electronic
			
			
			// sterge toate datele de azi
			
			$q = "DELETE FROM `indexElectronic` WHERE idAparat='".$data['idAparat']."' AND idFirma='".$data['idFirma']."'  AND data ='".date("Y-m-d")."'";
		
		
			$result = mysql_query($q,$conn);
			
			if(!$result)
				reportProblem("Contactati administratorul: ".mysql_error());
			
				
			// introdu azi mecanic
				$q = "INSERT INTO `indexElectronic`(`type`,`idFirma`,`idAparat`, `a`, `b`, `data`) VALUES ('3','".$data['idFirma']."','".$data['idAparat']."','".intval($data['e1'])."','".intval($data['e2'])."','".((date('Y-m-d')))."')";
		
			$result = mysql_query($q,$conn);
			
			if(!$result)
				reportProblem("Contactati administratorul: ".mysql_error());
			
			
			
			
			
			
			
			echo'<span class="confirmation">Aparatul a fost sters</span> <a href="aparate.php?id='.$data['idFirma'].' ">Inapoi la aparatele firmei</a>';
			}
			
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