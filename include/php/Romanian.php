<?php

	require_once "include/php/Total.php";
	
/**
 *
 * Contine toate informatiile privind tipul de afisare a datii, a banilor
 * @author			Cristian Sima
 * @data			12.02.2014
 * @version			1.1
 *
 */
abstract class Romanian
{	



	/**
	 * 
	 * Formateaza o suma de bani in stil romananesc
	 *
	 * @param float $money				Suma de bani neformatata
	 * @return string					Suma de bani formatata
	 * 
	 */
	public static function currency($money)
	{
		return number_format($money, 2, ',', '.').' lei';
	}
	
}