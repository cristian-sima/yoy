<?php
require_once "Utilizator.php";

class Login {
	private static $mysql = null;
	private static $id_user = null;
	private static $allowOperator = false;
	public static function request_access(MYSQL $mysql) {
		self::$mysql = $mysql;
		if (isset($_COOKIE['cookuser']) && isset($_COOKIE['cookpass'])) {
			$_SESSION['user']   = $_COOKIE['cookuser'];
			$_SESSION['parola'] = $_COOKIE['cookpass'];
		}
		if (isset($_SESSION['user']) && isset($_SESSION['parola'])) {

			$db = $mysql->getResource();

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

			if ($type == 'normal' && $temp != 0 && ($temp < (time() - 600))) {
				unset($_SESSION['user']);
				unset($_SESSION['parola']);
				setcookie("cookuser", "", time() - 60 * 60 * 24 * 100, "/");
				setcookie("cookpass", "", time() - 60 * 60 * 24 * 100, "/");
				throw new Exception('Ai fost deconectat automat pentru că au trecut mai mult de 10 minute de la ultima acțiune. Te poți re-conecta <a href="index.php">aici</a></center>');
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
			$return = self::confirmUser($_SESSION['user'], $_SESSION['parola']);
			if ($return != 0) {
				unset($_SESSION['user']);
				unset($_SESSION['parola']);
				throw new Exception("A intervenit o problemă cu conectarea. Vă sfătuim să mai încercați încă o dată conectarea.");
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
			}
		} else {
			if (isset($_POST['sublogin']) && isset($_POST['user']) && isset($_POST['pass'])) {
				$_POST['user'] = trim($_POST['user']);
				if (!$_POST['user'] || !$_POST['pass']) {
					$eroare = 'Nu ati completat toate campurile';
				} else if (strlen($_POST['user']) < 3 || strlen($_POST['user']) > 32) {
					$eroare = 'Numele trebuie să conțină între 3 și 32 caractere';
				} else {
					$continua = self::temp_user($_POST['user']);
					if ($continua == 'continua') {
						$parola  = $_POST['pass'];
						$md5pass = md5($_POST['pass']);
						$result  = self::confirmUser($_POST['user'], $md5pass);
						if ($result == 1) {
							$eroare = 'Numele <b>' . stripslashes($_POST['user']) . '</b> nu este înregistrat sau contul a fost dezactivat de către administrator';
						} else if ($result == 2) {
							$eroare = 'Parola este incorectă.';
						} else if ($result == 3) {
							$eroare = ('<center><br /><br /><font color="red"><h3>Înregistrarea pt. <u>' . stripslashes($_POST['user']) . '</u> este neconfirmată.</h3></font> Verificați contul de e-mail folosit la înregistrare (inclusiv în Spamm) pt. mesajul cu link-ul de confirmare.<br /><br /> </center>');
						} else {

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
							setcookie("cookuser", $_SESSION['user'], time() + 60 * 60 * 24 * 100, "/");
							setcookie("cookpass", $md5pass, time() + 60 * 60 * 24 * 100, "/");
							PAGE::showCSSLogin();
							PAGE::showConfirmation('Am stabilit conexiunea cu serverul ! <br />Aplicația se încarcă...');
							echo '<meta http-equiv="Refresh" content="0;url=index.php">';
							die();
						}
					} else {
						$continua = floor($continua / 60) . ' minute, ' . ($continua % 60) . ' secunde';
						$eroare   = 'Ați depășit numarul de încercari permise pt. autentificare. Puteți reîncerca după <br /><b>' . $continua . '</b>';
					}
				}
				throw new Exception($eroare);
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

		$db = self::$mysql->getResource();

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
			return 1;
		}

		foreach($stmt as $row) {
			$currentPassword = $row['parola'];
			$currentID = $row["id"];
			$currentType = $row["tipCont"];

			// check the type of user
			if (!self::$allowOperator && $currentType !== "admin") {
				return 1;
			}

			if ($clientPassword == $currentPassword) {
				return 0;
			}

			// save id
			self::$id_user = $currentID;

			return 2;
		}

		return 1;
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
	private static function temp_user($user) {
		$data       = time();
		$data_expir = $data - 601;
		$ip         = $_SERVER['REMOTE_ADDR'];

		$db = self::$mysql->getResource();

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
			WHERE user <:user"
		);

		$stmt = $db->prepare($query);
		$ok = $stmt->execute(array(
			'user' => $user
		));

		if(!$ok) {
			throw new Exception("Ceva nu a mers cum trebuia");
		}

		$affectedRows = $stmt->rowCount();

		if ($affectedRows == 0) {

			$query = (
				"INSERT INTO `user_temp` (user, ip, data)
				VALUES (:user, :ip, :data)
				"
			);

			$stmt = $db->prepare($query);
			$ok = $stmt->execute(array(
				'user' => $user,
				'ip' => $ip,
				'data' => $data
			));

			if(!$ok) {
				throw new Exception("Ceva nu a mers cum trebuia");
			}

			return 'continua';
		} else {
			$tbarray   = mysql_fetch_array($result);
			$incercari = $tbarray['incercari'];
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

				return 'continua';
			} else if ($incercari >= 3) {
				$timp = 600 - ($data - $tbarray['data']);

				return $timp;
			}
		}
	}
	public function getUtilizator() {
		return $this->utilizator;
	}
}
