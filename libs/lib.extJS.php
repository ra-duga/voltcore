<?php
	/**
	 * Библиотека функций для работы с extJS.
	 * 
	 * Данная библиотека содержит функции для облегчения работы с extJS.  
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @version 1.0
	 * @package voltcore
	 * @subpackage libs
	 */

	/**
	 * Создает из содержания файлов основу для вкладок TabPanel 
	 * 
	 * Создает из содержания файлов div'ы, которые могут автоматически преобразоваться в закладки TabPanel.
	 * @param string $dir директория из которой брать файлы  
	 */
	function tabsFromFiles($dir=false){
		tabsFromArrFiles(getPHPFiles($dir));
	}
	
	/**
	 * Создает из содержания файлов основу для вкладок TabPanel 
	 * 
	 * Создает из содержания файлов div'ы, которые могут автоматически преобразоваться в закладки TabPanel.
	 * @param array $arr Массив с путями к файлам
	 */
	function tabsFromArrFiles($arr){
		foreach ($arr as $file){
			$name=ucfirst(substr(basename($file),0,-4)); // обрезаем имя папки, расширение и делаем первую букву заглавной
			echo "<div class='x-tab' title='$name'>";
			include($file);
			echo "</div>";
		}
	}
?>