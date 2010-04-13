<?php
	/**
	 * Пример стандартного файла настроек 
	 *
	 * @author 
	 * @copyright 
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @version 
	 * @package 
	 */
	
	header('Content-Type: text/html; charset=utf-8');
	
	/**
	 * Корень сайта.
	 * 
	 * @var string 
	 */
	define("DOCROOT",dirname(__FILE__));
	
	/**
	 * Директория логов.
	 * 
	 * @var string 
	 */
	define("LOGDIR",DOCROOT."/admin/logs");
	
	/**
	 * Префикс файлов логов.
	 * 
	 * @var string 
	 */
	define("LOG_PREFIX","/autoAuth_");
	
	/**
	 * Подключение VoltCore
	 */
	require_once("/Frameworks/VoltCore/VoltCore.php");
	
	//$vf["db"]["base"]="testBase";			// Тестовая база
	//$vf["db"]["sqlLog"]=true;				// Логировать ли запросы
	
?>