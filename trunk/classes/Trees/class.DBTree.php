<?php
	/**
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @version 1.0
	 * @package classes
	 * @subpackage Trees
	 */
	
	/**
	 * Абстрактный класс работы с деревьями. От него наследуют классы для работы с конкретным способом хранения дерева.
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @package classes
	 * @subpackage Trees
	 * @abstract
	 */
	abstract class DBTree{

		/**
		 * Имен не передано.
		 * @var int
		 */
		const NO_NAME=0;

		/**
		 * Передано имя потомка.
		 * @var int
		 */
		const CHILD_NAME=1;
		
		/**
		 * Передано имя родителя.
		 * @var int
		 */
		const PARENT_NAME=2;
		
		/**
		 * Передано оба имени.
		 * @var int
		 */
		const BOTH_NAME=3;
		
		
		/**
	 	 * Таблица, в которой лежит дерево.
	 	 * @var string
	 	 */
		protected $table;

		/**
		 * Само дерево.
		 * @var array
		 */
		protected $tree;
	
		/**
		 * Конструктор.
		 * 
		 * @param string $tab Таблица, в которой лежит дерево.
		 */
		public function __construct($tab){
			$DB=SQLDBFactory::getDB();
			$this->table=$DB->escapeKeys($tab);
			$this->tree=null;
		}
	
		/**
		 * Добавляет новый лист в дерево.
		 *  
		 * @param mixed $id Идентификатор потомка или уникальная строка для вставки в таблицу имен.
		 * @param mixed $parId Идентификатор родителя или уникальная строка для поиска в таблице имен.
		 * @param int $haveNames Определяет, какие параметры считать именами.
		 * @throws SqlException При ошибке работы с базой.
		 * @throws FormatException Если указаны не все поля.
		 */
		abstract public function add($id, $parId, $haveNames=DBTree::NO_NAME);
			
		/**
		/**
		 * Меняет родителя у узла.
		 * 
		 * @param mixed $id Идентификатор того, у кого меняем родителя.
		 * @param mixed $parId Идентификатор нового родителя.
		 * @param int $haveNames Определяет, какие параметры считать именами.
		 * @throws SqlException При ошибке работы с базой.
		 * @throws FormatException Если указаны не все поля.
		 */
		abstract public function changePar($id, $parId, $haveNames=DBTree::NO_NAME);

		/**
		 * Удаляет поддерево.
		 * 
		 * @param mixed $id Идентификатор вершины поддерева или уникальная строка в таблице имен.
		 * @param int $haveNames Определяет, считать ли $id именем.
		 * @throws SqlException При ошибке работы с базой.
		 * @throws FormatException Если указаны не все поля.
		 */
		abstract public function deleteSubTree($id, $haveNames=DBTree::NO_NAME);
		
		
		/**
		 * Вытаскивает дерево из БД и создает соответствующий массив. 
		 * 
		 * @param mixed $id Идентификатор корня поддерева.
		 * @return array Массив с деревом. 
		 * 		Индексами этого массива является порядковый номер узла в уровне, начиная с 0, без пропусков.
		 * 		Узел – это массив, в которм содержатся следующие элементы:
		 * 			id – идентификатор узла дерева
		 * 			name – название узла дерева
		 * 			tree – список дочерних узлов для этого узла. Если у этого узла нет дочерних узлов, то здесь содержится пустой массив.
		 * @throws FormatException Если указаны не все поля.
		 */
		abstract public function cultivateTree($id=1);
		
		/**
		 * Возвращает непосредственного родителя узла.
		 * 
		 * @param mixed $id Идентификатор потомка.
		 * @param int $haveNames Определяет, считать ли $id именем.
		 * @return mixed Идентификатор предка.
		 */
		abstract public function getParent($id, $haveNames=DBTree::NO_NAME);
		
		/**
		 * Вытаскивает дерево в массив. 
		 * 
		 * @return array Массив с деревом. 
		 * 		Индексами этого массива является порядковый номер узла в уровне, начиная с 0, без пропусков.
		 * 		Узел – это массив, в которм содержатся следующие элементы:
		 * 			id – идентификатор узла дерева
		 * 			name – название узла дерева
		 * 			tree – список дочерних узлов для этого узла. Если у этого узла нет дочерних узлов, то здесь содержится пустой массив.
		 */
		public function getTree(){
			if (!$this->tree){
				$this->tree=$this->cultivateTree();
			}
			return $this->tree;
		}
		
	} 