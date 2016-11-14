<?php

	require_once "app/Aparat.php";
	require_once "app/Procesare.php";
	require_once "app/Aplicatie.php";
	require_once "app/FirmaSpatiu.php";

	Page::showHeader();
	Page::showContent();


	if(isset($_GET['id_firma']))
	{
		$id 		= 	$_GET['id_firma'];
		$firma		=   new FirmaSpatiu($id);
	}
	else
	{
		$id = 0;
	}



?>
	<table width="100%">
			<tr>
			<td>

			</td>
			<td style="text-align:right">
			<input type="button" value="Toate aparatele" onclick="document.location='toate_aparatele.php?id=<?php echo$id;?>'" />
			</td>
			</tr>
			</table>

			<div class="content">
				<div id="dialog"></div>
				<div id="form_wrapper" class="form_wrapper" style="width: 550px; display: block;">
					<form id="f1" class="register active" style="" action="adauga_aparat_POST.php" method="POST">
						<h3>
							<cufon class="cufon cufon-canvas" alt="Register" style="width: 106px; height: 25px;">
								<canvas width="122" height="28" style="width: 122px; height: 28px; top: -2px; left: 0px;"></canvas>
								<cufontext>	Adaugați aparat
									<?php
										if(isset($_GET['id_firma'])) {
											echo " la";
										} else {
											echo' în';
										}
									?>
									<span style="color:rgb(206, 143, 27)">
									 <?php
									 if(isset($_GET['id_firma'])) {
										 echo $firma->getDenumire();
									 } else {
										 echo' depozit';
									 }
									 ?>
								 </span>
								</cufontext>
							</cufon>
						</h3>
						<div class="column">
							<div>
								<label>Seria:</label>
								<input check="true" criteria='{type:"string",  minSize: "5", maxSize:"30"}' name="serie" type="text" id="seria">
								<span class="error">Există o eroare</span>
							</div>
							<div>
								<label>Denumire:</label>
								<input id="nume" name="nume" check="true" criteria='{type:"string",  maxSize:"30"}' type="text">
								<span class="error">Există o eroare</span>
							</div>

						</div>
						<div class="column">
							<div>
								<label>Factor multiplicare <b>mecanic</b></label>
								<input value="100" check="true" criteria='{type:"numeric"}' id="factor" name="factor_mecanic" type="text" >
								<span class="error">Există o eroare</span>
							</div>

							<div>
								<label>Preț pe impuls</label>
								<input value="0.01" check="true" criteria='{type:"string"}' id="factor" name="pret_impuls" type="text" >
								<span class="error">Există o eroare</span>
							</div>
												</div>
							<div class="column">
							<div>
								<label>Dată expirare autorizație:</label>
								<input check="true" class="alegeData" criteria='{type:"string", maxSize:"30"}' value="" name="data_autorizatie" type="text" id="autorizatie">
								<span class="error">Există o eroare</span>
							</div>
							<div>
								<label>Dată expirare ver. tech.:</label>
								<input id="inspectie" class="alegeData" value="" name="data_inspectie" check="true" criteria='{type:"string",minSize:"5",  maxSize:"30"}' type="text">
								<span class="error">Există o eroare</span>
							</div>

							<?php if(isset($_GET['id_firma']))
							{ ?>
								<div>
									<label>Index mecanic <b>intrare</b>:</label>
									<input check="true" check="true" value="" criteria='{type:"string",  maxSize:"30"}'id="mecanic_intrare" name="mecanic_intrare" type="text" style="width:247px;" />
									<span class="error">Există o eroare</span>
								</div>
								<div>
									<label>Index mecanic <b>ieșire</b>:</label>
									<input check="true" check="true" value="" criteria='{type:"string",  maxSize:"30"}'id="mecanic_iesire" name="mecanic_iesire" value="100" type="text" style="width:247px;" />
									<span class="error">Există o eroare</span>
								</div>
								<?php
							} ?>

						</div>
						<div class="column">
							<div>
								<label>Observații:</label>
								<input check="true" check="true" value="" criteria='{type:"string",  maxSize:"30"}'id="obs" name="observatii" type="text" style="width:247px;" />
								<span class="error">Există o eroare</span>
							</div>
							<div>
								<label>Ordinea:</label>
								<input check="true" check="true" value="" criteria='{type:"string",  maxSize:"3"}'id="ordinea" name="ordinea" value="100" type="text" style="width:247px;" />
								<span class="error">Există o eroare</span>
							</div>
						</div>


						<input type="hidden" value="<?php echo $id;?>" name="firma_id" />
						<?php if($id == 0)
						{
							echo '	<input type="hidden" value="1" name="in_depozit" />';
						}
						else
						{
							echo '	<input type="hidden" value="0" name="in_depozit" />';
						}
						?>

						<div class="bottom">
							<div class="remember">

							</div>
							<input type="button" class="submit" onclick="checkForm('f1')" value="Adauga aparat">
							<a href="#" rel="login" class="linkform">&nbsp;</a>
							<div class="clear"></div>
						</div>
					</form>

				</div>
				<div class="clear"></div>
			</div>

<script>
$( ".alegeData" ).datepicker({

   });
   	 $( ".alegeData" ).change(function() {
      $( ".alegeData" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
    });
</script>

<?php
Page::showFooter();
?>
