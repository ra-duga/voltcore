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
	 * Клас реализует возможности работы с нативными шаблонами.
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @package classes
	 * @subpackage templates
	 */
	class Template{   

		/**
		 * Буфер переменных.
		 * @var array
		 */
		protected $vars=array();

		/**
		 * Путь к шаблону.
		 * @var string;
		 */
		protected $path=null;
		
		/**
		 * Нужно ли кэшировать шаблон.
		 * @var boolean
		 */
		protected $needCache=null;

		/**
		 * Дирректория для кэша шаблона.
		 * @var string
		 */
		protected $cacheDir=null;
		
		/**
		 * Конструктор.
		 * 
		 * @param string $path Путь к файлу шаблона.
		 * @param boolean $cache Нужно ли кэширование шаблона.
		 * @param string $dir Дирректория для кэша шаблона.
		 */
		public function __construct($path, $cache=null, $dir=null){
			global $vf;
			$this->path=$path;
			$this->needCache= $cache==null ? $vf["tpl"]["needCache"] : $cache;
			$this->cacheDir= $dir==null ? $vf["dir"]["cacheDir"] : $dir;
		}

		/**
		 * Магическая запись переменной в буфер.
		 * 
		 * @param string $var Имя переменной
		 * @param mixed $val Значение переменной
		 */
		public function __set($var, $val){
			$this->vars[$var]=$val;
		}
		
		/**
		 * Мангическое получение значения переменной.
		 * 
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
		 * Высчитывает хэш шаблона.
		 * 
		 * @return string Хэш шаблона.    
		 */
		public function hashCode(){
			$hash = 0;
			$this->ksortVars();
			$hash = md5(serialize($this));
			return $hash;
		}
		
		/**
		 * Сортирует массивы переменных шаблона и подшаблонов по ключам.
		 */
		public function ksortVars(){
			deepKsort($this->vars);
			foreach($this->vars as $var){
				if ($var instanceof Template){
					$var->ksortVars();
				}
			}
		}
						
		/**
		 * Возвращает имя файла с кэшем.
		 * 
 		 * @return string Имя файла с кэшем.    
		 */
		protected function getCacheFileName(){
			if(!file_exists($this->cacheDir)){
				mkdir($this->cacheDir);
			}
			return $this->cacheDir."/".basename($this->path, ".tpl")."_".$this->hashCode();
		}
		
		/**
		 * Преобразование шаблона в строку.
		 * 
		 * @return string Результат выполнения шаблона
		 */
		public function __toString(){
			if ($this->needCache){
				$cacheFileName=$this->getCacheFileName();
				if (file_exists($cacheFileName)){
					return file_get_contents($cacheFileName);
				}
				else{
					$rez=$this->compile();		
					file_put_contents($cacheFileName, $rez);
					return $rez;
				}
			}
			else{
				return $this->compile();
			}
		}
		
		/**
		 * Выполнение шаблона
		 * 
		 * @return string Результат выполнения шаблона
		 */
		public function compile(){
			extract($this->vars);
			ob_start();
			include($this->path);
			return ob_get_clean();
		}
	}