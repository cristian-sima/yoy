<?php

require_once "include/php/Aplicatie.php";

Page::showHeader();
Page::showContent();


Page::showHeading('<img src="img/setari.png" align="absmiddle" /> Setări', "");

echo'<br />


<br />


<link href="include/css/fieldset.css" rel="stylesheet" type="text/css"/>
		<form action="setari_POST.php" method="POST">
				<fieldset>
				<legend>Date firmă</legend>
					<table border="0" width="100%">
					<tr><td width="50%">
					Denumire:</td><td width="50%"> <input type="text" id="nume" name="nume" value="'.Aplicatie::getInstance()->getFirmaOrganizatoare()->getDenumire().'"/></td></tr>

					<tr><td width="50%">
					Patron:</td><td width="50%"> <input type="text" name="patron" value="'.Aplicatie::getInstance()->getFirmaOrganizatoare()->getPatron().'"/></td></tr>
					<tr><td width="50%">
					Domiciliu fiscal:</td><td width="50%"> <input type="text" name="localitate" value="'.Aplicatie::getInstance()->getFirmaOrganizatoare()->getLocatie().'"/></td></tr>
					</table>
					<center><input type="submit" value="Modifică datele"/></center>
				</fieldset>
				</form>
				';

/// !!!!!!!!!!!!!!!!!!!


echo'<br /><br /><form action="modificaSetari.php" method="POST">
				<fieldset>
				<legend>Taxa pe aparat</legend>';
$id=0;
$q="SELECT valoare from taxa WHERE tip='aparat' AND isNow='1' limit 0,1";


$result2 = mysql_query($q,Aplicatie::getInstance()->Database);

if(mysql_num_rows($result2) == 0){



}
while($row2 = mysql_fetch_array($result2)){
	echo'<table width="100%" border="0"><tr><td width="100%">Valoare curentă: <b style="color:orange">'.$row2['valoare'].'</b> lei</td><td align="right">';
	?>
	<input type="button" value="Adaugă perioadă"
		onclick="document.location='adauga_interval_taxa.php?tip=aparat'" />
	</td>
	</tr>
	</table>
	<?php
}



echo'</br><br /><br />';

$q="SELECT * from taxa WHERE tip='aparat' order by _from asc";


$result = mysql_query($q,Aplicatie::getInstance()->Database);

if(mysql_num_rows($result) == 0){
	echo'<table width="100%" border="0"><tr><td width="50%"><span style="color:red">Completează o periodă curentă</span></td><td align="right">';
	?>
	<input type="button" value="Adaugă perioadă"
		onclick="document.location='adauga_interval_taxa.php?tip=aparat'" />
	</td>
	</tr>
	</table>
	<?php

}else
{
	echo'<table width="100%"><tr><tD  class="smoke" style="background: rgb(231, 231, 231);">De la</td><td class="smoke" style="background: rgb(231, 231, 231);">La</td><td style="background: rgb(231, 231, 231);" class="smoke" style="background: rgb(231, 231, 231);">Valoare</td><td class="smoke" style="background: rgb(231, 231, 231);"> Opțiuni</td></tr>';
	while($r = mysql_fetch_array($result)){
		echo'<td>'.$r['_from'].'</td><td>'.(($r['isNow']=="1")?"Prezent":$r['_to']).'</td><td>'.$r['valoare'].'</td><td>';


		?>

	<input type="button" value="Șterge"
		onclick="document.location='sterge_interval_taxa.php?id=<?php echo$r['id'];?>'" />
		<?php
		echo'</td></tr>';
	}

	echo'</table>';

}
echo'</fieldset></form>';


////

?>

<script>
$(document).ready(function() {
    $('#example').dataTable({
					});
} );
</script>
<?php

	Page::showFooter();
