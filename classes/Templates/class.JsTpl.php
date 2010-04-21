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
	 * Шаблон подключения файлов JS.
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @package classes
	 * @subpackage templates
	 */
	class JsTpl extends InclTpl{

		/**
		 * Конструктор.
		 * 
		 * @param boolean $cache Нужно ли кэширование шаблона.
		 * @param string $dir Дирректория для кэша шаблона.
		 */
		public function __construct($cache=null, $dir=null){
			parent::__construct(VCROOT."/Templates/linkJs.tpl",$cache, $dir);
			$jsFiles=getJSFiles();
			$rightArr=array();
			foreach($jsFiles as $file){
				$rightArr[]=str_replace(DOCROOT,"",$file);
			}
			$this->files=$rightArr;
		}
	}
?>
