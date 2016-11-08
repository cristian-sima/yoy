<?php

require_once "include/php/Aparat.php";
require_once "include/php/Aplicatie.php";
require_once "include/php/Procesare.php";
require_once "include/php/FirmaSpatiu.php";
require_once "include/php/SelectSituatie_GUI.php";

$firma			= new FirmaSpatiu($_GET['id_firma']);

Page::showHeader();
Page::showContent();
Page::showHeading("Editeaza date firma",'<input	type="button" class="disp" value="Înapoi la date firma" onclick="document.location='."'".'details.php?idFirma='.$firma->getID()."'".'" />');


?>
<table width="100%">
	<tr>
		<td></td>
		<td style="text-align: right"><?php if($firma->isActiva())
		{?> <input type="button" value="Inchide contract"
			onclick="confirmRequest('Esti sigur ca vrei sa inchei contractul ?', 'inchide_contract_firma.php?id_firma=<?php echo$firma->getID();?>')" />
			<?php
		} ?>
		</td>
	</tr>
</table>

<div class="content">
	<div id="dialog"></div>
	<div id="form_wrapper" class="form_wrapper"
		style="width: 550px; height: 496px; display: block;">
		<form id="f1" class="register active" style=""
			action="editare_date_firma_POST.php" method="POST">
			<input type="hidden" value="<?php echo$firma->getID();?>" name="id_firma" />
			<h3>
				<cufon class="cufon cufon-canvas" alt="Register"
					style="width: 106px; height: 25px;"> <canvas width="122"
					height="28"
					style="width: 122px; height: 28px; top: -2px; left: 0px;"></canvas>
				<cufontext> Editare date firma</cufontext></cufon>
			</h3>
			<div class="column">
				<div>
					<label>Nume:</label> <input check="true"
						criteria='{type:"string",  minSize: "5", maxSize:"30"}'
						value="<?php echo $firma->getDenumire();?>" name="nume" type="text"
						id="nume"> <span class="error">Exista o eroare</span>
				</div>
				<div>
					<label>Localitate:</label> <input id="localitate"
						value="<?php echo$firma->getLocatie();?>" name="localitate"
						check="true" criteria='{type:"string",minSize:"5",  maxSize:"30"}'
						type="text"> <span class="error">Exista o eroare</span>
				</div>

			</div>
			<div class="column">
				<div>
					<label>Comentarii:</label>
					<textarea id="con" check="true" name="comentarii" check="true"
						criteria='{type:"string",  maxSize:"30"}' type="text">
						<?php echo$firma->getComentarii();?>
					</textarea>
					<span class="error">Exista o eroare</span>
				</div>
				<div>
					<label>Date de contact:</label>
					<textarea check="true" check="true"
						criteria='{type:"string",  maxSize:"30"}' id="dateContact"
						name="dateContact" type="text">
						<?php echo$firma->getDateContact();?>
					</textarea>
					<span class="error">Exista o eroare</span>
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

<?php 

	Page::showFooter();