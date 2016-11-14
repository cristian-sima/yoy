<?php

require_once "app/Aplicatie.php";

Page::showHeader();
Page::showContent();



?>
<table id="heading">
	<tr>
		<td></td>
		<td style="text-align: right"><input type="button"
			value="Înapoi la setări" onclick="document.location='setari.php'" />
		</td>
	</tr>
</table>

<div class="content">
	<div id="dialog"></div>
	<div id="form_wrapper" class="form_wrapper"
		style="width: 550px; height: 496px; display: block;">
		<form id="f1" class="register active" style=""
			action="adauga_interval_POST.php" method="POST">
			<input type="hidden" name="tip" value="<?php echo$_GET['tip'];?>" />
			<h3>
				<cufon class="cufon cufon-canvas" alt="Register"
					style="width: 106px; height: 25px;"> <canvas width="122"
					height="28"
					style="width: 122px; height: 28px; top: -2px; left: 0px;"></canvas>
				<cufontext style="text-align:center"><center>Adaugă un nou interval pt. <?php echo $_GET['tip'];?></center></cufontext></cufon>
			</h3>
			<div class="column">
				<div>
					<label>De la :</label> <input class="alege" check="true"
						criteria='{type:"string",  minSize: "5", maxSize:"30"}'
						name="from" type="text" id="from"> <span class="error">Exista o
						eroare</span>
				</div>
				<div>
					<label>Până la :</label> <input class="alege" id="to" name="to"
						check="true" criteria='{type:"string"}' type="text"> <span
						class="error">Exista o eroare</span>

													<i><small>Info: Lăsați
													câmpul liber pentru a specifica o perioadă infinită (implicit o
													perioadă din prezent)</small></i>
				</div>
				<div>
					<label>Valoare :</label> <input check="true"
						criteria='{type:"string",  minSize: "1", maxSize:"10"}'
						name="valoare" type="text" id="valoare"> <span class="error">Exista
						o eroare</span>
				</div>

			</div>


			<div class="bottom">
				<div class="remember"></div>

				<input type="button" class="submit" onclick="checkForm('f1')"
					value="Adaugă perioadă">
				<div class="clear"></div>
			</div>
		</form>

	</div>
	<div class="clear"></div>

	<script>
 $( ".alege" ).datepicker({
    });

	$( ".alege" ).change(function() {
      $( ".alege" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
    });
</script>
<?php

Page::showFooter();
