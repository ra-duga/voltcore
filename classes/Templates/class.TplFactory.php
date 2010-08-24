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
		 * Текущий шаблон.
		 * @var Template
		 */
		protected $tpl;
		
		/**
		 * Страница по-умолчанию
		 * @var string
		 */
		protected $defaultPage='index';

		/**
		 * Шаблон по-умолчанию
		 * @var string
		 */
		protected $defaultTpl='index';
		
		/**
		 * Логирует запрос несуществующего шаблона.
		 * 
		 * @param $mes Запрошенный шаблон
		 */
		abstract protected function logErrorCall($mes);

		/**
		 * Перехват вызова несуществующего метода.
		 * 
		 * Такая ситуация может возникнуть, если пользователь попытается сам вбить название
		 * страницы или шаблона в адресной строке.
		 * В классе-потомке обязательно должны присутствовать методы для обработки страниц и шаблонов по-умолчанию,
		 * иначе произойдет зацикливание.
		 *  
		 * @param string $n Название метода.
		 * @param array $a Аргументы.
		 */
		public function __call($n, $a){
			$this->logErrorCall($n);
			if (strpos($n, 'prepare')===0){
				$this->callTpl();
				return;
			}
			else{
				return $this->callInfo();			
			}
		}
		
		/**
		 * Вощзвращает запрошенный шаблон
		 * 
		 * @param string $chto Корень имени метода, который должен создать и заполнить шаблон.
		 */
		protected function callInfo($chto=null){
			$chto=$chto ? $chto : $this->defaultPage;
			$method="set".ucfirst($chto)."Info";
			return $this->$method();
			
		}	

		/**
		 * Вызывает подготовку шаблона.
		 * 
		 * @param string $chto какой шаблон подготовить
		 */
		protected function callTpl($chto=null){
			$chto=$chto ? $chto : $this->defaultTpl;
			$method="prepare".ucfirst($chto)."Tpl";
			return $this->$method();
		}	
		
	}
