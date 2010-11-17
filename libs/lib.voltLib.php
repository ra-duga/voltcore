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
	 * @category VoltCore
	 * @package VoltCoreFiles
	 * @subpackage Libs
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
	
	/**
	 * Выполняет ksort для подмассивов.
	 * 
	 * @param array $arr Массив для сортировки.
	 */
	function deepKsort(&$arr){
		ksort($arr);
		foreach($arr as $key=>$val){
			if (is_array($val)){
				deepKsort($arr[$key]);
			}
		}
	}
	
	/**
	 * Разбирает bb коды и заменяет их на html.
	 * 
	 * @param string $string Строка с кодами.
	 * @return string Требуемый HTML 
	 */
	function parseBBCode($string){
		$string=nl2br($string);
		while (preg_match_all('#\[(.+?)=?(.*?)\](.+?)\[/\1\]#um', $string, $matches)){
			foreach ($matches[0] as $key => $match) { 
				list($tag, $param, $innertext) = array($matches[1][$key], $matches[2][$key], $matches[3][$key]); 
            	switch ($tag) { 
                	case 'b': $replacement = "<strong>$innertext</strong>"; break; 
                	case 'i': $replacement = "<em>$innertext</em>"; break; 
                	case 's': $replacement = "<span class=\"strike\">$innertext</span>"; break; 
                	case 'u': $replacement = "<span class=\"underline\">$innertext</span>"; break; 
            	} 
            	$string = str_replace($match, $replacement, $string); 
        	} 
        }
        return $string; 
	}
	
	/**
	 * Генерирует пароль.
	 * 
	 * @param int $num Количество символов в пароле. (Не более 248)
	 * @return string Новый пароль 
	 */
	function newPass($num){
		$s="qwertyuiopasdfghjklzxcvbnm";
		$s .=strtoupper($s);
		$s .="0123456789";
		$s .=$s.$s.$s;
		$s=str_shuffle($s);
		return substr($s,0,$num);
	}
	
	/**
	 * Удаляет файл, если тот существует.
	 * @param string $file Путь к файлу.
	 */
	function smartUnlink($file){
		if(file_exists($file)){
			unlink($file);
		}
	}
	
	/**
	 * Создает timestamp из даты формата d.m.Y H:i:s
	 * 
	 * @param string $date Дата формата d.m.Y H:i:s
	 */
	function timestampFromDate($date){
		$fullDate=$date;
		if (strlen($date)<12){
			$fullDate=$date." 00:00:00";
		}
		preg_match("#(\d{1,2}).(\d{1,2}).(\d{4})\s(\d{1,2}).(\d{1,2}).(\d{1,2})#", $fullDate, $matches);
		return mktime($matches[6],$matches[5],$matches[4], $matches[2], $matches[1], $matches[3]);
	}