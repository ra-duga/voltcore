<?php
	/**
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 	 * @version 1.0
	 * @package Templates
	 * @subpackage ConcreteTemplates
	 */

	/**
	 * Шаблон подключения файлов CSS.
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @package Templates
	 * @subpackage ConcreteTemplates
	 */
	class CssTpl extends InclTpl{

		/**
		 * Конструктор.
		 * 
		 * @param boolean $cache Нужно ли кэширование шаблона.
		 * @param string $dir Дирректория для кэша шаблона.
		 */
		public function __construct($cache=null, $dir=null){
			parent::__construct(VCROOT."/Templates/linkCSS.tpl",$cache, $dir);
		}
		
		/**
		 * Возвращает массив подключаемых файлов.
		 * 
		 * @return array Массив подключаемых файлов.
		 */
		protected function getFiles(){
			$arr=getCSSFiles();
			foreach ($arr as $file){
				$cssArr[$i]['title']=$file;
				$cssArr[$i]['media']='all';
				$i++;
			}
			return $cssArr;
		}
		
		/**
		 * Ищет файлы в настраиваемой дирректории и создает массив подключаемых файлов. 
		 */
		public function setDefault(){
			$files=getCSSFiles();
			$cssArr=array();
			$i=0;
			foreach($files as $file){
				$cssArr[$i]['title']=str_replace(SITEROOT,"",$file);
				$cssArr[$i]['media']='all';
				$i++;
			}
			$this->files=$cssArr;
		}
		
		/**
		 * Добавляет подключение css файлов.
		 * 
		 * Объявление, содержащееся в шаблоне, должно быть строкой или InclTpl объектом. 
		 * 
		 * @param mixed $more Строка, массив строк или InclTpl объект, содержащие необходимое дополнение.
		 * @param string $media Для каких устройств действует css 
		 * @throws FormatException При неверном типе данных.
		 */
		public function addCss($more, $media='all'){
			$addArray=$this->getAddArr($more);
			$rezArr=array();
			foreach($addArray as $key=>$file){
				if (is_string($file)){
					$rezArr[$key]['title']=$file;
					$rezArr[$key]['media']=$media;
				}
				else{
					$rezArr[$key]=$title;
				}
			}
			$this->files=array_merge($this->files, $rezArr);
		}
		
		/**
		 * Добавляет подключение файлов.
		 * 
		 * Объявление, содержащееся в шаблоне, должно быть строкой или InclTpl объектом.
		 * Объявления добовляются в начало.
		 * 
		 * @param mixed $more Строка, массив строк или InclTpl объект, содержащие необходимое дополнение.
		 * @param string $media Для каких устройств действует css 
		 * @throws FormatException При неверном типе данных.
		 */
		public function addBeforeCss($more, $media='all'){
			$addArray=$this->getAddArr($more);
			$rezArr=array();
			foreach($addArray as $key=>$file){
				if (is_string($file)){
					$rezArr[$key]['title']=$file;
					$rezArr[$key]['media']=$media;
				}
				else{
					$rezArr[$key]=$title;
				}
			}
			$this->files=array_merge($rezArr, $this->files);
		}
		
		/**
		 * Добавляет подключение файлов.
		 * 
		 * Объявление, содержащееся в шаблоне, должно быть строкой или InclTpl объектом. 
		 * 
		 * @param mixed $more Строка, массив строк или InclTpl объект, содержащие необходимое дополнение.
		 * @throws FormatException При неверном типе данных.
		 */
		public function add($more){
			$this->addCss($more);
		}
		
		/**
		 * Добавляет подключение файлов.
		 * 
		 * Объявление, содержащееся в шаблоне, должно быть строкой или InclTpl объектом.
		 * Объявления добовляются в начало.
		 * 
		 * @param mixed $more Строка, массив строк или InclTpl объект, содержащие необходимое дополнение.
		 * @throws FormatException При неверном типе данных.
		 */
		public function addBefore($more){
			$this->addBeforeCss($more);
		}
		
		
		
		
	}
?>