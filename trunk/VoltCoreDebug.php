<?php
	/**
	 * Файл настроек для отладки программы
	 *
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @version 1.0
	 * @package voltcore
	 */

	/**
	 * Подключение основных настроек
	 */
	require_once("VoltCore.php");
	ini_set("display_errors", "on");
	
	//Параметры показа sql запросов
	$vf["db"]["sqlShow"]=false;
	$vf["db"]["sqlLog"]=true;
	
?>
