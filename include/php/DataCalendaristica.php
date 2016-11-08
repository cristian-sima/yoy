<?php

/**
 *
 * Reprezinta o data calendaristica in format ANUL-LUNA-ZIUA. Poate returna ziua, luna și anul, precum și data.
 * @author			Cristian Sima
 * @data			17.10.2014
 * @version			1.6
 *
 */
class DataCalendaristica
{
	private static $months	= array('1'=>'Ianuarie', "2"=>'Februarie', "3"=>'Martie', "4"=>'Aprilie', "5"=>'Mai', "6"=>'Iunie', "7"=>'Iulie', "8"=>'August', "9"=>'Septembrie',"10"=> 'Octombrie', "11"=>'Noiembrie',"12"=> 'Decembrie');		
	private $luna 	= null;
	private $anul 	= null;
	private $ziua 	= null;
	private $fdm	= null;
	private $ldm 	= null;

	/**
	 *
	 * Creaza o noua data calanderistica
	 * @params String $data		O data calendaristica in format ANUL-LUNA-ZIUA
	 * @throws Excpetion 		Daca data nu este in formatul ANUL-LUNA-ZIUA
	 *
	 */
	public function DataCalendaristica($data)
	{			
		if (!(preg_match("/^[0-9]{4}-([1-9]|0[1-9]|1[0-2])-([1-9]|0[1-9]|[1-2][0-9]|3[0-1])$/",$data)))
	    {	    	
	    	throw new Exception("Data trebuie sa fie in formatul ANUL-LUNA-ZIUA. A fost data data [".$data.']');	
	    }
    
		$_exp 			= explode("-", self::format($data));
		$this->anul		= intval($_exp[0]);
		$this->luna		= $_exp[1];
		$this->ziua		= $_exp[2];

		$this->fdm		= ($this->anul.'-'.$this->luna.'-01');
		$this->ldm 		= self::format((self::getZiuaPrecedenta((($this->luna=="12")?($this->anul+1):$this->anul)."-".(($this->luna == "12")?1:(intval($this->luna)+1)).'-01')));
	}

	/**
	 *
	 * Returneaza anul
	 * @return				Anul
	 *
	 */
	public function getAnul()
	{
		return $this->anul;
	}

	/**
	 *
	 * Returneaza luna
	 * @return				Luna
	 *
	 */
	public function getLuna()
	{
		return $this->luna;
	}

	/**
	 *
	 * Returneaza ziua
	 * @return				Ziua
	 *
	 */
	public function getZiua()
	{
		return $this->ziua;
	}

	/**
	 *
	 * Returneaza data in format string
	 * @return				Data
	 *
	 */
	public function __toString()
	{
		return $this->anul.'-'.$this->luna.'-'.$this->ziua;
	}

	/**
	 *
	 * Returneaza prima zi a lunii care include aceasta data
	 * @return				Prima zi din luna care include data
	 * @example				Pentru data 2014-04-09 se returneaza 2014-04-01
	 *
	 */
	public function getFirstDayOfMonth()
	{
		return $this->fdm;
	}

	/**
	 *
	 * Returneaza ultima zi a lunii care include aceasta data
	 * @return				Ultima zi din luna care include data
	 * @example				Pentru data 2014-04-09 se returneaza 2014-04-30
	 *
	 */
	public function getLastDayOfMonth()
	{
		return $this->ldm;
	}


	/**
	 *
	 * Arata data in format romanesc ZIUA-LUNA-ANUL
	 * @return				Data in format romanesc
	 *
	 */
	public function romanianFormat()
	{
		return $this->ziua.'-'.$this->luna.'-'.$this->anul;
	}


	/**
	 *
	 * Returneaza o data cu zi mai putin decat cea data
	 *
	 * @param DataCalendaristica(sau String) $data			Data pentru care se va calcula
	 * @return												Returneaza o data cu zi mai putin decat cea data
	 */
	public static function getZiuaPrecedenta($data)
	{
		$dt = strtotime($data.'');
		return date("Y-m-d", $dt-86400);
	}

	/**
	 *
	 * Returneaza o data cu zi mai mult decat cea data
	 *
	 * @param DataCalendaristica(sau String) $data			Data pentru care se va calcula
	 * @return												Returneaza o data cu zi mai mult decat cea data
	 */
	public static function getZiuaUrmatoare($data)
	{
		 return date('Y-m-d', strtotime($data .' +1 day'));
	}
	
	/**
	 * 
	 * Returneaza numele lunii in functie de numarul
	 * 
	 * @param unknown_type $luna			Un numar intre 1 și 12
	 * 
	 */
	public static function getNumeleLunii($luna)
	{
		return self::$months[intval($luna)];
	}
	
	
	/**
	 * 
	 * Returneaza o data in format ANUL-LL-ANUL. Utiliza pentru formate ANUL-L-Z
	 * 
	 * @param String $date			Data in format ANUL-L-Z
	 */
	public static function format($date)
	{
		$_exp		= explode("-", $date);
		$anul		= intval($_exp[0]);
		$luna		= ((intval($_exp[1]) < 10)?("0".(intval($_exp[1]))):(intval($_exp[1])));
		$ziua		= ((intval($_exp[2]) < 10)?("0".(intval($_exp[2]))):(intval($_exp[2])));
		
		return $anul.'-'.$luna.'-'.$ziua; 
	}
}
?>