<?php
	/**
	 * Библиотека разнообразных функций.
	 * 
	 * Данная библиотека содержит функции которые не удалось с чем-либо логически объединить 
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @version 1.0
	 * @package voltcore
	 * @subpackage libs
	 */

	/**
	 * Функция перекодирует строки из кодировки $from в кодировку $to.
	 * 
	 * Функция аналогична стандартной функции iconv, но позволяет перекодировать не только строки, но и массивы строк.
	 * 
	 * @param string $from Из какой кодировки 
	 * @param string $to В какую кодировку
	 * @param mixed $sbj Что перекодируем 
	 * @return mixed Результат перекодирования
	 */
	function deepIconv($from, $to, $sbj){
		if (is_array($sbj) || is_object($sbj)){
			foreach ($sbj as &$val){ 
				$val= deepIconv($from, $to, $val);
			}
			return $sbj;
		}else{
			return iconv($from, $to, $sbj);
		}
	}

?>