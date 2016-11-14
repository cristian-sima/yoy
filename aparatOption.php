<?php
$infoPage = array();
$infoPage = array(
	"currentMenu" 		=> "1",
	"connection" 		=> "true",
	"type" 				=> "admin",
	"focus" 			=> "seria"
);

require 'app/functions.php';
include('login/bazadb.php');
include('login/login.php');

check();



$firma = array();

if(isset($_GET['id'])){
	$q = "SELECT * from firma WHERE id = '".$_GET['idFirma']."'";
	$result = mysql_query($q,$conn);
	while($row = mysql_fetch_array($result)){

		$id=	$firma['id'] = $_GET['id'];
		$firma['nume'] = $row['nume'];
		$firma['localitate'] = $row['localitate'];
	}
}
else
reportProblem('Problem');

$aparat = array();

$aparat['id'] = $_GET['id'];

$q = "SELECT firmaId,seria,off from aparat WHERE id = '".$_GET['id']."'";
$result = mysql_query($q,$conn);
while($row = mysql_fetch_array($result)){

	$aparat['idFirma'] 	= $row['firmaId'];
	$aparat['seria'] 	= $row['seria'];
	$aparat['off']		= $row['off'];
}
$isOff = ($aparat['off']=="1")?true:false;
$isInDepozit = ($aparat['idFirma']=="0")?true:false;

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
			<div id="content" style="padding:30px">

				<table width="100%" id="dispare3">
					<tr>
						<td style="padding-left:170px;">

						</td>
						<td style="text-align:right">


							<?php if($isInDepozit){?>

								<input type="button" value="Înapoi la depozit" onclick="document.location = 'depozit.php'" />

								<?php }else if(isset($_GET['aparat'])){
									?>
									<input type="submit" class="mod" value="Înapoi la aparate" onclick="document.location='aparatura.php'" />

									<?php

								} else
								{?>

									<input type="submit" class="mod" value="Înapoi la aparatele firmei" onclick="document.location='aparate.php?id=<?php echo$_GET['idFirma'];?>'" />

									<?php
								} ?>
							</td>
						</tr>
					</table>
					<big><b>Detalii aparat <span style="color:orange"><?php echo$aparat['seria'];?></span></b></big>

					<br /><Br />


					<br />


					<?php $a='';if(isset($_GET['situatie'])){
						$a = 'situatie=true&';
					}
					?>


					<input type="button" value="Editează date aparat" onclick="document.location = 'editAparat.php?<?php echo$a;?>idFirma=<?php echo$_GET['idFirma'];?>&idAparat=<?php echo$aparat['id'];?>'" />

					<?php if(!$isOff) {


						$aM = $bM =0;


						$r = "SELECT a,b from indexMecanic WHERE idAparat='".$aparat['id']."' AND a!=''  AND b!= '' order by data DESC limit 0,1";
						$result = mysql_query($r, $conn);

						while( $v = mysql_fetch_assoc( $result ) ) {

							$aM = $v['a'];
							$bM = $v['b'];

						}

						?>

						<br /><br />
						<form action ="stergeAparat.php" method="GET" id="f1">
							<fieldset>
								<legend>	Scoate aparat din uz</legend>
								<input type="hidden" name="idAparat" value="<?php echo$_GET['id'];?>" />
								<input type="hidden" name="idFirma" value="<?php echo$_GET['idFirma'];?>" />
								Indice mecanic intrare&nbsp;&nbsp;: <input type="text" name="index1" value="<?php echo$aM;?>" placeholder="" /><br />
								Indice mecanic eliminare:<input type="text" name="index2" value="<?php echo$bM;?>" placeholder="" /><br />

								<input type="submit" value="Scoate aparat din uz" onclick="confirmRequest('Esti sigur ca scoti din uz aparatul ? ', '$(`'f1`').submit()')" />
							</fieldset>
						</form>



						<br />



						<br />
						<br />
						<br />
						<br />

						<?php }

						if(!$isInDepozit && !$isOff) {?>



							<br />

							<form action ="mutaAparatInDepozit.php" method="GET" id="f1">

								<img src="public/images/firme.png" /><img src="public/images/spre.png" /><img src="public/images/depozit.png" />

								<fieldset>
									
									<legend>	Muta aparat în depozit</legend>
									<input type="hidden" name="idAparat" value="<?php echo$_GET['id'];?>" />
									<input type="hidden" name="idFirma" value="<?php echo$_GET['idFirma'];?>" />
									Indice mecanic intrare&nbsp;&nbsp;: <input type="text" name="index1" value="<?php echo$aM;?>" placeholder="" /><br />
									Indice mecanic eliminare:<input type="text" name="index2" value="<?php echo$bM;?>" placeholder="" /><br />

									<input type="submit" value="Muta în depozit" onclick="confirmRequest('Esti sigur ca vrei sa muti aparatul în depozit ? ', '$(`'f1`').submit()')" />
								</fieldset>
							</form>
							<br /><br />

							<?php }


							if(!$isOff) {?>



								<img src="public/images/firme.png" /><img src="public/images/spre.png" /><img src="public/images/firme.png" />



								<form action="mutaAparatLaFirma.php" method="GET">
									<fieldset>
										<legend>	Muta aparat la firma </legend>
										<input type="hidden" name="idAparat" value="<?php echo$_GET['id'];?>" />
										<input type="hidden" name="idFirma" value="<?php echo$_GET['idFirma'];?>" />
										Indice mecanic intrare&nbsp;&nbsp;: <input type="text" name="index1" value="<?php echo$aM;?>" placeholder="" /><br />
										Indice mecanic eliminare:<input type="text" name="index2" value="<?php echo$bM;?>" placeholder="" /><br />

										Firma:
										<select name="idWhere" />

										<?php
										$result= mysql_query("SELECT id,nume from firma WHERE activa='1' AND id != '".$_GET['idFirma']."'", $conn);

										while($f = mysql_fetch_array($result)){

											?>
											<option value="<?php echo$f['id']?>"><?php echo$f['nume']; ?>
											</option>
											<?php
										}
										?>
									</select>
									<br />
									<center>
										<input type="submit" value="Muta aparat" /></centeR>

									</fieldset>
								</form>


								<br /><br />

								Pentru a reseta indicele mecanic, scoate-ti aparatul din uz apoi adaugati-l.

								<?php }?>


							</div>
						</body>
						</html>
						<script>
							$(document).ready(function() {
								$('#example').dataTable({
								});
							} );
						</script>
						<?php endOfPage();?>
