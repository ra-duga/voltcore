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
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
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
		 * Вытаскивает из массива значения для переменых шаблона.
		 * 
		 * В случае асоциативного массива присваивает переменной шаблона с именем ключа массива соответсвующее значение массива.
		 * Если массив нумерованный (или имена начинаются с цифр), то создаются переменные типа vN, где N ключ массива.  
		 * 
		 * @param array $arr Массив со значениями.
		 */
		public function arraySet($arr){
			foreach($arr as $key=>$val){
				if ($key+0==0){
					$this->$key=$val;
				}
				else{
					$var="v".$key;
					$this->$var=$val;
				}
			}
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