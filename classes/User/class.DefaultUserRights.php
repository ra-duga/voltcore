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
	 * Класс стратегии прав пользователя по умолчанию.
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @package User
	 * @subpackage Rights
	 */
	class DefaultUserRights implements UserRights{
		
		/**
		 * Сообщение о невозможности выполнить действие.
		 * @var string
		 */
		protected $restrictMsg="Вы не можете выполнить данное действие.";

		/**
		 * Проверяет возможность выполнить действие.
		 * 
		 * @return bool true - если действие может быть выполнено, false - в противном случае.
		 */
		protected function checkRights(){
			return $vf['security']['userRights']==UserRights::ALLOW;
		}
		
		public function may($action, $params=null){
			if ($this->checkRights()){
				return true;
			}else{
				return $this->getRestrictMessage();
			}
		}
		
		/**
		 * Возвращает сообщение о невозможности выполнить действие.
		 * 
		 * @return string Сообщение о невозможности выполнить действие.
		 */
		public function getRestrictMessage(){
			return $this->restrictMsg;
		}
	}
		
