<?php 
	$infoPage = array();
	$infoPage = array(
			"currentMenu" 		=> "1",
			"connection" 		=> "true",
			"type" 				=> "admin",
			"focus" 			=> "nume"
		);
	
	require 'app/functions.php';
	include('login/bazadb.php');
	include('login/login.php');
	
	check();
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
			
			<table width="100%">
			<tr>
			<td>
		
			</td>
			<td style="text-align:right">
			<input type="button" value="Înapoi la perioade" onclick="document.location='perioade.php?idFirma=<?php echo $_GET['idFirma'];?>'" />
			</td>
			</tr>
			</table>
			
			<div class="content">
				<div id="dialog"></div>
				<div id="form_wrapper" class="form_wrapper" style="width: 550px; height: 496px; display: block;">
					<form id="f1" class="register active" style="" action="adaugaPerioadaPOST.php" method="POST">
						<input type="hidden" name="idFirma" value="<?php echo$_GET['idFirma'];?>" />
						<h3><cufon class="cufon cufon-canvas" alt="Register" style="width: 106px; height: 25px;"><canvas width="122" height="28" style="width: 122px; height: 28px; top: -2px; left: 0px;"></canvas><cufontext>	Adaugă o perioda noua</cufontext></cufon></h3>
						<div class="column">
							<div>
								<label>De la :</label>
								<input class="alege" check="true" criteria='{type:"string",  minSize: "5", maxSize:"30"}' name="from" type="text" id="from">
								<span class="error">Exista o eroare</span>
							</div>
							<div>
								<label>Până la :</label>
								<input class="alege" id="to" name="to" check="true" criteria='{type:"string"}' type="text">
								<span class="error">Exista o eroare</span>
							</div>
							<div>
								<label>Procent :</label>
								<input  check="true" criteria='{type:"string",  minSize: "1", maxSize:"3"}' name="valoare" type="text" id="valoare">
								<span class="error">Exista o eroare</span>
							</div>
						
						</div>
						
						<div class="bottom">
							<div class="remember">
							
							</div>
							
							<input type="button" class="submit" onclick="checkForm('f1')" value="Perioada Noua">
							<a href="#" rel="login" class="linkform">Lasati câmpul liber pentru a specifica o perioada infinita (implicit o perioada din prezent)</a>
							<div class="clear"></div>
						</div>
					</form>
					
				</div>
				<div class="clear"></div>
			</div>
			</div>
		
	</div>
</body>
</html>
<script>
 $( ".alege" ).datepicker({
     

  	 
    });
	
	$( ".alege" ).change(function() {
      $( ".alege" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
    });
</script>
<?php endOfPage();?>