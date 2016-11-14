<?php

require_once "Login.php";
require_once "FirmaOrganizatoare.php";

class Design {

	public static function showHeader() {
		$_temp = Aplicatie::getInstance();
		$isAccountAdministrator = Aplicatie::getInstance()->getUtilizator()->isAdministrator();

		self::showHeaderHTML(true, $isAccountAdministrator);
	}

	public static function showHeaderHTML($showMenu, $isAccountAdministrator) {
		?>
		<!DOCTYPE html>
		<html lang="ro">
		<head>
			<title>YOY.ro</title>
			<meta name="author" content="Cristian Sima"/>
			<meta name="description" content="Soft de gestiune online"/>
			<meta charset="utf-8">
			<meta http-equiv="x-ua-compatible" content="ie=edge">
			<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
			<link rel="stylesheet" href="public/css/font-awesome.min.css">
			<link rel="stylesheet" type="text/css" href="public/css/datatables.min.css"/>
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css" integrity="sha384-AysaV+vQoT3kOAXZkl02PThvDr8HYKPZhNT5h/CXfBThSRXQ6jW5DO2ekP5ViFdi" crossorigin="anonymous">
		</head>
		<body>
			<div class="container">
				<?php if($showMenu) { ?>
				<nav class="navbar navbar-light bg-faded mb-1">
					<button class="navbar-toggler hidden-lg-up" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation"></button>
					<div class="collapse navbar-toggleable-md" id="navbarResponsive">

						<?php
						if ($isAccountAdministrator) {
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
								<a class="btn btn-sm btn-secondary" href="editare_date_utilizator.php?id_user=' . Aplicatie::getInstance()->getUtilizator()->getID() . '">
								<i class="fa fa-user" aria-hidden="true"></i>
								Modifică datele
								</a>
								<button class="btn btn-sm btn-secondary" id="disconnectButton" >
								<i class="fa fa-sign-out" aria-hidden="true"></i>
								Deconectează-mă
								</button>
								';
								?>
							</form>
							<?php } else { ?>
								<a class="navbar-brand" href="#">YOY.ro</a>
								<?php } ?>
							</div>
						</nav>
						<?php } ?>
					</div>
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

				public static function showFooter() {

					$version = "";

					try {
						$string = file_get_contents("config/package.json");
						$decodedFile = json_decode($string, true);

						$version = $decodedFile["version"];

					} catch (Exception $e) {
						throw new Exception("Contactează administratorul - cod PACKAGE_JS_NOT_SET");
					}

					?>
				</div>

				<div class="container">
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
			</div>
			<script type="text/javascript" src="public/js/datatables.min.js"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.3.7/js/tether.min.js" integrity="sha384-XTs3FgkjiBgo8qjEjBk0tGmf3wPrWtA6coPfQDfFEY8AnYJwjalXCiosYRBIBZX8" crossorigin="anonymous"></script>
			<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/js/bootstrap.min.js" integrity="sha384-BLiI7JTZm+JWlgKa0M0kGRpJbF2J8q+qreVrKBC47e3K6BW78kGLrCkeRX6I9RoK" crossorigin="anonymous"></script>
			<script type="text/javascript" src="public/js/extra.js"></script>
			<script>
			$.extend(
				true,
				$.fn.dataTable.defaults, {
					oLanguage : {
						"sProcessing":   "Procesează...",
						"sLengthMenu":   "Afișează _MENU_ rânduri pe pagină",
						"sZeroRecords":  "Nu am găsit nimic - ne pare rău",
						"sInfo":         "Afișate de la _START_ la _END_ din _TOTAL_ rânduri",
						"sInfoEmpty":    "Afișate de la 0 la 0 din 0 rânduri",
						"sInfoFiltered": "(filtrate dintr-un total de _MAX_ rânduri)",
						"sInfoPostFix":  "",
						"sSearch":       "Caută:",
						"sUrl":          "",
						"oPaginate": {
							"sFirst":    "Prima",
							"sPrevious": "Precedenta",
							"sNext":     "Următoarea",
							"sLast":     "Ultima"
						}
					}
				}
			);
			</script>
		</body>
		</html>
		<?php
	}

	public static function showLoginForm() {
		self::showHeaderHTML(false, false);
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
