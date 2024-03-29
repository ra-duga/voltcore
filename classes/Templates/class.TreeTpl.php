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
	 * Шаблон вывода дерева.
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @category VoltCore
	 * @package VoltCoreClasses
	 * @subpackage TemplatesConcrete
	 */

	class TreeTpl extends Template{

		/**
		 * Обычный список.
		 * @var int
		 */
		const UL=0;

		/**
		 * ExtJs дерево.
		 * @var int
		 */
		const EXTJS=1;

		/**
		 * ExtJs дерево в таблице или ExtJs дерево с дополнительными параметрами.
		 * @var int
		 */
		const EXTJSADV=2;
		
		/**
		 * Конструктор.
		 * 
		 * @param array $tree Дерево.
		 * @param int $tplType Тип шаблона (список, extjs дерево, extjs дерево в таблице).
		 * @param boolean $gettext Использовать ли gettext для вывода дерева.
		 * @param boolean $cache Нужно ли кэширование шаблона.
		 * @param string $dir Дирректория для кэша шаблона.
		 */
		public function __construct($tree, $tplType=TreeTpl::UL, $gettext=null, $cache=null, $dir=null){
			global $vf;
			switch ($tplType) {
				case TreeTpl::UL: parent::__construct(VCROOT."/Templates/tree.tpl", $cache, $dir); break;
				case TreeTpl::EXTJS: parent::__construct(VCROOT."/Templates/ExtJS/extJSTree.tpl", $cache, $dir); break;
				case TreeTpl::EXTJSADV:parent::__construct(VCROOT."/Templates/ExtJS/extJSAdvTree.tpl", $cache, $dir); break;
				default: parent::__construct(VCROOT."/Templates/tree.tpl", $cache, $dir);
			}
			$this->tree=$tree;
			
			$this->gettext=is_null($gettext) ? $vf['gettext'] : $gettext;
		}
	}
