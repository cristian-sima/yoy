<?php
require_once "Utilizator.php";

function showLoading () {
	DESIGN::showHeaderHTML();
	echo '
	<body>
	<div class="text-xs-center mt-3">
	<svg width="64px" height="64px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="uil-gears"><rect x="0" y="0" width="100" height="100" fill="none" class="bk"></rect><g transform="translate(-20,-20)"><path d="M79.9,52.6C80,51.8,80,50.9,80,50s0-1.8-0.1-2.6l-5.1-0.4c-0.3-2.4-0.9-4.6-1.8-6.7l4.2-2.9c-0.7-1.6-1.6-3.1-2.6-4.5 L70,35c-1.4-1.9-3.1-3.5-4.9-4.9l2.2-4.6c-1.4-1-2.9-1.9-4.5-2.6L59.8,27c-2.1-0.9-4.4-1.5-6.7-1.8l-0.4-5.1C51.8,20,50.9,20,50,20 s-1.8,0-2.6,0.1l-0.4,5.1c-2.4,0.3-4.6,0.9-6.7,1.8l-2.9-4.1c-1.6,0.7-3.1,1.6-4.5,2.6l2.1,4.6c-1.9,1.4-3.5,3.1-5,4.9l-4.5-2.1 c-1,1.4-1.9,2.9-2.6,4.5l4.1,2.9c-0.9,2.1-1.5,4.4-1.8,6.8l-5,0.4C20,48.2,20,49.1,20,50s0,1.8,0.1,2.6l5,0.4 c0.3,2.4,0.9,4.7,1.8,6.8l-4.1,2.9c0.7,1.6,1.6,3.1,2.6,4.5l4.5-2.1c1.4,1.9,3.1,3.5,5,4.9l-2.1,4.6c1.4,1,2.9,1.9,4.5,2.6l2.9-4.1 c2.1,0.9,4.4,1.5,6.7,1.8l0.4,5.1C48.2,80,49.1,80,50,80s1.8,0,2.6-0.1l0.4-5.1c2.3-0.3,4.6-0.9,6.7-1.8l2.9,4.2 c1.6-0.7,3.1-1.6,4.5-2.6L65,69.9c1.9-1.4,3.5-3,4.9-4.9l4.6,2.2c1-1.4,1.9-2.9,2.6-4.5L73,59.8c0.9-2.1,1.5-4.4,1.8-6.7L79.9,52.6 z M50,65c-8.3,0-15-6.7-15-15c0-8.3,6.7-15,15-15s15,6.7,15,15C65,58.3,58.3,65,50,65z" fill="#cec9c9" transform="rotate(62.7937 50 50)"><animateTransform attributeName="transform" type="rotate" from="90 50 50" to="0 50 50" dur="1s" repeatCount="indefinite"></animateTransform></path></g><g transform="translate(20,20) rotate(15 50 50)"><path d="M79.9,52.6C80,51.8,80,50.9,80,50s0-1.8-0.1-2.6l-5.1-0.4c-0.3-2.4-0.9-4.6-1.8-6.7l4.2-2.9c-0.7-1.6-1.6-3.1-2.6-4.5 L70,35c-1.4-1.9-3.1-3.5-4.9-4.9l2.2-4.6c-1.4-1-2.9-1.9-4.5-2.6L59.8,27c-2.1-0.9-4.4-1.5-6.7-1.8l-0.4-5.1C51.8,20,50.9,20,50,20 s-1.8,0-2.6,0.1l-0.4,5.1c-2.4,0.3-4.6,0.9-6.7,1.8l-2.9-4.1c-1.6,0.7-3.1,1.6-4.5,2.6l2.1,4.6c-1.9,1.4-3.5,3.1-5,4.9l-4.5-2.1 c-1,1.4-1.9,2.9-2.6,4.5l4.1,2.9c-0.9,2.1-1.5,4.4-1.8,6.8l-5,0.4C20,48.2,20,49.1,20,50s0,1.8,0.1,2.6l5,0.4 c0.3,2.4,0.9,4.7,1.8,6.8l-4.1,2.9c0.7,1.6,1.6,3.1,2.6,4.5l4.5-2.1c1.4,1.9,3.1,3.5,5,4.9l-2.1,4.6c1.4,1,2.9,1.9,4.5,2.6l2.9-4.1 c2.1,0.9,4.4,1.5,6.7,1.8l0.4,5.1C48.2,80,49.1,80,50,80s1.8,0,2.6-0.1l0.4-5.1c2.3-0.3,4.6-0.9,6.7-1.8l2.9,4.2 c1.6-0.7,3.1-1.6,4.5-2.6L65,69.9c1.9-1.4,3.5-3,4.9-4.9l4.6,2.2c1-1.4,1.9-2.9,2.6-4.5L73,59.8c0.9-2.1,1.5-4.4,1.8-6.7L79.9,52.6 z M50,65c-8.3,0-15-6.7-15-15c0-8.3,6.7-15,15-15s15,6.7,15,15C65,58.3,58.3,65,50,65z" fill="#3c302e" transform="rotate(27.2063 50 50)"><animateTransform attributeName="transform" type="rotate" from="0 50 50" to="90 50 50" dur="1s" repeatCount="indefinite"></animateTransform></path></g></svg>
	</div>
	</body>
	</html>
	';
}

class Login {
	private static $db = null;
	private static $id_user = null;
	private static $allowOperator = false;
	public static function request_access(PDO $db) {
		self::$db = $db;

		/* verifica daca utilizatorul este memorat in cookie */
		if(isset($_COOKIE['cookuser']) && isset($_COOKIE['cookpass'])) {
			$_SESSION['user'] 		= $_COOKIE['cookuser'];
			$_SESSION['parola'] 	= $_COOKIE['cookpass'];
		}

		$isConnected = (
			isset($_SESSION['user']) &&
			isset($_SESSION['parola'])
		);

		if ($isConnected) {

			// check the valability of the connection

			$query = (
				"SELECT _temp, tipCont
				FROM `utilizator`
				WHERE `user`= :user"
			);

			$stmt = $db->prepare($query);
			$ok = $stmt->execute([
				'user' => $_SESSION['user']
			]);

			if(!$ok) {
				throw new Exception("Ceva nu a mers cum trebuia");
			}

			foreach($stmt as $row) {
				$temp = $row['_temp'];
				$type = $row['tipCont'];
			}

			$isOperator = $type == 'normal';

			// check if the operator did not exceded the max time allowed

			if ($isOperator) {

				// 10 min
				$maxAllowdTime = (
					60 * 10
				);

				$hasExcededMaxTime = (
					$temp != 0 &&
					$temp < (time() - $maxAllowdTime)
				);

				function deconnectOperator () {
					unset($_SESSION['user']);
					unset($_SESSION['parola']);

					setcookie("cookuser", "", time() - 60 * 60 * 24 * 100, "/");
					setcookie("cookpass", "", time() - 60 * 60 * 24 * 100, "/");

					throw new Exception(
						'Ai fost deconectat automat pentru că au trecut mai mult de 10 minute de la ultima acțiune. Te poți re-conecta oricând <a href="index.php">aici</a></center>'
					);
				}

				if ($hasExcededMaxTime) {
					deconnectOperator();
				}
			} else {

				$query = (
					"UPDATE utilizator
					SET _temp=:temp
					WHERE user=:user
					LIMIT 1"
				);

				$stmt = $db->prepare($query);
				$ok = $stmt->execute([
					'temp' => "0",
					'user' => $_SESSION['user']
				]);

				if(!$ok) {
					throw new Exception("Ceva nu a mers cum trebuia");
				}
			}

			$isConfirmed = self::confirmUser($_SESSION['user'], $_SESSION['parola']);

			if (!$isConfirmed) {
				unset($_SESSION['user']);
				unset($_SESSION['parola']);


				throw new Exception("Datele furnizate nu vă pot conecta");
			} else {

				$query = (
					"UPDATE utilizator
					SET _temp=:temp
					WHERE user=:user
					LIMIT 1"
				);

				$stmt = $db->prepare($query);
				$ok = $stmt->execute([
					'temp' =>  time(),
					'user' => $_SESSION['user']
				]);

				if(!$ok) {
					throw new Exception("Ceva nu a mers cum trebuia");
				}

				return self::$id_user;
			}
		} else {

			$isUserTrying = (
				isset($_POST['sublogin']) &&
				isset($_POST['user']) &&
				isset($_POST['pass'])
			);

			if ($isUserTrying) {

				$errorMessage = "Datele furnizate nu vă pot conecta";

				$_POST['user'] = trim($_POST['user']);

				$ok = (
					strlen($_POST['user']) > 3 &&
					strlen($_POST['user']) < 32 &&
					strlen($_POST['pass']) > 2 &&
					strlen($_POST['pass']) < 32
				);

				if ($ok) {

					$resultExceeded = self::hasUserExceededNrAllowedTries($_POST['user']);

					if ($resultExceeded == 'NOT_EXCEEDED') {

						$parola  = $_POST['pass'];
						$md5pass = md5($_POST['pass']);
						$isConfirmed  = self::confirmUser($_POST['user'], $md5pass);

						if ($isConfirmed) {

							$timeToBeConnected = time() + 60 * 60 * 24 * 100;

							$query = (
								"UPDATE utilizator
								SET _temp=:temp
								WHERE user=:user
								LIMIT 1"
							);

							$stmt = $db->prepare($query);
							$ok = $stmt->execute([
								'temp' => 0,
								'user' => $_POST['user']
							]);

							if(!$ok) {
								throw new Exception("Ceva nu a mers cum trebuia");
							}

							$_SESSION['user']   = $_POST['user'];
							$_SESSION['parola'] = $md5pass;

							setcookie("cookuser", $_SESSION['user'], $timeToBeConnected, "/");
							setcookie("cookpass", $md5pass, $timeToBeConnected, "/");

							showLoading();
							echo '
							<meta http-equiv="Refresh" content="0;url=index.php">
							';
							die();
						} else {
							throw new Exception($errorMessage);
						}
					} else {
						$whenToRetry = floor($resultExceeded / 60) . ' minute, ' . ($resultExceeded % 60) . ' secunde';

						$message   = (
							'Ați depășit numarul de încercari permise pt. autentificare.
							Puteți reîncerca după <strong>' . $whenToRetry . '</strong>'
						);

						throw new Exception($message);
					}
				} else {
					throw new Exception($errorMessage);
				}
			} else {
				throw new Exception("Această pagină necesită conectare !");
			}
		}
	}
	public static function getUserId() {
		return self::$id_user;
	}
	public static function permiteOperator() {
		self::$allowOperator = true;
	}
	private static function confirmUser($clientUser, $clientPassword) {

		$db = self::$db;

		$query = (
			"SELECT parola, id, tipCont
			FROM utilizator
			WHERE user=:user AND activ=:active
			LIMIT 1"
		);

		$stmt = $db->prepare($query);
		$ok = $stmt->execute([
			'user' => $clientUser,
			'active' => '1'
		]);

		if(!$ok) {
			return false;
		}

		$nrOfResults = $stmt->rowCount();

		if($nrOfResults == 0) {
			return false;
		}

		foreach($stmt as $row) {
			$currentPassword = $row['parola'];
			$currentType = $row["tipCont"];
			$currentID = $row["id"];

			self::$id_user = $currentID;

			// check the type of user
			if (!self::$allowOperator && $currentType == "normal") {
				return false;
			}

			if ($clientPassword == $currentPassword) {
				return true;
			}

			return false;
		}

		return userHasProblems;
	}
	public static function disconnect() {
		showLoading();
		if (isset($_COOKIE['cookuser']) && isset($_COOKIE['cookpass'])) {
			setcookie("cookuser", "", time() - 60 * 60 * 24 * 100, "/");
			setcookie("cookpass", "", time() - 60 * 60 * 24 * 100, "/");
		}
		if (isset($_SESSION['user'])) {
			unset($_SESSION['user']);
			unset($_SESSION['parola']);
			$_SESSION = array();
			@session_destroy();
		} else {
			echo " <meta http-equiv='Refresh' content='0;url=index.php'>";
		}
	}
	private static function hasUserExceededNrAllowedTries($user) {
		$data       = time();
		$data_expir = $data - 601;
		$ip         = $_SERVER['REMOTE_ADDR'];

		$db = self::$db;

		// delete old tokens

		$query = (
			"DELETE from user_temp
			WHERE data <:data"
		);

		$stmt = $db->prepare($query);
		$ok = $stmt->execute([
			'data' => $data_expir
		]);

		if(!$ok) {
			throw new Exception("Ceva nu a mers cum trebuia");
		}

		// add the new try

		$query = (
			"SELECT `user`, `incercari`, `data`
			FROM user_temp
			WHERE user=:user"
		);

		$stmt = $db->prepare($query);
		$ok = $stmt->execute([
			'user' => $user
		]);

		if(!$ok) {
			throw new Exception("Ceva nu a mers cum trebuia");
		}

		$nrOfRows = $stmt->rowCount();

		if ($nrOfRows == 0) {

			$query2 = (
				"INSERT INTO `user_temp` (user, ip, data)
				VALUES (:user, :ip, :data)"
			);

			$stmt2 = $db->prepare($query2);

			$ok2 = $stmt2->execute([
				'user' => $user,
				'ip' => $ip,
				'data' => $data
			]);

			if(!$ok2) {
				throw new Exception("Ceva nu a mers cum trebuia");
			}

			return 'NOT_EXCEEDED';
		} else {

			$incercari = 5;
			$lastDate = 0;

			foreach($stmt as $row) {
				$incercari = $row['incercari'];
				$lastDate = $row["data"];
			}

			if ($incercari < 5) {
				$incercari++;

				$query = (
					"UPDATE user_temp
					SET incercari=:incercari, `data`=:data
					WHERE `user`=:user
					"
				);

				$stmt = $db->prepare($query);
				$ok = $stmt->execute([
					'incercari' => $incercari,
					'data' => $data,
					'user' => $user
				]);

				if(!$ok) {
					throw new Exception("Ceva nu a mers cum trebuia");
				}

				return 'NOT_EXCEEDED';
			} else if ($incercari >= 3) {
				$timp = 600 - ($data - $lastDate);

				return $timp;
			}
		}
	}
	public function getUtilizator() {
		return $this->utilizator;
	}
}
