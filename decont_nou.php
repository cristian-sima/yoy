<?php

require_once "include/php/Guvern.php";
require_once "include/php/Aplicatie.php";
require_once "include/php/Procesare.php";
require_once "include/php/FirmaSpatiu.php";
require_once "include/php/SelectSituație_GUI.php";

Page::showHeader();
Page::showContent();


Procesare::createEmptyFields($_GET, array ('id_firma', 'data'));

$_criterii_MYSQL	= (($_GET['id_firma'] != '')?("AND i.idFirma='".$_GET['id_firma']."'"):(""));

Page::showHeading("Realizati un decont nou", '
			
			<input	class="disp" type="button" value="Înapoi la deconturi" class="disp" onclick="document.location='."'".'administreaza_deconturi.php'."'".'" />');
/* ---------------- content ---------------------*/
?>

<div class="content">
	<div id="dialog"></div>
	<div id="form_wrapper" class="form_wrapper"
		style="width: 550px; display: block;">
		<form id="f1" class="register active" style=""
			action="decont_nou_POST.php" method="POST">
			<h3>
				<cufon class="cufon cufon-canvas" alt="Register"
					style="width: 106px; height: 25px;"> <canvas width="122"
					height="28"
					style="width: 122px; height: 28px; top: -2px; left: 0px;"></canvas>
				<cufontext>Decont nou</cufontext></cufon>
			</h3>
			<div class="column">


				<div>
					<label>Document:</label> <input id="document" value=""
						name="document" check="true"
						criteria='{type:"string",minSize:"1",  maxSize:"30"}' type="text">

				</div>

				<div>
					<label>Data:</label> <input check="true" class="alegeData"
						criteria='{type:"string", maxSize:"30"}' value="" name="data"
						type="text" id="data"> <span class="error">Exista o eroare</span>
				</div>

			</div>

			<div class="column">

				<div>
					<label>Suma:</label> <input id="suma" value="" name="suma"
						check="true" criteria='{type:"string",minSize:"1",  maxSize:"30"}'
						type="text"> <span class="error">Exista o eroare</span>
				</div>

				<div>
					<label>Explicație:</label> <input id="explicatie" value=""
						name="explicatie" check="true"
						criteria='{type:"string",  maxSize:"30"}' type="text">

				</div>


			</div>




			<div class="bottom">
				<div class="remember"></div>
				<input type="button" class="submit" onclick="checkForm('f1')"
					value="Realizează decont"> <a href="#" rel="login" class="linkform">&nbsp;</a>
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

<?php Page::showFooter();
