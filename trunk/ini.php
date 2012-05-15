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
	 * @var string 
	 */
	define("SITEROOT",str_replace('\\','/',dirname(__FILE__)));

	/**
	 * Базовый адрес модуля.
	 * @var string 
	 */
	define("URLROOT","http://VoltCore.volt");
	
	/**
	 * Директория c объектами.
	 * @var string 
	 */
	define("OBJDIR",SITEROOT."/objs");
	
	/**
	 * Директория логов.
	 * @var string 
	 */
	define("LOGDIR",SITEROOT."/logs");
	
	/**
	 * Префикс файлов логов.
	 * @var string 
	 */
	define("LOG_PREFIX","/");
	
    /**
     * Директория логов событий.
     * @var string 
     */
    define("EVENTDIR",LOGDIR."/events");
    
	/**
	 * Уровень ошибок для логирования.
	 * @var int
	 */
	define("ERR_LOG_LEVEL",0);
	
	/**
	 * Подключение VoltCore
	 */
	require_once("/Frameworks/VoltCore/VoltCore.php");
	
	//$vf["db"]["base"]="testBase";			// Тестовая база
	//$vf["db"]["sqlLog"]=true;				// Логировать ли запросы
	
?>