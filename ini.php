<?php
	/**
	 * Файл настроек 
	 *
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @version 1.0
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
	 * Подключение VoltCore
	 */
	require_once("/Frameworks/VoltCore/VoltCore.php");
?>