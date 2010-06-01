<?php
	/**
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 	 * @version 1.0
	 * @package classes
	 * @subpackage Templates
	 */

	/**
	 * Класс фабрика шаблонов.
	 * 
	 * Класс реализует общую функциональность фабрик шаблонов.
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @package classes
	 * @subpackage templates
	 */
	abstract class TplFactory{   

		/**
		 * Возвращает шаблон по умолчанию.
		 * 
		 * @return object Шаблон по умолчанию.
		 */
		abstract public function getIndex();

		/**
		 * Логирует запрос несуществующего шаблона.
		 * 
		 * @param $mes Запрошенный шаблон
		 */
		abstract protected function logErrorCall($mes);
		
		/**
		 * Магическое получение шаблона.
		 * 
		 * @param string $var Имя переменной
		 * @return mixed Значение переменной
		 */
		public function __get($var){
			$method='get'.ucfirst($var);
			if (method_exists($this,$method)){
				return $this->$method();
			}
			$this->logErrorCall($var);
			return $this->getIndexTpl();
		}
	}
