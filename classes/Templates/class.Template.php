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
	 * Класс шаблон.
	 * 
	 * Клас реализует возможности работы с нативными шаблонами.
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @category VoltCore
	 * @package VoltCoreClasses
	 * @subpackage Templates
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
		public function __construct($path=null, $cache=null, $dir=null){
			global $vf;
			if (strpos($path,SITEROOT)===false && strpos($path,VCROOT)===false){
				$this->setPath($path);
			}
			else{
				$this->setFullPath($path);
			}
			$this->needCache= $cache==null ? $vf["tpl"]["needCache"] : $cache;
			$this->cacheDir= $dir==null ? $vf["dir"]["cache"] : $dir;
			
			if(!file_exists($this->cacheDir)){
				mkdir($this->cacheDir);
			}
		}

		/**
		 * Магическое получение значения переменной.
		 * 
		 * @param string $var Имя переменной
		 * @return mixed Значение переменной
		 */
		public function __get($var){
            return $this->get($var);
		}
        
		/**
		 * Получение значения переменной.
		 * 
		 * @param string $var Имя переменной
		 * @return mixed Значение переменной
		 */
        public function get($var){
			if(isset($this->vars[$var])){
				return $this->vars[$var];
			}else{
				return null;
			}
        }
		
		/**
		 * Магическая запись переменной в буфер.
		 * 
		 * @param string $var Имя переменной
		 * @param mixed $val Значение переменной
		 */
		public function __set($var, $val){
			$this->set($var, $val);
		}

        /**
		 * Запись переменной в буфер.
		 * 
		 * @param string $var Имя переменной
		 * @param mixed $val Значение переменной
		 */
		public function set($var, $val){
			$this->vars[$var] = $val;
		}
        
		/**
		 * Устанавливает путь к файлу шаблона из дирректории с шаблонами.
		 * 
		 * @param string $file Путь к файлу шаблона из дирректории $vf["dir"]["tpls"]
		 */
		public function setPath($file){
			global $vf;
			$this->path=$vf["dir"]["tpls"]."/".$file;
		}
		
		/**
		 * Устанавливает путь к файлу шаблона.
		 * 
		 * @param string $file Путь к файлу шаблона
		 */
		public function setFullPath($file){
			$this->path=$file;
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
			$hash = md5(serialize($this));
			return $hash;
		}
		
		/**
		 * Подготавливает объект к сериализации.
		 * 
		 * Т.к. объект этого класса обычно сериализуется для вычисления уникольного ключа объекта,
		 * то подготовка к сериализации заключается в обеспечении создания одинаковых строк для 
		 * одинаковых переменных в $vars, но указанных в разной последовательности.
		 * По сути метод сортирует массив переменных шаблона по ключам.
		 */
		public function __sleep(){
			deepKsort($this->vars);
			return array_keys(get_class_vars(__CLASS__));
		}
								
		/**
		 * Возвращает имя файла с кэшем.
		 * 
 		 * @return string Имя файла с кэшем.    
		 */
		protected function getCacheKey(){
			return basename($this->path, ".tpl")."_".$this->hashCode();
		}
		
		/**
		 * Преобразование шаблона в строку.
		 * 
		 * @return string Результат выполнения шаблона
		 */
		public function __toString(){
			if ($this->needCache){
				$cacheKey=$this->getCacheKey();
				$rez=getCacheFromFile($cacheKey, $this->cacheDir);
				if (!is_null($rez)){
					return $rez;
				}
				else{
					$rez=$this->compile();		
					cacheToFile($cacheKey, $rez, $this->cacheDir);
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
			if(!file_exists($this->path)) throw new FormatException("Не указан файл с шаблоном. Указан $this->path","Нет шаблона");
			extract($this->vars);
			ob_start();
			include($this->path);
			return ob_get_clean();
		}
        
        /**
         * Возвращает данные шаблонов без информации о шаблонах.
         * 
         * @return array Данные
         */
        public function getClearData(){
            $v = array();
            foreach($this->vars as $k=>$var){
                if ($var instanceof Template){
                    $v[$k] = $var->getClearData();
                }else{
                    $v[$k] = $var;
                }
            }
            return $v;
        }
	}