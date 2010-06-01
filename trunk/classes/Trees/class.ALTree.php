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
	 * Класс для работы с деревом. Дерево хранится по принципу Adjacency List.
	 * 
	 * Данная реализация принципа предполагает:
	 * 1)Существует корневой элемент. 
	 * 2)Корневой элемент ссылается на себя как на родителя. 
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @package classes
	 * @subpackage Trees
	 */
	class ALTree extends DBTree{

		/**
		 * Имя поля с идентификаторами родителей. 
		 * @var string
		 */
		private $idParField;
	
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
		public function __construct($tab, $idName, $idParName, $nameTab=null, $nameField=null, $DBCon=null){
			parent::__construct($tab, $idName, $nameTab, $nameField, $DBCon);
			$this->idParField=$this->DB->escapeKeys($idParName);
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
		 * Возвращает запросы для вставки нового листа в дерево.
		 *
		 * @param int $idChild Идентификатор того, у кого меняем родителя.
		 * @param int $idParent Идентификатор нового родителя.
		 * @return string Запросы для вставки нового листа в дерево.
		 */
		protected function getAddInsert($idChild, $idParent){
			return array("insert into $this->table($this->idField, $this->idParField) values ($idChild, $idParent)");
		}
	
		/**
		 * Выполняет запросы для смены родителя у узла.
		 * 
		 * @param int $idChild Идентификатор того, у кого меняем родителя.
		 * @param int $idParent Идентификатор нового родителя.
		 * @throws SqlException При ошибке работы с базой.
		 */
		protected function doChangePar($idChild, $idParent){
			$insert="insert into $table($f, $pid, $level)
				SELECT down.$f, up.$pid, down.$level + up.$level + 1
				FROM $table as up join $table as down on 
				up.$f = $idParent and down.$pid=$idChild";
		
				$DB->insert($insert);
		}
	
		/**
		 * Выполняет запросы для удаления поддерева.
		 * 
		 * @param int $idChild Идентификатор того, у кого меняем родителя.
		 * @throws SqlException При ошибке работы с базой.
		 */
		protected function doDeleteSubTree($idChild){
			$DB=$this->DB;
			$DB->delete("delete from $this->table where $this->idField=$idChild or $this->idParField=$idChild");
			while ($DB->affectRows()>0){
				$DB->delete("delete from $this->nameTable where id not in (select $this->idField from $this->table)");
				$DB->delete("delete from $this->table where $this->idParField not in (select id from $this->nameTable)");
			}
		}
		
		/**
		 * Возвращает запрос для нахождения непосредственного родителя.
		 *
		 * @param int $idChild Идентификатор того, у кого меняем родителя.
		 * @return string Запрос для нахождения непосредственного родителя.
		 */
		protected function getSelectParent($idChild){
			return "select $this->idParField from $this->table where $this->idField=$idChild";
		}
		
	
		/**
		 * Вытаскивает дерево из БД и создает соответствующий массив. 
		 * 
		 * @todo Реализовать выбор поддерева.
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
			$name=$this->nameField;
			$tab=$this->nameTable;
		
			//Выбор
			$sql="select c.id as cid, c.$name as cname, par.id as pid
			    from $tree as t join $tab as c on t.$f=c.id
			    order by c.$name";
			
			$DB=$this->DB;
			$DB->select($sql);
			
			//Запись в массив
			$path=array();
			while($row=$DB->fetchAssoc()){
				if(!isset($path[$row["cid"]])){
					$path[$row["cid"]]=array("name"=>$row["cname"], "id"=>$row["cid"], "tree"=>array());
				}
				else{
					$path[$row["cid"]]["name"]=$row["cname"];
					$path[$row["cid"]]["id"]=$row["cid"];
				}
				if ($row["pid"]!=$row["cid"]){
					$path[$row["pid"]]["tree"][]=&$path[$row["cid"]];
				}
				else{
					$root=$row["cid"];
				}
			}
			$tree=array();
			$tree[0]=$path[$root];
			return $tree;
		}
	}