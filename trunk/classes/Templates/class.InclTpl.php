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
	 * Шаблон подключения сторонних файлов (таких как CSS или JS).
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @package classes
	 * @subpackage templates
	 */
	abstract class InclTpl extends Template{

		/**
		 * Конструктор.
		 * 
		 * @param String $path Путь к файлу шаблона
		 */
		public function __construct($path){
			parent::__construct($path);
			$this->files=array();
		}
		
			/**
		 * Преобразует переданные данные ля добавления в массив.
		 * 
		 * @param mixed $more Строка, массив строк или InclTpl объект, содержащие необходимое дополнение.
		 * @return array Данные для добавления.
		 * @throws FormatException При неверном типе данных.
		 */
		private function getAddArr($more){
			$addArray=array();
			if (is_string($more)){
				return explode(PHP_EOL, $more);
			}
			
			if (is_array($more)){
				return $more;
			}
			if ($more instanceof InclTpl){
				return $more->files;
			}
			throw FormatException("css или js для добавления переданы в неверном формате","Неверный тип данных");
		}
		
		/**
		 * Добавляет подключение файлов.
		 * 
		 * Объявление, содержащееся в шаблоне, должно быть строкой или InclTpl объектом. 
		 * 
		 * @param mixed $moreCss Строка, массив строк или InclTpl объект, содержащие необходимое дополнение.
		 * @throws FormatException При неверном типе данных.
		 */
		public function add($more){
			$addArray=$this->getAddArr($more);
			
			$this->files=array_merge($this->files, $addArray);
		}
		
		/**
		 * Добавляет подключение файлов.
		 * 
		 * Объявление, содержащееся в шаблоне, должно быть строкой или InclTpl объектом.
		 * Объявления добовляются в начало.
		 * 
		 * @param mixed $moreCss Строка, массив строк или InclTpl объект, содержащие необходимое дополнение.
		 * @throws FormatException При неверном типе данных.
		 */
		public function addBefore($more){
			$addArray=$this->getAddArr($more);
			
			$this->files=array_merge($addArray, $this->files);
		}
		
	}