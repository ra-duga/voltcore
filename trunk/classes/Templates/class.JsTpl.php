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
	 * Шаблон подключения файлов JS.
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @package Templates
	 * @subpackage ConcreteTemplates
	 */
	class JsTpl extends InclTpl{

		/**
		 * Конструктор.
		 * 
		 * @param boolean $cache Нужно ли кэширование шаблона.
		 * @param string $dir Дирректория для кэша шаблона.
		 */
		public function __construct($cache=null, $dir=null){
			parent::__construct(VCROOT."/Templates/linkJS.tpl",$cache, $dir);
		}
		
		/**
		 * Возвращает массив подключаемых файлов.
		 * 
		 * @return array Массив подключаемых файлов.
		 */
		protected function getFiles(){
			return getJsFiles();
		}
	}
?>
