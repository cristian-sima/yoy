<?php

require_once "app/Aparat.php";
require_once "app/Aplicatie.php";
require_once "app/Procesare.php";
require_once "app/FirmaSpatiu.php";
require_once "app/SelectSituatie_GUI.php";

$aparat			= new Aparat($_GET['id_aparat']);

Design::showHeader();

Design::showHeading("Editează date aparat",'<input	type="button" class="disp" value="Înapoi la optiuni aparat" onclick="document.location='."'".'optiuni_aparat.php?id_aparat='.$aparat->getID()."'".'" />');


?>

	<div class="content">
		<div id="dialog"></div>
		<div id="form_wrapper" class="form_wrapper"
			style="width: 550px; display: block;">
			<form id="f1" class="register active" style=""
				action="editeaza_date_aparat_POST.php" method="POST">
				<input type="hidden" value="<?php echo $aparat->getID();?>" name="id_aparat" />
				<h3>
					<cufon class="cufon cufon-canvas" alt="Register"
						style="width: 106px; height: 25px;"> <canvas width="122"
						height="28"
						style="width: 122px; height: 28px; top: -2px; left: 0px;"></canvas>
					<cufontext> Modificare date aparat </cufontext></cufon>
				</h3>
				<div class="column">
					<div>
						<label>Nume:</label> <input check="true"
							criteria='{type:"string", maxSize:"30"}'
							value="<?php echo $aparat->getNume();?>" name="nume" type="text"
							id="nume"> <span class="error">Exista o eroare</span>
					</div>
					<div>
						<label>Serie:</label> <input id="seria"
							value="<?php echo $aparat->getSerie(); ?>" name="seria" check="true"
							criteria='{type:"string",minSize:"5",  maxSize:"30"}' type="text">
						<span class="error">Exista o eroare</span>
					</div>

				</div>
				<div class="column">
					<div>
						<label>Data expirare autorizatie:</label> <input check="true"
							class="alegeData" criteria='{type:"string", maxSize:"30"}'
							value="<?php echo$aparat->getDataAutorizatie(); ?>" name="autorizatie"
							type="text" id="autorizatie"> <span class="error">Exista o eroare</span>
					</div>
					<div>
						<label>Data expirare ver. tech.:</label> <input id="inspectie"
							class="alegeData" value="<?php echo $aparat->getDataInspectie();?>"
							name="inspectie" check="true"
							criteria='{type:"string",minSize:"5",  maxSize:"30"}' type="text">
						<span class="error">Exista o eroare</span>
					</div>

				</div>
				<div class="column">
					<div>
						<label>Observatii:</label>
						<textarea id="con" check="true" name="observatii" check="true"
							criteria='{type:"string",  maxSize:"30"}' type="text"><?php echo$aparat->getObservatii();?>
						</textarea>
						<span class="error">Exista o eroare</span>
					</div>
					<div>
						<label>Factor multiplicare mecanic:</label>
						<textarea check="true" check="true"
							criteria='{type:"numeric", minSize:"1",  maxSize:"30"}'
							id="factorM" name="factorM" type="text"><?php echo$aparat->getFactorMecanic();?>
						</textarea>
						<span class="error">Exista o eroare</span>
					</div>
				</div>
				<div class="column">
					<div>
						<label>Pret impuls :</label>
						<textarea check="true" check="true"
							criteria='{type:"numeric", minSize:"1",  maxSize:"30"}'
							id="pretImpuls" name="pretImpuls" type="text"><?php echo $aparat->getPretImpuls();?>
						</textarea>
						<span class="error">Exista o eroare</span>
					</div>
					<div>
						<label>Ordinea:</label> <input check="true" check="true"
							criteria='{type:"string",  maxSize:"3"}' id="ordinea"
							value="<?php echo $aparat->getOrdinea(); ?>" name="ordinea"
							type="text" style="width: 247px;" /> <span class="error">Exista o
							eroare</span>
					</div>

				</div>
				<div class="bottom">
					<div class="remember"></div>
					<input type="button" class="submit" onclick="checkForm('f1')"
						value="Modifica Date"> <a href="#" rel="login" class="linkform">&nbsp;</a>
					<div class="clear"></div>
				</div>
			</form>

		</div>
		<div class="clear"></div>
	</div>
	
<script> $(".alegeData").datepicker({});$(".alegeData").change(function(){$(".alegeData").datepicker("option","dateFormat","yy-mm-dd")}) </script>
<?php 
		Design::showFooter();
?>