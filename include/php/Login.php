<?php
require_once "Utilizator.php";

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
			$ok = $stmt->execute(array(
				'user' => $_SESSION['user']
			));

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
				$ok = $stmt->execute(array(
					'temp' => "0",
					'user' => $_SESSION['user']
				));

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
				$ok = $stmt->execute(array(
					'temp' =>  time(),
					'user' => $_SESSION['user']
				));

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
							$ok = $stmt->execute(array(
								'temp' => 0,
								'user' => $_POST['user']
							));

							if(!$ok) {
								throw new Exception("Ceva nu a mers cum trebuia");
							}

							$_SESSION['user']   = $_POST['user'];
							$_SESSION['parola'] = $md5pass;

							setcookie("cookuser", $_SESSION['user'], $timeToBeConnected, "/");
							setcookie("cookpass", $md5pass, $timeToBeConnected, "/");

							PAGE::showCSSLogin();
							PAGE::showConfirmation(
								'Am stabilit conexiunea cu serverul !
								<br />
								Aplicația se încarcă...
								<meta http-equiv="Refresh" content="0;url=index.php">
								');
								die();
							} else {
								throw new Exception($errorMessage);
							}
						} else {
							$whenToRetry = floor($resultExceeded / 60) . ' minute, ' . ($resultExceeded % 60) . ' secunde';

							$message   = (
								'Ați depășit numarul de încercari permise pt. autentificare.
								Puteți reîncerca după
								<br />
								<b>' . $whenToRetry . '</b>'
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
			$ok = $stmt->execute(array(
				'user' => $clientUser,
				'active' => '1'
			));

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
			if (isset($_COOKIE['cookuser']) && isset($_COOKIE['cookpass'])) {
				setcookie("cookuser", "", time() - 60 * 60 * 24 * 100, "/");
				setcookie("cookpass", "", time() - 60 * 60 * 24 * 100, "/");
			}
			if (isset($_SESSION['user'])) {
				unset($_SESSION['user']);
				unset($_SESSION['parola']);
				$_SESSION = array();
				@session_destroy();
				PAGE::showCSSLogin();
				Page::showConfirmation("Ai fost deconectat cu succes ! <br /> Redirecționare la pagina principală în 3 secunde. <meta http-equiv='Refresh' content='3;url=index.php'>");
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
			$ok = $stmt->execute(array(
				'data' => $data_expir
			));

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
			$ok = $stmt->execute(array(
				'user' => $user
			));

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

				$ok2 = $stmt2->execute(array(
					'user' => $user,
					'ip' => $ip,
					'data' => $data
				));

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
					$ok = $stmt->execute(array(
						'incercari' => $incercari,
						'data' => $data,
						'user' => $user
					));

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
