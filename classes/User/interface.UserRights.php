<?php
	/**
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 	 * @version 1.0
	 * @package Interfaces
	 */

	/**
	 * Интерфейс стратегии прав пользователя.
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @package Interfaces
	 */
	interface UserRights{
		
		/**
		 * Все разрешать.
		 * @var int
		 */
		const ALLOW=1;
		
		/**
		 * Все запрещать.
		 * @var int
		 */
		const RESTRICT=0;
		
		/**
		 * Определяет может ли пользователь выполнить действие $action.
		 * 
		 * @param string $action Проверяемое действие.
		 * @param mixed $params Дополнительные параметры.
		 * @return mixed true - если действие может быть выполнено.
		 * 				 Данные которые должны быть переданы в вызывающую функцию, если действие не может быть выполнено.
		 */
		public function may($action, $params=null);
		
	}