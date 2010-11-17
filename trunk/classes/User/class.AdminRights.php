<?php
	/**
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 	 * @version 1.0
	 * @category VoltCore
	 * @package VoltCoreFiles
	 * @subpackage Classes
	 */

	/**
	 * Класс стратегии "все разрешить".
	 * 
	 * @category VoltCore
	 * @package VoltCoreClasses
	 * @subpackage UserRights
	 */
	class AdminRights implements UserRights{
		
		public function may($action, $params=null){
				return true;
		}
	}