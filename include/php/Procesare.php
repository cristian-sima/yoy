<?php

/**
 *
 * Contine functiile pentru procesarea datelor
 * @author			Cristian Sima
 * @data			27.01.2014
 * @version			1.0
 *
 */
class Procesare
{

	/**
	 *
	 * Functia verifica ca toti parametrii pentru un array au fost completati. In cazul in care exista o problema se arunca o exceptie
	 *
	 * @param array $args					Matricea cu parametrii care se vor verifica
	 * @param array $type					Matricea cu valoriile unde se vor verifica
	 * @param string $address				Adresa la care se va redirectiona duce in cazul in care apare o problema
	 * @throws Exception 					In cazul in care un camp nu a fost completat un camp
	 */
	public static function checkRequestedData($args, $array, $address)
	{
		foreach($args as $arg)
		{
			if(!isset($array[$arg]))
			{
				throw new Exception("Câmpul ".$arg." nu a fost completat ! <a href='".$address."'>Înapoi</a>");
			}
		}
	}

	/**
	 * Functia creeaza campuri cu valori "" in cazul in care nu exista valorile in matrice
	 *
	 * @param array $data				Matricea care contine valorile		
	 * @param array $toCheck			Matricea cu valorile care se verifica
	 */
	public static function createEmptyFields(&$data, $toCheck)
	{
		foreach($toCheck as $field)
		{
			if(!isset($data[$field]))
			{
				$data[$field]='';
			}
		}
	}
}
