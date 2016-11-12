<?php

require_once "Login.php";
require_once "FirmaOrganizatoare.php";

/**
 *
 * Reprezinta obiectul care se ingrijeste de continutul HTML
 * @author			Cristian Sima
 * @data			27.02.2014
 * @version			1.2
 *
 */
class Page
{
	public static $version = "3.0";


	/**
	 *
	 * 		Afișează inceputul paginii și incepe headerul <head>. De asemenea, forteaza aplicatia sa inceapa
	 *
	 */
	public static function showHeader()
	{
		$_temp		= Aplicatie::getInstance();

		echo '	<!DOCTYPE>
				<html xmlns="http://www.w3.org/1999/xhtml">
				<head>
				<meta charset="utf-8">
				<title>YOY.ro</title>
				<meta name="description" content="Soft de gestiune online"/>
				<meta name="author" content="Cristian Sima"/>
				<link href="include/css/aplicatie.css" rel="stylesheet" type="text/css" />
				<link href="include/css/aplicatie.css" rel="stylesheet" type="text/css"/>
				<link href="include/css/extra.css" rel="stylesheet" type="text/css"/>
				<link href="include/css/jquery-ui-1.10.3.custom.min.css" rel="stylesheet" type="text/css"/>
				<link href="include/css/menu.css" rel="stylesheet" type="text/css"/>
				<link href="include/css/print.css" rel="stylesheet" type="text/css" media="print" />
				<link href="include/css/t.css" rel="stylesheet" type="text/css"/>
				<script type="text/javascript" src="include/js/extra.js" ></script>
				<script type="text/javascript" src="include/js/jquery-1.9.1.js" ></script>
				<script type="text/javascript" src="include/js/jquery-ui-1.10.3.custom.min.js" ></script>
				<script type="text/javascript" src="include/js/jquery.dataTables.js" ></script>';

			// forteaza aplicatia sa inceapa
	}



	/**
	 *
	 * Afișează meniul aplicatiei in functie de utilizator și versiunea de device
	 *
	 */
	private static function showMeniu()
	{

		echo '<table width="100%" class="hide_prt" style="background:#FBFBFB;border-bottom: 1px solid #D5D5D5;"><tr><td>';


		if(Aplicatie::getInstance()->getUtilizator()->isAdministrator())
		{
			echo'
				<div >
					<ul id="nav">
						<li>
							<a href="pagina_principala.php">Firme</a>
						</li>
						<li>
							<a href="utilizatori.php">Utilizatori</a>
						</li>
						<li>
							<a href="toate_aparatele.php">Aparate</a>
							<ul>
								<li><a href="toate_aparatele.php">Toate aparatele</a></li>
								<li><a href="aparate_din_depozit.php">Depozit</a></li>
							</ul>
						</li>
						<li>
							<a href="selecteaza_situatie.php">Situații</a>
						</li>
						<li>
							<a href="actiuni.php">Acțiuni</a>
						</li>
						<li>
							<a href="vizualizare_dispozitii.php">Dispoziții</a>
						</li>
						<li>
							<a href="depuneri.php">Depuneri</a>
						</li>
						<li>
							<a href="setari.php">Setări</a>
						</li>
					</ul>
				</div>';
		}
		else
		{
			echo'<br /><big>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;YOY.ro</big><br >';
		}


		echo'</td>
			<td style="text-align:right">
				<input type="button" onclick="document.location='."'".'editare_date_utilizator.php?id_user='.Aplicatie::getInstance()->getUtilizator()->getID()."'".'" value="Modifică datele personale" />
				<input type="button" onclick="confirmRequest('."'Ești sigur că vrei să te deconectezi ?',"."'" ."paraseste_aplicatia.php"."'".')"  value="Deconectează-te" />
			</td>
		</tr>
		</table>';
	}

	/**
	 * Afișează titlul paginii și in partea dreapta afiseaza continutul primit prin parametru
	 *
	 * @param string $title						Titlul paginii pe care se afla utilizatorul
	 * @param string $right_side				Continutul din partea dreapta
	 */
	public static function showHeading($title, $right_side)
	{
		echo'
		<table id="heading" class="hide_prt">
			<tr>
				<td>
					<h2 style="color: orange">
						'.$title.'
					</h2>
				</td>
				<td style="text-align: right; vertical-align:top">'.$right_side.' </td>
			</tr>
		</table>';
	}

	/**
	 *
	 * 	Afișează content-ul paginii. Acest content include meniul, și incepe div-ul de content
	 *
	 */
	public static function showContent()
	{
		echo '</head><body><div id="top">';

		self::showMeniu();

		echo '</div><div id="container"><div id="content">';
	}



	/**
	 *
	 * 		Afișează footer-ul paginii și inchide tagurile <div> <body> <html>
	 *
	 */
	public static function showFooter()
	{
		echo '
		</div>
		<div class="aplicatie_footer center hide_prt">
			<table width="100%">
				<tr>
					<td width:30%>&copy; YOY.ro '.date('Y').'</td>
					<td width="70%" style="text-align:right"><small> V'.self::$version.'</small></td>
				</tr>
			</table>
		</div>
		</div>
		</body>
		</html>';
	}

	/**
	 * Afișează design-ul pentru formularul de login
	 */
	public static function showCSSLogin()
	{
		echo '<link href="include/css/aplicatie.css" rel="stylesheet" type="text/css"/>';
	}

	/**
	 *
	 * Afișează formularul de conectare. De asemenea, include fisierul CSS respectiv
	 *
	 */
	public static function showLoginForm()
	{
		echo '
		<link href="include/css/login.css" rel="stylesheet" type="text/css"/>
		<div style="text-align:center">
			<form id="f1" action="request_access.php" method="POST" style="text-align:center;margin:0 auto;" >
				<div id="msg">
					<table align="center" border="0" cellspacing="2" cellpadding="1" width="270" >
						<tr>
							<td align="center"><label for="user">Utilizator:&nbsp; </label></td>
							<td><input type="text" name="user" size="7" maxlength="30" id="user" /></td>
						</tr>
						<tr>
							<td align="center"><label for="pass">Parolă: </label></td>
							<td><input type="password" name="pass" size="7" maxlength="30" id="pass" /></td>
						</tr>
					</table>
					<br />
					<table align="center" border="0" cellspacing="0" cellpadding="1" width="270" >
						<tr>
							<td> &nbsp; <input type="submit" onclick="beforeSubmit()" name="sublogin" value="Autentifică" /></td>
							<td align="center"><input type="checkbox" class="switch" name="remember" /><font size="2">Ține-mă minte</font></td>
						</tr>
					</table>
				</div>
			</form>
		</div>
		<script>document.getElementById("user").focus()</script>
		';
	}


	/**
	 *
	 * Afișează un mesaj de confirmare
	 *
	 * @param string $message			Mesajul care va fi afisat
	 *
	 */
	public static function showConfirmation($message)
	{
		echo '<div class="aplicatie_success">'.$message.'</div>';
	}


	/**
	 * Afișează un mesaj de eroare și opreste aplicatia
	 *
	 * @param string $message				Mesajul care va aparea pe ecran
	 */
	public static function complain($message)
	{
		self::showError($message);
		die();
	}

	/**
	 *
	 * Afișează un mesaj de erroare
	 * @param string $message			Mesajul care va fi afisat
	 *
	 */
	public static function showError($message)
	{
		echo '<div class="aplicatie_error">'.$message.'</div>';
	}


	/**
	 *
	 * Afișează toate campurile detinute de obiectul primit ca parametru.
	 *
	 * @param Object $obj			Obiectul care va fi afisat
	 *
	 */
	public static function representVisual($obj)
	{

		$args = func_get_args();

		$backtrace = debug_backtrace();
		$code = file($backtrace[0]['file']);

		echo "<pre style='background: #eee; border: 1px solid #aaa; clear: both; overflow: auto; padding: 10px; text-align: left; margin-bottom: 5px'>";

		echo "<b>".htmlspecialchars(trim($code[$backtrace[0]['line']-1]))."</b>\n";

		echo "\n";

		ob_start();

		foreach ($args as $arg)
		var_dump($arg);

		$str = ob_get_contents();

		ob_end_clean();

		$str = preg_replace('/=>(\s+)/', ' => ', $str);
		$str = preg_replace('/ => NULL/', ' &rarr; <b style="color: #000">NULL</b>', $str);
		$str = preg_replace('/}\n(\s+)\[/', "}\n\n".'$1[', $str);
		$str = preg_replace('/ (float|int)\((\-?[\d\.]+)\)/', " <span style='color: #888'>$1</span> <b style='color: brown'>$2</b>", $str);

		$str = preg_replace('/array\((\d+)\) {\s+}\n/', "<span style='color: #888'>array&bull;$1</span> <b style='color: brown'>[]</b>", $str);
		$str = preg_replace('/ string\((\d+)\) \"(.*)\"/', " <span style='color: #888'>str&bull;$1</span> <b style='color: brown'>'$2'</b>", $str);
		$str = preg_replace('/\[\"(.+)\"\] => /', "<span style='color: purple'>'$1'</span> &rarr; ", $str);
		$str = preg_replace('/object\((\S+)\)#(\d+) \((\d+)\) {/', "<span style='color: #888'>obj&bull;$2</span> <b style='color: #0C9136'>$1[$3]</b> {", $str);
		$str = str_replace("bool(false)", "<span style='color:#888'>bool&bull;</span><span style='color: red'>false</span>", $str);
		$str = str_replace("bool(true)", "<span style='color:#888'>bool&bull;</span><span style='color: green'>true</span>", $str);

		echo $str;

		echo "</pre>";

		echo "<div class='block tiny_text' style='margin-left: 10px'>";

		echo "Sizes: ";
		foreach ($args as $k => $arg) {

			if ($k > 0) echo ",";
			echo count($arg);

		}
		echo "</div>";
	}
}
