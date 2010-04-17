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
	 * Шаблон вывода дерева.
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @package classes
	 * @subpackage templates
	 */

	class TreeTpl extends Template{

		const UL=0;
		const EXTJS=1;
		const EXTJSTABLE=2;
		
		/**
		 * Конструктор.
		 * 
		 * @param array $tree Дерево.
		 * @param int $tplType Тип шаблона (список, extjs дерево, extjs дерево в таблице).
		 */
		public function __construct($tree, $tplType=TreeTpl::UL, $cache=null){
			switch ($tplType) {
				case TreeTpl::UL: parent::__construct(VCROOT."/Templates/tree.tpl", $cache); break;
				case TreeTpl::EXTJS: parent::__construct(VCROOT."/Templates/extJSTree.tpl", $cache); break;
				case TreeTpl::EXTJSTABLE: 
					parent::__construct(VCROOT."/Templates/extJSTableTree.tpl", $cache); 
					$this->provider="col";
				break;
				default: parent::__construct(VCROOT."/Templates/tree.tpl", $cache);
			}
			$this->tree=$tree;
		}
	}
