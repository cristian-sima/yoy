<?php
	
	require_once "include/php/Aparat.php";
	require_once "include/php/Procesare.php";
	require_once "include/php/Aplicatie.php";
	require_once "include/php/FirmaSpatiu.php";
	
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
						<h3><cufon class="cufon cufon-canvas" alt="Register" style="width: 106px; height: 25px;"><canvas width="122" height="28" style="width: 122px; height: 28px; top: -2px; left: 0px;"></canvas><cufontext>	Adaugati aparat <span style="color:rgb(206, 143, 27)"> <?php if(isset($_GET['id_firma'])) echo' la <br /><center>'.$firma->getDenumire().'</center></span>'; else echo' in depozit';?></cufontext></cufon></h3>
						<div class="column">
							<div>
								<label>Seria:</label>
								<input check="true" criteria='{type:"string",  minSize: "5", maxSize:"30"}' name="serie" type="text" id="seria">
								<span class="error">Exista o eroare</span>
							</div>
							<div>
								<label>Nume:</label>
								<input id="nume" name="nume" check="true" criteria='{type:"string",  maxSize:"30"}' type="text">
								<span class="error">Exista o eroare</span>
							</div>
									
						</div>
						<div class="column">
							<div>
								<label>Factor Multiplicare Mecanic</label>
								<input value="100" check="true" criteria='{type:"numeric"}' id="factor" name="factor_mecanic" type="text" >
								<span class="error">Exista o eroare</span>
							</div>
							
							<div>
								<label>Pret pe impuls</label>
								<input value="0.01" check="true" criteria='{type:"string"}' id="factor" name="pret_impuls" type="text" >
								<span class="error">Exista o eroare</span>
							</div>
								
							
						</div>
						
							<div class="column">
							<div>
								<label>Data expirare autorizatie:</label>
								<input check="true" class="alegeData" criteria='{type:"string", maxSize:"30"}' value="" name="data_autorizatie" type="text" id="autorizatie">
								<span class="error">Exista o eroare</span>
							</div>
							<div>
								<label>Data expirare ver. tech.:</label>
								<input id="inspectie" class="alegeData" value="" name="data_inspectie" check="true" criteria='{type:"string",minSize:"5",  maxSize:"30"}' type="text">
								<span class="error">Exista o eroare</span>
							</div>
							
							<?php if(isset($_GET['id_firma'])) 
							{ ?>
								<div>
									<label>Index mecanic intrare:</label>
									<input check="true" check="true" value="" criteria='{type:"string",  maxSize:"30"}'id="mecanic_intrare" name="mecanic_intrare" type="text" style="width:247px;" />
									<span class="error">Exista o eroare</span>
								</div>
								<div>
									<label>Index mecanic iesire:</label>
									<input check="true" check="true" value="" criteria='{type:"string",  maxSize:"30"}'id="mecanic_iesire" name="mecanic_iesire" value="100" type="text" style="width:247px;" />
									<span class="error">Exista o eroare</span>
								</div>
								<?php 
							} ?>
							
						</div>
						<div class="column">
							<div>
								<label>Observatii:</label>
								<input check="true" check="true" value="" criteria='{type:"string",  maxSize:"30"}'id="obs" name="observatii" type="text" style="width:247px;" />
								<span class="error">Exista o eroare</span>
							</div>
							<div>
								<label>Ordinea:</label>
								<input check="true" check="true" value="" criteria='{type:"string",  maxSize:"3"}'id="ordinea" name="ordinea" value="100" type="text" style="width:247px;" />
								<span class="error">Exista o eroare</span>
							</div>
							<?php if(isset($_GET['id_firma'])) 
							{ ?>
								<div>
									<label>Index electronic intrare:</label>
									<input check="true" check="true" value="" criteria='{type:"string",  maxSize:"30"}'id="electronic_intrare" name="electronic_intrare" type="text" style="width:247px;" />
									<span class="error">Exista o eroare</span>
								</div>
								<div>
									<label>Index electronic iesire:</label>
									<input check="true" check="true" value="" criteria='{type:"string",  maxSize:"30"}'id="electronic_iesire" name="electronic_iesire" value="100" type="text" style="width:247px;" />
									<span class="error">Exista o eroare</span>
								</div>
								<?php 
							} ?>
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