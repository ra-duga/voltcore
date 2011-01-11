<?php
	/**
	 * Библиотека функций логирования.
	 * 
	 * Данная библиотека содержит функции для записи логов.
	 * Настройки логирования находятся в массиве $vf["log"] 
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @version 1.5
	 * @category VoltCore
	 * @package VoltCoreFiles
	 * @subpackage Libs
	 */


	/**
	 * Записывает сообщение в файл.
	 * 
	 * Функция записывает сообщение $mes с типом $type и параметрами $maspar в файл $file.
	 * Файл находится в директории $vf["log"]["dir"]. В случае отсутствия файла - файл создается.
	 * Сообщение записывается в виде $mes."|".$type."|".$maspar."|".date("d-m-Y H:i:s").PHP_EOL.
	 * 
	 * @param string $mes Сообщение для логирования
	 * @param string $file Файл в который писать лог
	 * @param string $type Тип сообщения.
	 * @param array $masPar Дополнительные данные которые надо приписать к сообщению.
	 */
	function logToFile($mes, $file, $type='debug', $masPar=null){
		global $vf;
		
		makeDirs($file);
		
		if (is_array($masPar)) {
			$masPar=implode("|",$masPar);
		}
		$logText=$mes."|".$masPar."|".$type."|".date("d-m-Y H:i:s").PHP_EOL;
		file_put_contents($file, $logText, FILE_APPEND);
		}

	/**
	 * Создает на основе исключения cообщение и записывает его в файл. 
	 * 
	 * Функция записывает информацию об исключении в файл $file.
	 * Файл находится в директории $vf["log"]["dir"]. В случае отсутствия файла - файл создается.
	 * Сообщение записывается в виде $mes."|".$type."|File: ...; Line:...; |".date("d-m-Y H:i:s").PHP_EOL.
	 * 
	 * @param Exception $e исключение для логирования
	 * @param string $file Файл в который писать лог
	 */
	function excToFile($e, $file){
		$par  = "File:".$e->getFile().";";
		$par .= "Line:".$e->getLine().";";
		$type='debug';
		if (method_exists($e, "getType")){
			$type=$e->getType();
		}
		if (method_exists($e, "getSql")){
			$par=$e->getSql()."|".$par;
		}
		
		logToFile($e->getMessage(), $file, $type, $par);
		}
		
	/**
	 * Создает на основе исключения cообщение и записывает его в стандартный файл. 
	 * 
	 * Функция записывает информацию об исключении в файл указанный в $vf["log"][$fil].
	 * Файл находится в директории $vf["log"]["dir"]. В случае отсутствия файла - файл создается.
	 * Сообщение записывается в виде $mes."|".$type."|File: ...; Line:...; |".date("d-m-Y H:i:s").PHP_EOL.
	 * 
	 * @param Exception $e исключение для логирования
	 * @param string $fil Ключ в массиве $vf["log"], соответствующий нужному файлу.
	 */
	function excLog($e){
		global $vf;
		$par  = "File:".$e->getFile().";";
		$par .= "Line:".$e->getLine().";";
		$type='debug';
		if ($e instanceof VoltException){
			$type=$e->getType();
		}
		if ($e instanceof SqlException){
			$par=PHP_EOL.$e->getSql().PHP_EOL."|".$par;
		}
		$fil=get_class($e);
		if(!in_array($fil, $vf['log'])){
			$fil=get_parent_class($e);
		}
		if(!in_array($fil, $vf['log'])){
			$fil='log';
		}
		
		logMsg($e->getMessage(), $type, $fil, $par);
	}
		
	/**
	 * Записывает сообщение в стандартный файл. 
	 *
	 * Функция записывает сообщение $mes с типом $type и параметрами $maspar в файл указанный в $vf["log"][$fil].
	 * Файл находится в директории $vf["log"]["dir"]. В случае отсутствия файла - файл создается.
	 * Сообщение записывается в виде $mes."|".$type."|".$maspar."|".date("d-m-Y H:i:s").PHP_EOL.
	 * 
	 * @param string $mes Сообщение для логирования.
	 * @param string $type Тип сообщения.
	 * @param string $fil Ключ в массиве $vf["log"], соответствующий нужному файлу.
	 * @param array $maspar Дополнительные данные которые надо приписать к сообщению.
	 */
	function logMsg($mes, $type='debug', $fil='log', $masPar=null){
		global $vf;
		if (is_array($masPar)) {
			$masPar=implode("|",$masPar);
		}
		$logText=$mes."|".$masPar."|".$type."|".date("d-m-Y H:i:s")."\r\n";
		$logFile=$vf["log"][$fil];
		
		makeDirs($logFile);
		
		file_put_contents($logFile, $logText, FILE_APPEND);
	}
	
	/**
	 * Логирует переменную
	 * 
	 * Функция записывает информацию о переменной в файл указанный в $vf["log"]["var"].
	 * Файл находится в директории $vf["log"]["dir"]. В случае отсутствия файла - файл создается.
	 * Сообщение записывается в виде $name." => ".var_export($var)."|".$type."|".$maspar."|".date("d-m-Y H:i:s").PHP_EOL. 
	 * 
	 * @param string $name Имя переменной
	 * @param mixed $var Переменная для логирования
	 * @param bool $return Нужно вернуть результат(true) или залогировать(false).
	 * @param string $type Тип сообщения.
	 * @param array $maspar Дополнительные данные которые надо приписать к сообщению.
	 * @return string Если $return=true, то возвращается строка вида $name." => ".var_dump($var);
	 */
	function logVar($var, $name='par', $return=false, $type='debug', $masPar=null){
		ob_start();
			var_dump($var);
		$msg=ob_get_clean();
		if ($return) return $name." => ".$msg;
		else logMsg($name." => ".$msg, $type, 'var', $masPar);
	}
?>