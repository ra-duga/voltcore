<?php
	/**
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 	 * @version 1.0
	 * @package User
	 * @subpackage Rights
	 */

	/**
	 * Класс стратегии "все разрешить".
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @package User
	 * @subpackage Rights
	 */
	class AdminRights implements UserRights{
		
		public function may($action, $params=null){
				return true;
		}
	}