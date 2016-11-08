<?php

require_once "Utilizator.php";

/**
 *
 * Contine informatii despre login-ul unui utilizator la aplicatie
 * @author			Cristian Sima
 * @data			12.02.2014
 * @version			1.2
 *
 */
class Login
{
	private static $mysql				= null;
	private static $id_user				= null;
	private static $allowOperator		= false;

	/**
	 *
	 * @description 			Verifica autentificarea prin sesiune - daca utilizatorul este deja autentificat  verifica daca utilizatorul a fost memorat in cookie adaug o sesiune ca utilizatoru este logat Apeleaza functia ce verifica daca datele din sesiune sunt corecte cu cele din baza de date returneaza TRUE daca utilizatorul este autentificat.
	 * @param MYSQL $mysql 		Referinta spre obiectul cu conexiunea sql
	 * @throws Exception 		In cazul in care exista o problema la logare
	 *
	 */
	public static function request_access(MYSQL $mysql)
	{

		self::$mysql = $mysql;


		/* verifica daca utilizatorul este memorat in cookie */
		if(isset($_COOKIE['cookuser']) && isset($_COOKIE['cookpass']))
		{
			$_SESSION['user'] 		= $_COOKIE['cookuser'];
			$_SESSION['parola'] 	= $_COOKIE['cookpass'];
		}


		/* verifica daca exista in sesiune numele și parola setate */
		if(isset($_SESSION['user']) && isset($_SESSION['parola']))
		{
			/* Confirma daca numele și parola sunt valide */


			$sql1 = "SELECT _temp,tipCont from `utilizator` WHERE `user`='".$_SESSION['user']."'";
			$result2 = mysql_query($sql1, $mysql->getResource());

			/* Gaseste nr. incercari asociata user */
			while($user3 = mysql_fetch_array($result2))
			{
				$temp  = $user3['_temp'];
				$type  = $user3['tipCont'];
			}



			if($type=='normal' && $temp!=0&& ($temp < (time()-600)) )
			{
				unset($_SESSION['user']);
				unset($_SESSION['parola']);
				setcookie("cookuser", "", time()-60*60*24*100, "/");
				setcookie("cookpass", "", time()-60*60*24*100, "/");

				throw new Exception('Ai fost deconectat automat pentru că au trecut mai mult de 10 minute de la ultima acțiune. Te poți re-conecta <a href="index.php">aici</a></center>');
			}
			else
			{
				$q = "UPDATE utilizator SET _temp='0' WHERE user='".$_SESSION['user']."' LIMIT 1" ;
				$result = mysql_query($q, $mysql->getResource());
			}


			$return =  self::confirmUser($_SESSION['user'], $_SESSION['parola']);


			if($return != 0)
			{
				/* daca variabilele sunt incorecte, sterge sesiunea, utilizatorul nu este logat */
				unset($_SESSION['user']);
				unset($_SESSION['parola']);

				throw new Exception("A intervenit o problemă cu conectarea. Vă sfătuim să mai încercați încă o dată conectarea.");
			}
			else
			{
				$q = "UPDATE utilizator
					SET 	_temp='".time()."'
					WHERE 	user='".$_SESSION['user']."'
					LIMIT 1" ;
				$result = mysql_query($q, self::$mysql->getResource());
			}
		}
		else
		{
			if(isset($_POST['sublogin']) && isset($_POST['user']) && isset($_POST['pass']))
			{
				// Elimina spatiile din marginile numelui și parolei
				$_POST['user'] = trim($_POST['user']);
				// $_POST['pass'] = trim($_POST['pass']);

				/* verifica daca toate campurile au fost completate */
				if(!$_POST['user'] || !$_POST['pass']){
					$eroare='Nu ati completat toate campurile';
				}

				/* verifica lungimea numelui */
				else if(strlen($_POST['user'])<3 || strlen($_POST['user'])>32) {
					$eroare='Numele trebuie să conțină între 3 și 32 caractere';
				}

				else
				{
					// Verifica și adauga incercarea de logare in tabelul user_temp
					$continua = self::temp_user($_POST['user']);

					if ($continua=='continua')
					{

						$parola  = $_POST['pass'];
						$md5pass =  md5($_POST['pass']);		// Cripteaza parola

						/* verifica daca numele este in baza de date și parola este corecta */
						$result = self::confirmUser($_POST['user'], $md5pass);

						/* Seteaza mesajul in cazul in care user sau parola sunt incorecte */
						if($result==1) {
							$eroare='Numele <b>'. stripslashes($_POST['user']). '</b> nu este înregistrat sau contul a fost dezactivat de către administrator';
						}
						else if($result==2)
						{
							$eroare='Parola este incorectă.';
						}
						else if($result==3)
						{
							$eroare = ('<center><br /><br /><font color="red"><h3>Înregistrarea pt. <u>'. stripslashes($_POST['user']). '</u> este neconfirmată.</h3></font> Verificați contul de e-mail folosit la înregistrare (inclusiv în Spamm) pt. mesajul cu link-ul de confirmare.<br /><br /> </center>');
						}
						else
						{

							$q = "UPDATE utilizator SET _temp='0' WHERE user='".$_POST['user']."' LIMIT 1" ;
							$result = mysql_query($q, $mysql->getResource());

							/* daca numele și parola sunt corecte, inregistreaza variabilele in sesiune */
							$_SESSION['user'] = $_POST['user'];
							$_SESSION['parola'] = $md5pass;

							setcookie("cookuser", $_SESSION['user'], time()+60*60*24*100, "/");
							setcookie("cookpass", $md5pass, time()+60*60*24*100, "/");


							PAGE::showCSSLogin();
							PAGE::showConfirmation('Am stabilit conexiunea cu serverul ! <br />Aplicația se încarcă...');

							/* Auto redirect pentru a evita retrimiterea datelor cand da inapoi sau la refresh */
							echo '<meta http-equiv="Refresh" content="0;url=index.php">';
							die();
						}
					}
					else
					{
						// Seteaza mesajul cu timpul ramas pana la o noua incercare de autentificare
						$continua = floor($continua/60). ' minute, '. ($continua%60). ' secunde';
						$eroare = 'Ați depășit numarul de încercari permise pt. autentificare. Puteți reîncerca după <br /><b>'. $continua. '</b>';
					}
				}
					throw new Exception($eroare);
			}
			else
			{
				throw new Exception("Această pagină necesită conectare !");
			}
		}

	}

	/**
	 * Returneaza id-ul utilizatorului care este conectat la aplicatie
	 * @return int				ID-ul utilizatorului care este conectat la aplicatie
	 */
	public static function getUserId()
	{
		return self::$id_user;

	}

	/**
	 * Permite accesul la aplicatie petru operatori. In mod prestabilit posibilitate de accesare este doar pentru administratori
	 */
	public static function permiteOperator()
	{
		self::$allowOperator = true;
	}

	/**
	 *
	 * Verifica daca user exista in baza de date daca da, verifica daca parola se potriveste cu cea din baza de date daca numele sau parola nu sunt corecte, returneaza eroarea (1 sau 2). pentru nume și parola confirmate returneaza 0.
	 *
	 * @param String $user			Utilizatorul care va fi verificat
	 * @param String $parola		Parola utilizatorului criptata md5
	 * @throws  Exception			Utilizator care nu se afla in baza de date sau nu este activ
	 * @throws  Exception			Parola incorecta
	 * @return						ID-ul utilizatorului
	 *
	 */
	private static function confirmUser($user, $parola)
	{

		/* Adauga slashuri daca este necesar (pentru query) */
		if(!get_magic_quotes_gpc())
		{
			$user = addslashes($user);
			$parola = addslashes($parola);
		}


		/* Verifica daca numle este in baza de date */
		$mysql_ = "	SELECT parola,id
				FROM `utilizator`
				WHERE `user`='$user' AND activ='1' ".((self::$allowOperator)?(""):("AND tipCont = 'admin'"))."
				LIMIT 1";

		$result = mysql_query($mysql_, self::$mysql->getResource());


		if(!$result || (mysql_num_rows($result)<1))
		{
			return 1;
		}
		else
		{
			/* Gaseste parola asociata numelui */
			$dbarray 					= mysql_fetch_array($result);
			$parola_db  				= stripslashes($dbarray['parola']);
			$id_db				  		= stripslashes($dbarray['id']);

			self::$id_user 				= $id_db;

			/* Verifica daca parola scrisa este aceeasi cu cea gasita in baza de date */
			if($parola == $parola_db)
			{
				return 0;
			}
			else
			{
				return 2;
			}
		}
	}

	/**
	 * Șterge cook-urile, datele de sesiune și implicit deconecteaza utilizatorul conectat
	 */
	public static function disconnect()
	{
		if(isset($_COOKIE['cookuser']) && isset($_COOKIE['cookpass']))
		{
			setcookie("cookuser", "", time()-60*60*24*100, "/");
			setcookie("cookpass", "", time()-60*60*24*100, "/");
		}

		if(isset($_SESSION['user']))
		{
			/* Șterge sesiunea */
			unset($_SESSION['user']);
			unset($_SESSION['parola']);
			$_SESSION = array(); // reseteaza matricea sesiunii
			@session_destroy();   // sterge toate sesiunile.

			PAGE::showCSSLogin();
			Page::showConfirmation("Ai fost deconectat cu succes ! <br /> Redirecționare la pagina principală în 3 secunde. <meta http-equiv='Refresh' content='3;url=index.php'>");
		}
		else
		{
			echo " <meta http-equiv='Refresh' content='0;url=index.php'>";
		}
	}

	/**
	 *
	 * 	Urmatoarea functie sterge randurile din tabelul user_temp mai vechi de 10 min. verifica daca utilizatorul a incercat de mai multe ori autentificarea daca a incercat deja de 3 ori fara nume-parola confirmate solicita incercarea unei noi autentificari dupa 10 min
	 * @param string $user					Utilizatorul care va fi testat [@String]
	 *
	 */
	private static function temp_user($user)
	{
		$data = time();
		$data_expir = $data-601;
		$ip = $_SERVER['REMOTE_ADDR'];

		/* Șterge randurile din tabelul user_temp mai vechi de 10 min */
		$sql = "DELETE FROM `user_temp`
					WHERE `data`<$data_expir";
		mysql_query($sql, self::$mysql->getResource());

		/* Adauga slashuri (pentru nume), daca PHP nu e setat sa adauge implicit */
		if(!get_magic_quotes_gpc())
		{
			$user = addslashes($user);
		}

		/* Verifica daca numle este in tabelul user_temp */
		$sql1 = "SELECT `user`,`incercari`,`data` FROM `user_temp` WHERE `user`='$user'";
		$result = mysql_query($sql1,self::$mysql->getResource());


		if(!$result || (mysql_numrows($result)<1))
		{
			$sql1 = "INSERT INTO `user_temp` (user, ip, data) VALUES ('$user', '$ip', '$data')";
			mysql_query($sql1, self::$mysql->getResource());
			return 'continua';
		}
		else
		{
			/* Gaseste nr. incercari asociata user */
			$tbarray = mysql_fetch_array($result);
			$incercari = $tbarray['incercari'];
			if ($incercari<5)
			{
				$incercari++;
				// incrementeaza nr. incercari cu 1 și actualizeaza data
				$sql1 = "UPDATE `user_temp` SET `incercari`='$incercari', `data`='$data' WHERE `user`='$user'";
				$result = mysql_query($sql1, self::$mysql->getResource());
				return 'continua';
			}
			else if ($incercari>=3)
			{
				$timp = 600 - ($data - $tbarray['data']);
				return $timp;		// Indica nr. incercari depasit și returneaza timpul pt. calculare asteptare
			}
		}
	}

	/**
	 *
	 * Returneaza utilizatorul care este conectat
	 * @return Utilizator 				Utilizatorul conectat la aplicatie
	 *
	 */
	public function getUtilizator()
	{
		return $this->utilizator;
	}
}
