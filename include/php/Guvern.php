<?php

	require_once "DataCalendaristica.php";

	/**
	 *	Reprezinta obiectul cu toate taxele și darile spre stat
	 *
	 *  @author					Cristian Sima
	 *  @date					10.02.2014
	 *  @version				1.1
	 *
	 */
	class Guvern
	{
		/**
		 *
		 * Returneaza taxa de autorizare pentru un aparat, impusa de guvern in luna pentru care se ofera data
		 * @param DataCalendaristica $data		O zi calendaristica de tip DataCalendaristica
		 * @return								Taxa de autorizare pentru un aparat
		 *
		 */
		public static function getTaxaDeAutorizareAparat(DataCalendaristica $data)
		{
			return (self::_doWork("aparat", $data));
		}


		/**
		 *
		 * Returneaza o anumita valoare din baza de date sau opreste executia programuui daca nu exista accea taxa stabilita
		 * @param string $type					aparat, suma, procent
		 * @param DataCalendaristica $data		Data calendaristica
		 * @return 								Depinde de parametrii
		 *
		 */
		private static function _doWork($type, DataCalendaristica $data)
		{

			$valoare 	= 0;
			$exist 		= false;


			$A = "SELECT valoare from taxa WHERE tip='".$type."' AND ( ( isNow='0' AND _from>='".$data->getFirstDayOfMonth()."' AND _to <= '".$data->getLastDayOfMonth()."') OR ( isNow='1' AND '".$data->getFirstDayOfMonth()."'>=_from )) LIMIT 1";


			$result = mysql_query($A, Aplicatie::getInstance()->getMYSQL()) or die(mysql_error());
			while($taxa = mysql_fetch_array($result))
			{
				$valoare = $taxa['valoare'];
				$exist = true;
			}

			if(!$exist)
			{
				die("Nu există o valoare stabilită pentru [#".$type."#] pentru data: ".$data);
			}
			return $valoare;
		}
	}
