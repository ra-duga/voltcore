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
		 * Имя таблицы, в которой содержатся имена узлов.
		 * @var string
		 */
		protected $nameTable;
		
		/**
		 * Имя поля с идентификаторами. 
		 * @var string
		 */
		protected $idField;
		
		/**
		 * Имя поля, в котором содержатся имена узлов.
		 * @var string
		 */
		protected $nameField;
		
		
		/**
		 * Само дерево.
		 * @var array
		 */
		protected $tree;
		
		/**
		 * Объект для работы с БД
		 * @var object
		 */
		protected $DB;
	
		/**
		 * Конструктор.
		 * 
		 * @param string $tab Таблица, в которой лежит дерево.
		 * @param string $idName Имя поля с идентификаторами.
		 * @param string $idParName Имя поля с идентификаторами родителей. 
		 * @param string $nameTab Имя таблицы, в которой содержатся имена узлов. 
		 * @param string $nameField Имя поля, в котором содержатся имена узлов.
		 * @param string $DBCon Объект для работы с БД.
		 */
		public function __construct($tab, $idName, $nameTab=null, $nameField=null, $DBCon=null){
			$this->DB=$DBCon ? $DBCon : SQLDBFactory::getDB();
			$this->table=$this->DB->escapeKeys($tab);
			$this->idField=$this->DB->escapeKeys($idName);
			$this->nameTable=$this->DB->escapeKeys($nameTab);
			$this->nameField=$this->DB->escapeKeys($nameField);
			$this->tree=null;
		}
	
		/**
		 * Возвращает запросы для вставки нового листа в дерево.
		 *
		 * @param int $idChild Идентификатор того, у кого меняем родителя.
		 * @param int $idParent Идентификатор нового родителя.
		 * @return string Запросы для вставки нового листа в дерево.
		 */
		abstract protected function getAddInsert($idChild, $idParent);

		/**
		 * Возвращает запрос для нахождения непосредственного родителя.
		 *
		 * @param int $idChild Идентификатор того, у кого меняем родителя.
		 * @return string Запрос для нахождения непосредственного родителя.
		 */
		abstract protected function getSelectParent($idChild);
		
		/**
		 * Выполняет запросы для смены родителя у узла.
		 * 
		 * @param int $idChild Идентификатор того, у кого меняем родителя.
		 * @param int $idParent Идентификатор нового родителя.
		 * @throws SqlException При ошибке работы с базой.
		 */
		abstract protected function doChangePar($idChild, $idParent);

		/**
		 * Выполняет запросы для удаления поддерева.
		 * 
		 * @param int $idChild Идентификатор того, у кого меняем родителя.
		 * @throws SqlException При ошибке работы с базой.
		 */
		abstract protected function doDeleteSubTree($idChild);
		
		/**
		 * Добавляет новый лист в дерево.
		 *  
		 * @param mixed $id Идентификатор потомка или уникальная строка для вставки в таблицу имен.
		 * @param mixed $parId Идентификатор родителя или уникальная строка для поиска в таблице имен.
		 * @param int $haveNames Определяет, какие параметры считать именами.
		 * @throws SqlException При ошибке работы с базой.
		 * @throws FormatException Если указаны не все поля.
		 */
		public function add($id, $parId, $haveNames=DBTree::NO_NAME){
			if ($haveNames!=DBTree::NO_NAME && (!$this->nameTable || !$this->nameField)) throw new FormatException("Недостаточно данных.","Указаны не все данные");
			$this->tree=null;
			$DB=$this->DB;
			try{
				$DB->startTran();
				$id=$DB->escapeString($id);
				$parId=$DB->escapeString($parId);

				$idChild=$id;
				if ($haveNames & DBTree::CHILD_NAME){
					$idChild=$DB->insert("insert into $this->nameTable($this->nameField) values($id)");
				}
				$idParent=$parId;
				if ($haveNames & DBTree::PARENT_NAME){
					$idParent=$DB->getVal("select id from $this->nameTable where $this->nameField=$parId");
				}
				
				$insert=$this->getAddInsert($idChild, $idParent);
				foreach($insert as $query){
					$DB->insert($query);
				}
				$DB->commit();
			}catch(SqlException $e){
				$DB->rollback();
				throw $e;
			}
		}
			
		/**
		 * Меняет родителя у узла.
		 * 
		 * @param mixed $id Идентификатор того, у кого меняем родителя.
		 * @param mixed $parId Идентификатор нового родителя.
		 * @param int $haveNames Определяет, какие параметры считать именами.
		 * @throws SqlException При ошибке работы с базой.
		 * @throws FormatException Если указаны не все поля.
		 */
		public function changePar($id, $parId, $haveNames=DBTree::NO_NAME){
			if ($haveNames!=DBTree::NO_NAME && (!$this->nameTable || !$this->nameField)) throw new FormatException("Недостаточно данных.","Указаны не все данные");
			$DB=$this->DB;
			try{
				$DB->startTran();
				$id=$DB->escapeString($id);
				$parId=$DB->escapeString($parId);

				$idChild=$id;
				if ($haveNames & DBTree::CHILD_NAME){
					$idChild=$DB->getVal("select id from $this->nameTable where $this->nameField=$id");
				}
				$idParent=$parId;
				if ($haveNames & DBTree::PARENT_NAME){
					$idParent=$DB->getVal("select id from $this->nameTable where $this->nameField=$parId");
				}
			
				$this->tree=null;
				$this->doChangePar($idChild, $idParent);
				$DB->commit();
			}catch(SqlException $e){
				$DB->rollback();
				throw $e;
			}
			
			
		}

		/**
		 * Удаляет поддерево.
		 * 
		 * @param mixed $id Идентификатор вершины поддерева или уникальная строка в таблице имен.
		 * @param int $haveNames Определяет, считать ли $id именем.
		 * @throws SqlException При ошибке работы с базой.
		 * @throws FormatException Если указаны не все поля.
		 */
		public function deleteSubTree($id, $haveNames=DBTree::NO_NAME){
			if (!$this->nameTable || !$this->nameField) throw new FormatException("Недостаточно.","Указаны не все данные");
			
			$this->tree=null;
			$DB=SQLDBFactory::getDB();
			try{
				$DB->startTran();
				$id=$DB->escapeString($id);

				$idChild=$id;
				if ($haveNames & DBTree::CHILD_NAME){
					$idChild=$DB->getVal("select id from $this->nameTable where $this->nameField=$id");
				}

				$this->doDeleteSubTree($idChild);
				$DB->commit();
			}catch(SqlException $e){
				$DB->rollback();
				throw $e;
			}
		}
		
		
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
		public function getParent($id, $haveNames=DBTree::NO_NAME){
			if (!$this->nameTable || !$this->nameField) throw new FormatException("Недостаточно данных.","Указаны не все данные");
			
			$DB=$this->DB;
			$id=$DB->escapeString($id);

			$idChild=$id;
			if ($haveNames & DBTree::CHILD_NAME){
				$idChild=$DB->getVal("select id from $this->nameTable where $this->nameField=$id");
			}
			$select=$this->getSelectParent($idChild);
			return $DB->getVal($select);
			
		}
		
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