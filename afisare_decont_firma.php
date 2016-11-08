<?php

require_once "include/php/Guvern.php";
require_once "include/php/Aplicatie.php";
require_once "include/php/Procesare.php";
require_once "include/php/FirmaSpatiu.php";
require_once "include/php/SituatieMecanica.php";
require_once "include/php/SelectSituatie_GUI.php";

Page::showHeader();
Page::showContent();


$firma 					= new FirmaSpatiu($_GET['id_firma']);
$data					= new DataCalendaristica($_GET['data']);
$from					= new DataCalendaristica($data->getFirstDayOfMonth());
$to						= new DataCalendaristica($data->getLastDayOfMonth());
$situatie				= new SituatieMecanica($from, $to, $firma);
$taxa_autorizatie		= Guvern::getTaxaDeAutorizareAparat($data);
$plata_taxa				= $situatie->getNumarulDeAparate() * $taxa_autorizatie;
$total_plati			= $situatie->getTotalInSertar()- $plata_taxa;
$total_firma_incasari	= round($firma->getProcentFirma($data) * $total_plati / 100);


$selector_GUI		= new SelectSituatie_GUI($_GET['data'], $_GET['id_firma']);
	
$selector_GUI->afiseazaButon(true);
$selector_GUI->setAdresaButon("afisare_decont_firma.php");
$selector_GUI->afiseazaDescriere(false);
$selector_GUI->afiseazaToateFirmele(false);
	
Page::showHeading("Decont ", "");

$selector_GUI->display();
?>
<link
	rel="stylesheet" type="text/css" href="include/css/decont.css">
<table width="100%">
	<tr>
		<td width="100%" style="text-align: right"><input type="button"
			class="disp" value="Printeaza" onclick="window.print()" /> <input
			type="button" class="disp" value="Inapoi la situatii"
			onclick="document.location='selecteaza_situatie.php?id_firma=<?php echo$firma->getID();?>&data=<?php echo$data;?>'" />
		
		<td>
	
	</tr>
</table>

<?php



function nice($text){

	return str_replace(".",",",$text) ;

}


?>
<center>
	<table border=0 cellpadding=0 cellspacing=0 width=634
		style='border-collapse: collapse; table-layout: fixed; width: 477pt'>
		<col width=64 style='width: 48pt'>
		<col width=62
			style='mso-width-source: userset; mso-width-alt: 2267; width: 47pt'>
		<col width=97
			style='mso-width-source: userset; mso-width-alt: 3547; width: 73pt'>
		<col width=137
			style='mso-width-source: userset; mso-width-alt: 5010; width: 103pt'>
		<col width=86
			style='mso-width-source: userset; mso-width-alt: 3145; width: 65pt'>
		<col width=60
			style='mso-width-source: userset; mso-width-alt: 2194; width: 45pt'>
		<col width=64 span=2 style='width: 48pt'>
		<tr height=17 style='height: 12.75pt'>
			<td colspan=4 height=17 class=xl995063 width=360
				style='height: 12.75pt; width: 271pt'><a name="RANGE!A1:H40">LOCATAR:
					Societatea Comercială</a></td>
			<td colspan=2 class=xl1015063 width=146
				style='border-left: none; width: 110pt'><?php echo Aplicatie::getInstance()->getFirmaOrganizatoare()->getDenumire();?>
			</td>
			<td class=xl775063 width=64 style='width: 48pt'>&nbsp;</td>
			<td class=xl785063 width=64 style='width: 48pt'>&nbsp;</td>
		</tr>
		<tr height=17 style='height: 12.75pt'>
			<td colspan=4 height=17 class=xl1035063 style='height: 12.75pt'>LOCATOR:
				Societatea Comercială<span style='mso-spacerun: yes'>  </span></td>
			<td colspan=2 class=xl1025063 style='border-left: none'><?php echo $firma->getDenumire();?>
			</td>
			<td class=xl155063></td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=17 style='height: 12.75pt'>
			<td colspan=2 height=17 class=xl1085063 style='height: 12.75pt'>Sala
				din</td>
			<td colspan=2 class=xl1105063 style='border-right: 1px solid black'><?php echo $firma->getLocatie();?><span
				style='mso-spacerun: yes'> </span></td>
			<td class=xl665063></td>
			<td class=xl665063></td>
			<td class=xl155063></td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=17 style='height: 12.75pt'>
			<td height=17 class=xl675063 style='height: 12.75pt'>&nbsp;</td>
			<td class=xl665063></td>
			<td class=xl715063></td>
			<td class=xl715063></td>
			<td class=xl665063></td>
			<td class=xl665063></td>
			<td class=xl155063></td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=17 style='height: 12.75pt'>
			<td height=17 class=xl675063 style='height: 12.75pt'>&nbsp;</td>
			<td class=xl665063></td>
			<td class=xl715063></td>
			<td class=xl715063></td>
			<td class=xl665063></td>
			<td class=xl665063></td>
			<td class=xl155063></td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=24 style='height: 18.0pt'>
			<td colspan=8 height=24 class=xl1125063
				style='border-right: 1.0pt solid black; height: 18.0pt'>PROCES
				VERBAL PENTRU STABILIREA CUANTUMULUI CHIRIEI</td>
		</tr>
		<tr height=24 style='height: 18.0pt'>
			<td height=24 class=xl675063 style='height: 18.0pt'>&nbsp;</td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl865063>LUNA</td>
			<td colspan=2 class=xl1145063
				style='border-right: 1px solid black; font-size: 16px;'><?php echo DataCalendaristica::getNumeleLunii($data->getLuna()).' '.$data->getAnul();?>
			</td>
			<td class=xl895063></td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=17 style='height: 12.75pt'>
			<td height=17 class=xl675063 style='height: 12.75pt'>&nbsp;</td>
			<td class=xl655063 width=62 style='width: 47pt'>Nr. Crt.</td>
			<td class=xl655063 width=97 style='border-left: none; width: 73pt'>Simbol
				cont C/D</td>
			<td class=xl655063 width=137 style='border-left: none; width: 103pt'>Denumire
				cont</td>
			<td class=xl655063 width=86
				style='border-top: none; border-left: none; width: 65pt'>Valoare
				totala</td>
			<td class=xl855063 width=60 style='width: 45pt'></td>
			<td class=xl855063 width=64 style='width: 48pt'></td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=17 style='height: 12.75pt'>
			<td height=17 class=xl675063 style='height: 12.75pt'>&nbsp;</td>
			<td class=xl645063 width=62 style='border-top: none; width: 47pt'>0</td>
			<td class=xl635063 style='border-top: none; border-left: none'>1</td>
			<td class=xl745063 width=137
				style='border-top: none; border-left: none; width: 103pt'>2</td>
			<td class=xl755063 style='border-top: none; border-left: none'>3</td>
			<td class=xl855063 width=60 style='width: 45pt'></td>
			<td class=xl855063 width=64 style='width: 48pt'></td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=17 style='height: 12.75pt'>
			<td height=17 class=xl675063 style='height: 12.75pt'>&nbsp;</td>
			<td class=xl645063 width=62 style='border-top: none; width: 47pt'>1</td>
			<td class=xl635063 style='border-top: none; border-left: none'>708</td>
			<td class=xl635063 style='border-top: none; border-left: none'>Incasari</td>
			<td class=xl765063 style='border-top: none; border-left: none'><?php echo $situatie->getTotalIncasari();?>
			</td>
			<td class=xl855063 width=60 style='width: 45pt'></td>
			<td class=xl855063 width=64 style='width: 48pt'></td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=17 style='height: 12.75pt'>
			<td height=17 class=xl675063 style='height: 12.75pt'>&nbsp;</td>
			<td class=xl645063 width=62 style='border-top: none; width: 47pt'>2</td>
			<td class=xl635063 style='border-top: none; border-left: none'>462</td>
			<td class=xl635063 style='border-top: none; border-left: none'>Premii
				jucatori</td>
			<td class=xl765063 style='border-top: none; border-left: none'><?php echo $situatie->getTotalPremii();?>
			</td>
			<td class=xl855063 width=60 style='width: 45pt'></td>
			<td class=xl855063 width=64 style='width: 48pt'></td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=17 style='height: 12.75pt'>
			<td height=17 class=xl675063 style='height: 12.75pt'>&nbsp;</td>
			<td class=xl645063 width=62 style='border-top: none; width: 47pt'>3</td>
			<td class=xl635063 style='border-top: none; border-left: none'>446</td>
			<td class=xl635063 style='border-top: none; border-left: none'>Taxa<span
				style='mso-spacerun: yes'>  </span>autorizare</td>
			<td class=xl765063 style='border-top: none; border-left: none'><?php echo $plata_taxa;?>
			</td>
			<td class=xl855063 width=60 style='width: 45pt'></td>
			<td class=xl855063 width=64 style='width: 48pt'></td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=17 style='height: 12.75pt'>
			<td height=17 class=xl675063 style='height: 12.75pt'>&nbsp;</td>
			<td class=xl705063 width=62 style='width: 47pt'></td>
			<td class=xl715063></td>
			<td class=xl685063 style='border-top: none'>TOTAL</td>
			<td class=xl905063 style='border-top: none; border-left: none'><?php echo $total_plati;?>
			</td>
			<td class=xl915063></td>
			<td class=xl915063></td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=17 style='height: 12.75pt'>
			<td height=17 class=xl675063 style='height: 12.75pt'>&nbsp;</td>
			<td class=xl705063 width=62 style='width: 47pt'></td>
			<td class=xl715063></td>
			<td class=xl715063></td>
			<td class=xl715063></td>
			<td class=xl715063></td>
			<td class=xl715063></td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=17 style='height: 12.75pt'>
			<td height=17 class=xl675063 style='height: 12.75pt'>&nbsp;</td>
			<td class=xl705063 width=62 style='width: 47pt'></td>
			<td class=xl805063></td>
			<td class=xl805063></td>
			<td class=xl925063></td>
			<td class=xl845063></td>
			<td class=xl715063></td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=17 style='height: 12.75pt'>
			<td height=17 class=xl675063 style='height: 12.75pt'>&nbsp;</td>
			<td colspan=3 class=xl645063 width=296 style='width: 223pt'><?php echo $firma->getDenumire();?>
			</td>
			<td class=xl935063 style='border-left: none'><?php echo $firma->getProcentFirma($data).' %';?>
			</td>
			<td class=xl945063 style='border-left: none'><?php echo $total_firma_incasari;?>
			</td>
			<td class=xl925063></td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=17 style='height: 12.75pt'>
			<td height=17 class=xl955063 style='height: 12.75pt'>&nbsp;</td>
			<td class=xl805063></td>
			<td class=xl805063></td>
			<td class=xl965063></td>
			<td class=xl925063></td>
			<td class=xl845063></td>
			<td class=xl715063></td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=17 style='height: 12.75pt'>
			<td height=17 class=xl675063 style='height: 12.75pt'>&nbsp;</td>
			<td class=xl705063 width=62 style='width: 47pt'></td>
			<td class=xl805063></td>
			<td class=xl155063></td>
			<td class=xl875063></td>
			<td class=xl845063></td>
			<td class=xl715063></td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=17 style='height: 12.75pt'>
			<td height=17 class=xl675063 style='height: 12.75pt'>&nbsp;</td>
			<td class=xl705063 width=62 style='width: 47pt'></td>
			<td class=xl805063></td>
			<td class=xl155063></td>
			<td class=xl875063></td>
			<td class=xl845063></td>
			<td class=xl715063></td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=17 style='height: 12.75pt'>
			<td height=17 class=xl675063 style='height: 12.75pt'>&nbsp;</td>
			<td colspan=6 class=xl705063 width=506 style='width: 381pt'>PARTILE
				AU STABILIT DE COMUN ACORD CUANTUMUL CHIRIEI PENTRU</td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=17 style='height: 12.75pt'>
			<td height=17 class=xl675063 style='height: 12.75pt'>&nbsp;</td>
			<td class=xl705063 width=62 style='width: 47pt;'>LUNA</td>
			<td class=xl975063 style="font-size: 10px;"><?php echo DataCalendaristica::getNumeleLunii($data->getLuna()).' '.$data->getAnul();?>
			</td>
			<td class=xl725063>LA VALOAREA DE</td>
			<td class=xl985063><?php echo $total_firma_incasari;?></td>
			<td class=xl725063>LEI</td>
			<td class=xl715063></td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=17 style='height: 12.75pt'>
			<td height=17 class=xl675063 style='height: 12.75pt'>&nbsp;</td>
			<td class=xl705063 width=62 style='width: 47pt'></td>
			<td class=xl805063></td>
			<td class=xl155063></td>
			<td class=xl875063></td>
			<td class=xl845063></td>
			<td class=xl715063></td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=17 style='height: 12.75pt'>
			<td height=17 class=xl675063 style='height: 12.75pt'>&nbsp;</td>
			<td class=xl705063 width=62 style='width: 47pt'></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl715063></td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=21 style='height: 15.75pt'>
			<td height=21 class=xl675063 style='height: 15.75pt'>&nbsp;</td>
			<td class=xl735063 width=62 style='width: 47pt'></td>
			<td colspan=3 class=xl1165063 width=320 style='width: 241pt'></td>
			<td class=xl715063></td>
			<td class=xl715063></td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=17 style='height: 12.75pt'>
			<td height=17 class=xl675063 style='height: 12.75pt'>&nbsp;</td>
			<td class=xl155063></td>
			<td class=xl665063></td>
			<td class=xl665063></td>
			<td class=xl665063></td>
			<td class=xl665063></td>
			<td class=xl665063></td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=20 style='height: 15.0pt'>
			<td height=20 class=xl675063 style='height: 15.0pt'>&nbsp;</td>
			<td class=xl805063>Intocmit,</td>
			<td class=xl805063>Numele:</td>
			<td class=xl815063><?php echo Aplicatie::getInstance()->getFirmaOrganizatoare()->getPatron();?></td>
			<td class=xl725063></td>
			<td class=xl815063></td>
			<td class=xl815063></td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=20 style='height: 15.0pt'>
			<td height=20 class=xl675063 style='height: 15.0pt'>&nbsp;</td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl815063></td>
			<td class=xl725063></td>
			<td class=xl815063></td>
			<td class=xl815063></td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=17 style='height: 12.75pt'>
			<td height=17 class=xl675063 style='height: 12.75pt'>&nbsp;</td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=17 style='height: 12.75pt'>
			<td height=17 class=xl675063 style='height: 12.75pt'>&nbsp;</td>
			<td colspan=2 class=xl1055063 style='border-right: 1px solid black'><?php echo Aplicatie::getInstance()->getFirmaOrganizatoare()->getDenumire();?>
			</td>
			<td class=xl885063></td>
			<td colspan=3 class=xl1055063 style='border-right: 1px solid black'><?php echo $firma->getDenumire();?>
			</td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=17 style='height: 12.75pt'>
			<td height=17 class=xl675063 style='height: 12.75pt'>&nbsp;</td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=17 style='height: 12.75pt'>
			<td height=17 class=xl675063 style='height: 12.75pt'>&nbsp;</td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=17 style='height: 12.75pt'>
			<td height=17 class=xl675063 style='height: 12.75pt'>&nbsp;</td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=17 style='height: 12.75pt'>
			<td height=17 class=xl675063 style='height: 12.75pt'>&nbsp;</td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=17 style='height: 12.75pt'>
			<td height=17 class=xl675063 style='height: 12.75pt'>&nbsp;</td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=17 style='height: 12.75pt'>
			<td height=17 class=xl675063 style='height: 12.75pt'>&nbsp;</td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=17 style='height: 12.75pt'>
			<td height=17 class=xl675063 style='height: 12.75pt'>&nbsp;</td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=17 style='height: 12.75pt'>
			<td height=17 class=xl675063 style='height: 12.75pt'>&nbsp;</td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=17 style='height: 12.75pt'>
			<td height=17 class=xl675063 style='height: 12.75pt'>&nbsp;</td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=17 style='height: 12.75pt'>
			<td height=17 class=xl675063 style='height: 12.75pt'>&nbsp;</td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl155063></td>
			<td class=xl795063>&nbsp;</td>
		</tr>
		<tr height=18 style='height: 13.5pt'>
			<td height=18 class=xl695063 style='height: 13.5pt'>&nbsp;</td>
			<td class=xl825063>&nbsp;</td>
			<td class=xl825063>&nbsp;</td>
			<td class=xl825063>&nbsp;</td>
			<td class=xl825063>&nbsp;</td>
			<td class=xl825063>&nbsp;</td>
			<td class=xl825063>&nbsp;</td>
			<td class=xl835063>&nbsp;</td>
		</tr>

	</table>
</center>
<?php 

	Page::showFooter();

