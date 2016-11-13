<?php

require_once "Login.php";
require_once "FirmaOrganizatoare.php";

class Page {

	public static function showHeader() {
		$_temp = Aplicatie::getInstance();
		self::showHeaderHTML();
	}

	public static function showHeaderHTML() {
		echo '	<!DOCTYPE>
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta charset="utf-8">
		<title>YOY.ro</title>
		<meta name="description" content="Soft de gestiune online"/>
		<meta name="author" content="Cristian Sima"/>
		<link href="include/css/jquery-ui-1.10.3.custom.min.css" rel="stylesheet" type="text/css"/>
		<link href="include/css/menu.css" rel="stylesheet" type="text/css"/>
		<link href="include/css/print.css" rel="stylesheet" type="text/css" media="print" />
		<link href="include/css/t.css" rel="stylesheet" type="text/css"/>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css" integrity="sha384-AysaV+vQoT3kOAXZkl02PThvDr8HYKPZhNT5h/CXfBThSRXQ6jW5DO2ekP5ViFdi" crossorigin="anonymous">
		<script type="text/javascript" src="include/js/jquery-1.9.1.js" ></script>
		<script type="text/javascript" src="include/js/jquery-ui-1.10.3.custom.min.js" ></script>
		<script type="text/javascript" src="include/js/jquery.dataTables.js" ></script>
		<script type="text/javascript" src="include/js/extra.js" ></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/js/bootstrap.min.js" integrity="sha384-BLiI7JTZm+JWlgKa0M0kGRpJbF2J8q+qreVrKBC47e3K6BW78kGLrCkeRX6I9RoK" crossorigin="anonymous"></script>
		';
	}


	private static function showMeniu() {

		?>

		<nav class="navbar navbar-light bg-faded mb-1">
			<?php
				if (Aplicatie::getInstance()->getUtilizator()->isAdministrator()) {
			?>
			<ul class="nav navbar-nav">
				<li class="nav-item">
					<a class="nav-link" href="space_companies.php">Firme</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="utilizatori.php">Utilizatori</a>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="toate_aparatele.php" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Aparate</a>
					<div class="dropdown-menu" aria-labelledby="supportedContentDropdown">
						<a class="dropdown-item" href="toate_aparatele.php">Toate aparatele</a>
						<a class="dropdown-item" href="aparate_din_depozit.php">Depozit</a>
					</div>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="selecteaza_situatie.php">Situații</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="actiuni.php">Acțiuni</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="vizualizare_dispozitii.php">Dispoziții</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="depuneri.php">Depuneri</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="setari.php">Setări</a>
				</li>
			</ul>
			<form class="form-inline float-xs-right">
				<?php
				echo '
				<button type="button" class="btn btn-sm btn-secondary-outline" onclick="document.location=' . "'" . 'editare_date_utilizator.php?id_user=' . Aplicatie::getInstance()->getUtilizator()->getID() . "'" . '">
					Modifică datele
				</button>
				<button type="button" class="btn btn-sm btn-secondary-outline" id="disconnectButton" >
					Deconectează-mă
				</button>
				';
				?>
			</form>
			<?php } else { ?>
				<a class="navbar-brand" href="#">YOY.ro</a>
			<?php } ?>
		</nav>
		<?php
	}

	public static function showHeading($title, $right_side) {
		?>
		<div class="container">
			<div class="row">
				<div class="col-xs-8">
					<h1><?php echo $title; ?> </h1>
				</div>
				<div class="col-xs-4 text-xs-right">
					<?php echo $right_side; ?>
				</div>
			</div>
		</div>
		<?php
	}

	public static function showContent() {
		echo '
		</head>
		<body style="min-width:1000px">
		<div id="container"><div class="container">';
		self::showMeniu();
		echo '
			';
	}

	public static function showFooter() {

		$version = "";

		try {
			$string = file_get_contents("package.json");
			$decodedFile = json_decode($string, true);

			$version = $decodedFile["version"];

		} catch (Exception $e) {
			throw new Exception("Contactează administratorul - cod PACKAGE_JS_NOT_SET");
		}

		?>
	</div>

	<div class="hidden-print container-fluid mt-3">
		<hr>
		<div class="row">
			<div class="col-xs-6">
				&copy; YOY.ro <?php echo date('Y'); ?>
			</div>
			<div class="col-xs-6 text-xs-right">
				<small> V<?php echo $version; ?></small>
			</div>
		</div>
	</div>

</div>
</body>
</html>
<?php
}

public static function showLoginForm() {
	self::showHeaderHTML();
	?>
	<body>
		<div class="container">
			<form class="form-signin" action="request_access.php" method="POST">
				<h2 class="form-signin-heading">Te rog să te conectezi</h2>
				<label for="user" class="sr-only">Cont de utilizator</label>
				<input type="text" id="user" name="user" class="form-control" placeholder="Cont de utilizator" required autofocus size="7" maxlength="30" >
				<label for="pass" class="sr-only">Parolă</label>
				<input type="password" id="pass" name="pass" class="form-control" placeholder="Parolă" required size="7" maxlength="30" >
				<div class="checkbox">
					<label>
						<input type="checkbox" name="remember"> Ține-mă minte
					</label>
				</div>
				<input class="btn btn-lg btn-primary btn-block" type="submit" onclick="beforeSubmit()" name="sublogin" value="Conectează-mă" />
			</form>
		</div>
	</div>
	<?php
}

public static function showConfirmation($message) {
	echo '<div class="alert alert-success">' . $message . '</div>';
}

public static function complain($message) {
	self::showError($message);
	die();
}

public static function showError($message) {
	echo '<div class="alert alert-warning">' . $message . '</div>';
}

// public static function representVisual($obj) {
// 	$args      = func_get_args();
// 	$backtrace = debug_backtrace();
// 	$code      = file($backtrace[0]['file']);
// 	echo "<pre style='background: #eee; border: 1px solid #aaa; clear: both; overflow: auto; padding: 10px; text-align: left; margin-bottom: 5px'>";
// 	echo "<b>" . htmlspecialchars(trim($code[$backtrace[0]['line'] - 1])) . "</b>\n";
// 	echo "\n";
// 	ob_start();
// 	foreach ($args as $arg)
// 	var_dump($arg);
// 	$str = ob_get_contents();
// 	ob_end_clean();
// 	$str = preg_replace('/=>(\s+)/', ' => ', $str);
// 	$str = preg_replace('/ => NULL/', ' &rarr; <b style="color: #000">NULL</b>', $str);
// 	$str = preg_replace('/}\n(\s+)\[/', "}\n\n" . '$1[', $str);
// 	$str = preg_replace('/ (float|int)\((\-?[\d\.]+)\)/', " <span style='color: #888'>$1</span> <b style='color: brown'>$2</b>", $str);
// 	$str = preg_replace('/array\((\d+)\) {\s+}\n/', "<span style='color: #888'>array&bull;$1</span> <b style='color: brown'>[]</b>", $str);
// 	$str = preg_replace('/ string\((\d+)\) \"(.*)\"/', " <span style='color: #888'>str&bull;$1</span> <b style='color: brown'>'$2'</b>", $str);
// 	$str = preg_replace('/\[\"(.+)\"\] => /', "<span style='color: purple'>'$1'</span> &rarr; ", $str);
// 	$str = preg_replace('/object\((\S+)\)#(\d+) \((\d+)\) {/', "<span style='color: #888'>obj&bull;$2</span> <b style='color: #0C9136'>$1[$3]</b> {", $str);
// 		$str = str_replace("bool(false)", "<span style='color:#888'>bool&bull;</span><span style='color: red'>false</span>", $str);
// 		$str = str_replace("bool(true)", "<span style='color:#888'>bool&bull;</span><span style='color: green'>true</span>", $str);
// 		echo $str;
// 		echo "</pre>";
// 		echo "<div class='block tiny_text' style='margin-left: 10px'>";
// 		echo "Sizes: ";
// 		foreach ($args as $k => $arg) {
// 			if ($k > 0)
// 			echo ",";
// 			echo count($arg);
// 		}
// 		echo "</div>";
// 	}
}
