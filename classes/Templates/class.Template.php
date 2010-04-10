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
	 * Класс шаблон.
	 * 
	 * Клас реализует возможности работы с нативными шаблонами
	 * 
	 * @author [vs]
	 * @package classes
	 * @subpackage templates
	 */
	class Template{   

		/**
		 * Буфер переменных
		 * @var array
		 */
		protected $vars=array();

		/**
		 * Путь к шаблону
		 * @var string;
		 */
		protected $path=null;

		/**
		 * Конструктор
		 * 
		 * @param String $path Путь к файлу шаблона
		 */
		public function __construct($path){
			$this->path=$path;
		}

		/**
		 * Магическая запись переменной в буфер 
		 * @param string $var Имя переменной
		 * @param mixed $val Значение переменной
		 */
		public function __set($var, $val){
			$this->vars[$var]=$val;
		}
		
		/**
		 * Мангическое получение значения переменной.
		 * @param string $var Имя переменной
		 * @return mixed Значение переменной
		 */
		public function __get($var){
			if (isset($this->$var)){
				return $this->$var;
			}
			return $this->vars[$var];
		}

		/**
		 * Выполнение шаблона
		 * @return string Результат выполнения шаблона
		 */
		public function __toString(){
			extract($this->vars);
			ob_start();
			include($this->path);
			return ob_get_clean();
		}
	}