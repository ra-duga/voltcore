<?php
	/**
	 * Библиотека функций для работы с файлами.
	 * 
	 * Данная библиотека содержит функции работы с файлами. 
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
	 * Функция возвращает строку с подключением js файлов
	 * 
	 * @param string $dir дирректория из которой брать файлы
	 * @return Template Шаблон, с html инструкциями подключения js файлов
	 */
	function includeJS($dir=false){
		$jsFiles=getJSFiles($dir);
		$tpl = new Template(VCROOT."/Templates/linkJS.tpl");
		$tpl->files=$jsFiles;
		return $tpl;
	}

	/**
	 * Функция возвращает строку с подключением css файлов
	 * 
	 * @param string $dir дирректория из которой брать файлы
	 * @return Template Шаблон, с html инструкциями подключения css файлов
	 */
	function includeCSS($dir=false){
		$cssFiles=getCSSFiles($dir);
		$tpl = new Template(VCROOT."/Templates/linkCSS.tpl");
		$tpl->files=$cssFiles;
		return $tpl;
	}
	
	/**
	 * Функция возвращает массив с именами js файлов
	 * 
	 * @param $dir дирректория из которой брать файлы
	 * @return array массив с именами файлов 
	 */
	function getJSFiles($dir=false){
		global $vf;
		if ($dir===false){
			$dir=$vf["dir"]["js"];
		}
		return getFiles($dir."/*.js");
	}

	/**
	 * Функция возвращает массив с именами css файлов
	 * 
	 * @param string $dir дирректория из которой брать файлы
	 * @return array массив с именами файлов 
	 */
	function getCSSFiles($dir=false){
		global $vf;
		if ($dir===false){
			$dir=$vf["dir"]["css"];
		}
			return getFiles($dir."/*.css");
	}

	/**
	 * Функция возвращает массив с именами php файлов
	 * 
	 * @param string $dir дирректория из которой брать файлы
	 * @return array массив с именами файлов 
	 */
	function getPHPFiles($dir=false){
		global $vf;
		if ($dir===false){
			$dir=$vf["dir"]["php"];
		}
		return getFiles($dir."/*.php");
	}

	/**
	 * Функция возвращает массив с именами файлов, которые соответствуют шаблону 
	 * 
	 * @param string $pattern шаблон, которому должны соответствовать имена файлов
	 * @return array массив с именами файлов 
	 */
	function getFiles($pattern){
		$rez=glob($pattern);
		return  $rez ? $rez : array();
	}
	
	/**
	 * Создает путь до файла $file. 
	 * 
	 * @param string $file Адрес файла для которого создаются дирректории. 
	 */
	function makeDirs($file){
		if (!file_exists(dirname($file))){
			if (!mkdir(dirname($file), 0777, true)){
				throw FormatException("Не удалось создать дирректории","Некуда записывать");
			}
		}
	}