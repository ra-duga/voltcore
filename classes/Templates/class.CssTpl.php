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
	 * Шаблон подключения файлов CSS.
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @package classes
	 * @subpackage templates
	 */
	class CssTpl extends InclTpl{

		/**
		 * Конструктор.
		 */
		public function __construct(){
			parent::__construct(VCROOT."/Templates/linkCss.tpl");
			$cssFiles=getCSSFiles();
			$rightArr=array();
			foreach($cssFiles as $file){
				$rightArr[]=str_replace(DOCROOT,"",$file);
			}
			$this->files=$rightArr;
		}
	}
?>