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
	 * Класс для работы с деревом. Дерево хранится по принципу Full Hierarchy (Redundant Adjacency List по другим источникам). 
	 * 
	 * Данный принцип предполагает:
	 * 1)Существует корневой элемент с id = 1. 
	 * 2)В таблице есть три колонки: идентификатор узла, идентификатор родителя, уровень.
	 * 3)У узла создается запись для каждого родителя с указанием уровня (первый родитель - 1, родитель родителя - 2 и т.д.).
	 * 4)Узел имеет как минимум 2 записи - ссылка на себя с уровнем 0 и ссылка на корневой элемент с id = 1. 
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @package classes
	 * @subpackage Trees
	 */
	class FHTree extends DBTree{

		/**
		 * Имя поля с идентификаторами. 
		 * @var string
		 */
		private $idField;

		/**
		 * Имя поля с идентификаторами родителей. 
		 * @var string
		 */
		private $idParField;
	
		/**
		 * Имя поля уровня. 
		 * @var string
		 */
		private $levelField;
	
		/**
		 * Имя таблицы, в которой содержатся имена узлов.
		 * @var string
		 */
		private $nameTable;
	
		/**
		 * Имя поля, в котором содержатся имена узлов.
		 * @var string
		 */
		private $nameField;
	
		/**
		 * Конструктор.
		 * 
		 * @param string $tab Таблица, в которой лежит дерево.
		 * @param string $idName Имя поля с идентификаторами.
		 * @param string $idParName Имя поля с идентификаторами родителей. 
		 * @param string $levelName Имя поля уровня.
		 * @param string $nameTab Имя таблицы, в которой содержатся имена узлов. 
		 * @param string $nameField Имя поля, в котором содержатся имена узлов.
		 */
		public function __construct($tab, $idName, $idParName, $levelName, $nameTab=null, $nameField=null){
			parent::__construct($tab);
			$DB=SQLDBFactory::getDB();
			$this->idField=$DB->escapeKeys($idName);
			$this->idParField=$DB->escapeKeys($idParName);
			$this->levelField=$DB->escapeKeys($levelName);
			$this->nameTable=$DB->escapeKeys($nameTab);
			$this->nameField=$DB->escapeKeys($nameField);
		}

		/**
		 * Устанавливает имя таблицы, в которой содержатся имена узлов.
		 * 
		 * @param string $name Имя таблицы, в которой содержатся имена узлов.
		 */
		public function setNameTable($name){
			$this->nameTable=$name;
		}
	
		/**
		 * Устанавливает имя поля, в котором содержатся имена узлов.
		 * 
		 * @param string $name Имя поля, в котором содержатся имена узлов.
		 */
		public function setNameField($name){
			$this->nameField=$name;
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
		public function add($id, $parId, $haveNames=DBTree::NO_NAME){
			if ($haveNames!=DBTree::NO_NAME && (!$this->nameTable || !$this->nameField)) throw new FormatException("Недостаточно данных.","Указаны не все данные");
			$this->tree=null;
			$DB=SQLDBFactory::getDB();
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
				
				$insert="insert into $this->table($this->idField, $this->idParField, $this->levelField)
					select $idChild, $this->idParField, $this->levelField+1 from $this->table where $this->idField=$idParent";
				$selfInsert="insert into $this->table($this->idField, $this->idParField, $this->levelField)	values($idChild, $idChild, 0)";		
				$DB->insert($insert);
				$DB->insert($selfInsert);
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
			$table=$this->table;
			$f=$this->idField;
			$pid=$this->idParField;
			$level=$this->levelField;
			$DB=SQLDBFactory::getDB();
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
				$allChilds="select $f from $table where $pid=$idChild";
				$allParents="select $pid from $table where $f=$idChild and $pid<>$idChild";
				
				$childs="(".implode(",",$DB->getColumn($allChilds)).")";
				$parents="(".implode(",",$DB->getColumn($allParents)).")";
				
				$delete="delete from $table where $f in $childs and $pid in $parents";
		
				$insert="insert into $table($f, $pid, $level)
					SELECT down.$f, up.$pid, down.$level + up.$level + 1
					FROM $table as up join $table as down on 
					up.$f = $idParent and down.$pid=$idChild";
		
				$DB->delete($delete);
				$DB->insert($insert);
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

				$allChilds="select $this->idField from $this->table where $this->idParField=$idChild";
				$childs="(".implode(",",$DB->getColumn($allChilds)).")";
				
				$deleteName="delete from $this->nameTable where id in $childs";
				$delete="delete from $this->table where $this->idField in $childs";
				
				$DB->delete($delete);
				$DB->delete($deleteName);
				$DB->commit();
			}catch(SqlException $e){
				$DB->rollback();
				throw $e;
			}
		}
		
		/**
		 * Возвращает непосредственного родителя узла.
		 * 
		 * @param mixed $id Идентификатор потомка.
		 * @param int $haveNames Определяет, считать ли $id именем.
		 * @return mixed Идентификатор предка.
		 */
		public function getParent($id, $haveNames=DBTree::NO_NAME){
			if (!$this->nameTable || !$this->nameField) throw new FormatException("Недостаточно данных.","Указаны не все данные");
			
			$DB=SQLDBFactory::getDB();
			$id=$DB->escapeString($id);

			$idChild=$id;
			if ($haveNames & DBTree::CHILD_NAME){
				$idChild=$DB->getVal("select id from $this->nameTable where $this->nameField=$id");
			}
			$select="select $this->idParField from $this->table where $this->idField=$idChild and $this->levelField=1";
			return $DB->getVal($select);
		}
		
	
		/**
		 * Вытаскивает дерево из БД и создает соответствующий массив. 
		 * 
		 * Запрос построен таким образом, что родитель узла в очередной строке находится в строке, которая уже обработана.
		 *   
		 * 
		 * @param mixed $id Идентификатор корня поддерева. Если не указан, то возвращается все дерево.
		 * @return array Массив с деревом. 
		 * 		Индексами этого массива является порядковый номер узла в уровне, начиная с 0, без пропусков.
		 * 		Узел – это массив, в которм содержатся следующие элементы:
		 * 			id – идентификатор узла дерева
		 * 			name – название узла дерева
		 * 			tree – список дочерних узлов для этого узла. Если у этого узла нет дочерних узлов, то здесь содержится пустой массив.
		 * @throws FormatException Если указаны не все поля.
		 */
		public function cultivateTree($id=1){
			if (!$this->nameTable || !$this->nameField) throw new FormatException("Недостаточно данных для создания дерева.","Указаны не все данные");
		
			//Переприсваивание для создания более читаемого запроса
			$tree=$this->table;
			$f=$this->idField;
			$pid=$this->idParField;
			$level=$this->levelField;
			$name=$this->nameField;
			$tab=$this->nameTable;
		
			//Выбор
			$sql="	select c.id as cid, c.$name as cname, par.id as pid
					from 
						$tree as t 
						join $tab as c on t.$f=c.id
						left outer join $tree as t2 on t.$f=t2.id_cat and t2.$level=1
						left outer join $tab as par on par.id=t2.$pid
					where t.$pid=$id
					order by t.$level, c.$name";

			$DB=SQLDBFactory::getDB();
			$DB->select($sql);
			
			//Запись в массив
			$path=array();
			while($row=$DB->fetchAssoc()){
				if(!isset($path[$row["cid"]])){
					$path[$row["cid"]]=array("name"=>$row["cname"], "id"=>$row["cid"], "tree"=>array());
				}
				if($row["pid"]){
					$path[$row["pid"]]["tree"][]=&$path[$row["cid"]];
				}
			}
			$tree=array();
			reset($path);
			$tree[0]=$path[key($path)];
			return $tree;
		}
	}