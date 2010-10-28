<?php
	/**
	 * Запуск SingleProcessTest в новый поток.
	 * 
	 * В файл необходимо подключить нужный ini.php файл.
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @version 1.0
	 * @package Tests
	 */
	
	//ЭТА СТРОЧКА ДОЛЖНА МЕНЯТЬСЯ В ЗАВИСИМОСТИ ОТ СРЕДЫ ТЕСТИРОВАНИЯ
	require_once("H:/home/localhost/www/ini.php");
	require_once(VCROOT."/classes/Tests/class.SingleProcess_Tester.php");
	
	$proc=new SingleProcessTest("1", new AdminRights());
	$proc->run();

