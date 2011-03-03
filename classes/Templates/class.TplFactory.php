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
		 * Нужно ли логировать ошибку вызова несуществующего метода.
		 * @var bool
		 */
		protected $needLogError=true;
		
		/**
		 * Логирует запрос несуществующего шаблона.
		 * 
		 * @param $mes Запрошенный шаблон
		 */
		protected function logErrorCall($mes, $file=null, $type=null){
			$file=$file ? $file : EVENTDIR.'/wrongTplQuery.log';
			$type=$type ? $type : 'Запрос несуществующего шаблона';
			logToFile($mes, $file, $type,$_SERVER['REMOTE_ADDR']);
		}

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
				if ($this->needLogError){
					$this->logErrorCall($method);
				}
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
