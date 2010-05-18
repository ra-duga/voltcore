<?php
	/**
	 * Кэширует значение $val с ключом $key в хранилище $cacheType.
	 * 
	 * @param $key Ключ.
	 * @param $val Значение.
	 * @param $cacheType Тип хранилища.
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
	 * @param $key Ключ, по которому искать значение.
	 * @param $cacheType Тип хранилища.
	 */
	function getFromCache($key, $cacheType){
		global $vf;
		$cacheType= $cacheType ? $cacheType : $vf["cache"]["defType"];
		switch ($cacheType){
			case "file" : getCacheFromFile($key);
			default: throw new FormatException("Указано неизвестное хранилище.", "Неверные данные");  
		}
	}
	
	/**
	 * Кэширует значение в файл.
	 * @param $key Ключ.
	 * @param $val Значение.
	 * @param $dir Дирректория с кэшем.
	 */
	function cacheToFile($key, $val, $dir=null){
		file_put_contents(getCacheFileName($key, $dir), $val);
	}
	
	/**
	 * Возвращает значение из файлового кэша. 
	 * 
	 * @param $key Ключ.
	 * @param $dir Дирректория с кэшем.
	 */
	function getCacheFromFile($key, $dir=null){
		if (file_exists(getCacheFileName($key, $dir))){
			return file_get_contents($cacheFileName);
		}
		return null;
	}
	
	/**
	 * Возвращает имя файла с кэшем для указанного ключа.
	 * 
	 * @param $key Ключ.
	 * @param $dir Дирректория с кэшем.
	 */
	function getCacheFileName($key, $dir=null){
		global $vf;
		$dir= $dir ? $dir : $vf["dir"]["cache"]; 
		return $dir."/".$key.".cch";
	}
	
?>