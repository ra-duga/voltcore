<?php
	/**
	 * Библиотека функций хэширования.
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @version 1.0
	 * @package voltcore
	 * @subpackage libs
	 */


	/**
	 * Кэширует значение $val с ключом $key в хранилище $cacheType.
	 * 
	 * @param string $key Ключ.
	 * @param string $val Значение.
	 * @param string $cacheType Тип хранилища.
	 */
	function cacheIt($key, $val, $cacheType=null){
		global $vf;
		$cacheType= $cacheType ? $cacheType : $vf["cache"]["defType"];
		switch ($cacheType){
			case "file" : cacheToFile($key, $val);
			default: throw new FormatException("Указано неизвестное хранилище.", "Неверные данные");  
		}
		
	}
	
	/**
	 * Достает значение с ключом $key из хранилища $cacheType.
	 * 
	 * @param string $key Ключ, по которому искать значение.
	 * @param string $cacheType Тип хранилища.
	 */
	function getFromCache($key, $cacheType=null){
		global $vf;
		$cacheType= $cacheType ? $cacheType : $vf["cache"]["defType"];
		switch ($cacheType){
			case "file" : return getCacheFromFile($key);
			default: throw new FormatException("Указано неизвестное хранилище.", "Неверные данные");  
		}
	}
	
	/**
	 * Кэширует значение в файл.
	 * @param string $key Ключ.
	 * @param string $val Значение.
	 * @param string $dir Дирректория с кэшем.
	 */
	function cacheToFile($key, $val, $dir=null){
		file_put_contents(getCacheFileName($key, $dir), $val);
	}
	
	/**
	 * Возвращает значение из файлового кэша. 
	 * 
	 * @param string $key Ключ.
	 * @param string $dir Дирректория с кэшем.
	 * @return string Закэшированное значение.
	 */
	function getCacheFromFile($key, $dir=null){
		$cacheFileName=getCacheFileName($key, $dir);
		if (file_exists($cacheFileName)){
			return file_get_contents($cacheFileName);
		}
		return null;
	}
	
	/**
	 * Возвращает имя файла с кэшем для указанного ключа.
	 * 
	 * @param string $key Ключ.
	 * @param string $dir Дирректория с кэшем.
	 * @return string Имя файла для хранения кэша.
	 */
	function getCacheFileName($key, $dir=null){
		global $vf;
		$dir= $dir ? $dir : $vf["dir"]["cache"]; 
		return $dir."/".$key.".cch";
	}
	
?>