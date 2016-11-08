<?php

require_once "include/php/Aplicatie.php";
require_once "include/php/Procesare.php";
require_once "include/php/FirmaSpatiu.php";
require_once "include/php/SelectSituatie_GUI.php";

Login::permiteOperator();

Procesare::createEmptyFields($_GET, array ('id_firma'));

Page::showHeader();
Page::showContent();
Page::showHeading("Adaugă dispoziție", ((Aplicatie::getInstance()->getUtilizator()->isAdministrator())?('<input type="button" value="Înapoi la dispoziții" onclick="document.location='."'".'vizualizare_dispozitii.php?id_firma='.$_GET['id_firma']."'".'" />'):('<input	type="button" class="disp" value="Înapoi la situație" onclick="document.location='."'".'situatie_mecanica_operator.php'."'".'" />')));




echo '
<div class="content">
<div id="form_wrapper" class="form_wrapper"
	style="width: 550px; display: block;">
	<form id="f1" class="register active" style=""
		action="dispozitie_noua_POST.php" method="POST">
		<h3>
			<cufon class="cufon cufon-canvas" alt="Register"
				style="width: 106px; height: 25px;"> <canvas width="122" height="28"
				style="width: 122px; height: 28px; top: -2px; left: 0px;"></canvas>
			<cufontext>Adaugă dispoziție</cufontext></cufon>
		</h3>'; ?>
		<div class="column">
			<div>
				<select style="margin-left: 19px;margin-top: 49px;font-size: 15px;" name="tip">
					<option value="incasare">Plătesc catre <?php echo Aplicatie::getInstance()->getFirmaOrganizatoare()->getDenumire()?></option>
					<option value="plata">Încasez de la <?php echo Aplicatie::getInstance()->getFirmaOrganizatoare()->getDenumire()?></option>
				</select>
			</div>
			<div>
				<label>Valoare:</label> <input id="valoare" value="" name="valoare"
					check="true" criteria='{type:"string",minSize:"1",  maxSize:"30"}'
					type="text"> <span class="error">Exista o eroare</span>
			</div>
			<div>
				<label>Data:</label> <input check="true" class="alegeData"
					criteria='{type:"string", maxSize:"30"}' value="" name="data"
					type="text" id="data"> <span class="error">Exista o eroare</span>
			</div>


		</div>
		<div class="column">

			<input type="hidden" id='_to' name='_to' value="<?php echo Aplicatie::getInstance()->getUtilizator()->getIDFirma(); ?>" />
			<div>
				<label>Document:</label> <input id="document" value=""
					name="document" check="true"
					criteria='{type:"string",minSize:"1",  maxSize:"30"}' type="text">

			</div>
			<div><br />
				<label>Explicație opțională:</label> <input id="explicatie" value=""
					name="explicatie" check="true"
					criteria='{type:"string", maxSize:"60"}' type="text"> <span
					class="error">Exista o eroare</span>
			</div>
		</div>


		<input type="hidden" value="nu" name="auto" id="auto" />

		<div class="bottom">
			<div class="remember"></div>
			<input type="button" class="submit" onclick="checkForm('f1')"
				value="Adaugă dispoziție"> <a href="#" rel="login" class="linkform">&nbsp;</a>
			<div class="clear"></div>
		<?php
		echo '
		</div>
	</form>
</div>
</div>
<div class="clear"></div>

<script>
$(".alegeData").datepicker({});$(".alegeData").change(function(){$(".alegeData").datepicker("option","dateFormat","yy-mm-dd")});$(function(){$(document).tooltip()})
</script>';

Page::showFooter();
