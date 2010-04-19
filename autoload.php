<?php

	/**
	 * Автозагрузчик.
	 *
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 	 * @version 1.2
	 * @package voltcore
	 */
 
 	/**
	 * Получение всех файлов.
	 * 
	 * @param string $dir Директория сканирования.
	 * @param array $allFiles Результат сканирования.
	 */
	function scanFiles($dir, &$allFiles) {
		if (!is_dir($dir)) return;
		$cont=glob($dir."/*");
		if(!$cont) return;
		
		foreach($cont as $file){
			if (is_dir($file)){
				scanFiles($file, $allFiles);
			}
			if (is_file($file)){
				list($c,$classname) = explode('.',basename($file,".php"));
				$classname=strtolower($classname);
				if(!isset($allFiles[$classname])){
					$allFiles[$classname] = $file;
				}
				else {
					logMsg("Дубликат класса [{$classname}]. Источник [{$allFiles[$classname]}]", "FATAL");
				}
			}
		}
	}
 
 	/**
	 * Функция автозагрузки файлов классов.
	 *
	 * @param string $class Имя требуемого класса
	 */
 	function voltAutoload($class='') {
		if($class == '') return;
 		
		static $dirs = false;
 
		if(!$dirs) {
			scanFiles(VCROOT.'/classes/',$dirs);
		}
 
		$classname = strtolower($class);

		if(isset($dirs[$classname])) {
			require_once($dirs[$classname]);
			return;
		}
 
		//die("не могу загрузить класс [{$class}]");
 	}
 
	spl_autoload_register("voltAutoload");
?>