<?php
require 'app/Aplicatie.php';
Design::showHeader();

?>

<table width="100%" id="heading">
	<tr>
		<td>
			<h2 style="color: orange">
				<img src="public/images/firme.png" align="absmiddle" />Adaugă firmă
			</h2>
		</td>
		<td style="text-align: right">
			<input type="button" value="Înapoi la firme" onclick="document.location='space_companies.php'" />
		</td>
	</tr>
</table>
<div class="content">
	<div id="dialog"></div>
	<div id="form_wrapper" class="form_wrapper" style="width: 550px; display: block;">
		<form id="f1" class="register active" style="" action="firma_noua_POST.php" method="POST">
			<h3>
				<cufon class="cufon cufon-canvas" alt="Register"
				style="width: 106px; height: 25px;">
				<canvas width="122"
				height="28"
				style="width: 122px; height: 28px; top: -2px; left: 0px;"></canvas>
				<cufontext>Adaugă firmă</cufontext>
			</cufon>
		</h3>
		<div class="column">
			<div>
				<label>Denumire:</label>
				<input check="true" criteria='{type:"string",  minSize: "5", maxSize:"30"}' name="nume" type="text" id="nume">
				<span class="error">Exista o
					eroare</span>
				</div>
				<div>
					<label>Localitate:</label>
					<input id="localitate" name="localitate" check="true" criteria='{type:"string",minSize:"5",  maxSize:"30"}' type="text">
					<span class="error">Exista o eroare</span>
				</div>
				<div>
					<label>Procent câștiguri:</label>
					<input check="true" criteria='{type:"numeric",  minSize: "1", maxSize:"3"}' id="procent" name="procent" type="text">
					<span class="error">Exista
						o eroare</span>
					</div>
				</div>
				<div class="column">
					<div>
						<label>Comentarii:</label>
						<textarea id="comentarii" check="true" name="comentarii" check="true" criteria='{type:"string",  maxSize:"30"}' type="text"></textarea>
						<span class="error">Exista o eroare</span>
					</div>
					<div>
						<label>Date de contact:</label>
						<textarea check="true" check="true" criteria='{type:"string",  maxSize:"30"}' id="date_contact" name="date_contact" type="text"></textarea>
						<span class="error">Exista o eroare</span>
						<i><small>Info: Vei putea adăuga administratorii ulterior.</small></i>
					</div>
				</div>
				<div class="column"></div>
				<div class="bottom">
					<input type="button" class="submit" onclick="checkForm('f1')" value="Firmă nouă" />
					<div class="clear"></div>
					</div>
				</form>
			</div>
			<div class="clear"></div>
		</div>
		<?php Design::showFooter(); ?>
