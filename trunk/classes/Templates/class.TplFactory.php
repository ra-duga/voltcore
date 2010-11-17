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
	 * Класс фабрика шаблонов.
	 * 
	 * Класс реализует общую функциональность фабрик шаблонов.
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @category VoltCore
	 * @package VoltCoreClasses
	 * @subpackage Templates
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
		 * Такая ситуация не должна возникать.
		 *  
		 * @param string $n Название метода.
		 * @param array $a Аргументы.
		 */
		public function __call($n, $a){
			throw new FormatException("Не должно быть вызова несуществующего метода!","Ошибка кодирования");
		}
		
		/**
		 * Вощзвращает запрошенный шаблон
		 * 
		 * @param string $chto Корень имени метода, который должен создать и заполнить шаблон.
		 */
		protected function callInfo($chto=null){
			$chto=$chto ? $chto : $this->defaultPage;
			$method="set".ucfirst($chto)."Info";
			if (method_exists($this, $method)){
				return $this->$method();
			}else{
				$this->logErrorCall($method);
				$method="set".ucfirst($this->defaultPage)."Info";
				return $this->$method();			
			}
			
		}	

		/**
		 * Вызывает подготовку шаблона.
		 * 
		 * @param string $chto какой шаблон подготовить
		 */
		protected function callTpl($chto=null){
			$chto=$chto ? $chto : $this->defaultTpl;
			$method="prepare".ucfirst($chto)."Tpl";
			if (method_exists($this, $method)){
				return $this->$method();
			}else{
				$this->logErrorCall($method);
				$method="prepare".ucfirst($this->defaultTpl)."Tpl";
				$this->$method();
			}
		}	
		
	}
