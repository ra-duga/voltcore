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
	 * Данная реализация принципа предполагает:
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
		 * Конструктор.
		 * 
		 * @param string $tab Таблица, в которой лежит дерево.
		 * @param string $idName Имя поля с идентификаторами.
		 * @param string $idParName Имя поля с идентификаторами родителей. 
		 * @param string $levelName Имя поля уровня.
		 * @param string $nameTab Имя таблицы, в которой содержатся имена узлов. 
		 * @param string $nameField Имя поля, в котором содержатся имена узлов.
		 * @param string $DBCon Объект для работы с БД.
		 */
		public function __construct($tab, $idName, $idParName, $levelName, $nameTab=null, $nameField=null, $DBCon=null){
			parent::__construct($tab, $idName, $nameTab, $nameField, $DBCon);
			$this->idParField=$this->DB->escapeKeys($idParName);
			$this->levelField=$this->DB->escapeKeys($levelName);
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
			return array(	
				"insert into $this->table($this->idField, $this->idParField, $this->levelField)
					select $idChild, $this->idParField, $this->levelField+1 from $this->table where $this->idField=$idParent",
				"insert into $this->table($this->idField, $this->idParField, $this->levelField)	values($idChild, $idChild, 0)");		
		}
	
		/**
		 * Выполняет запросы для смены родителя у узла.
		 * 
		 * @param int $idChild Идентификатор того, у кого меняем родителя.
		 * @param int $idParent Идентификатор нового родителя.
		 * @throws SqlException При ошибке работы с базой.
		 */
		protected function doChangePar($idChild, $idParent){
			$table=$this->table;
			$f=$this->idField;
			$pid=$this->idParField;
			$level=$this->levelField;
			$DB=$this->DB;
			
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
		}
	
		/**
		 * Выполняет запросы для удаления поддерева.
		 * 
		 * @param int $idChild Идентификатор того, у кого меняем родителя.
		 * @throws SqlException При ошибке работы с базой.
		 */
		protected function doDeleteSubTree($idChild){
			$DB=$this->DB;
			
			$allChilds="select $this->idField from $this->table where $this->idParField=$idChild";
			$childs="(".implode(",",$DB->getColumn($allChilds)).")";
				
			$deleteName="delete from $this->nameTable where id in $childs";
			$delete="delete from $this->table where $this->idField in $childs";
				
			$DB->delete($delete);
			$DB->delete($deleteName);
		}
		
		/**
		 * Возвращает запрос для нахождения непосредственного родителя.
		 *
		 * @param int $idChild Идентификатор того, у кого меняем родителя.
		 * @return string Запрос для нахождения непосредственного родителя.
		 */
		protected function getSelectParent($idChild){
			return "select $this->idParField from $this->table where $this->idField=$idChild and $this->levelField=1";
		}
		
	
		/**
		 * Вытаскивает дерево из БД и создает соответствующий массив. 
		 * 
		 * Запрос построен таким образом, что родитель узла в очередной строке находится в строке, которая уже обработана.
		 * 
		 * @param string $sortField Имя поля по которому сортировать дерево. 
		 * @param array $dopFields дополнительные поля из таблицы с именем.
		 * @param mixed $id Идентификатор корня поддерева. Если не указан, то возвращается все дерево.
		 * @return array Массив с деревом. 
		 * 		Индексами этого массива является порядковый номер узла в уровне, начиная с 0, без пропусков.
		 * 		Узел – это массив, в которм содержатся следующие элементы:
		 * 			id – идентификатор узла дерева
		 * 			name – название узла дерева
		 * 			tree – список дочерних узлов для этого узла. Если у этого узла нет дочерних узлов, то здесь содержится пустой массив.
		 * @throws FormatException Если указаны не все поля.
		 */
		public function cultivateTree($sortField=null,  $dopFields=null, $id=1){
			if (!$this->nameTable || !$this->nameField) throw new FormatException("Недостаточно данных для создания дерева.","Указаны не все данные");
			$sFiled= $sortField ? "c.$sortField," : '';
			$dop= $this->getDopFields($dopFields);
			//Переприсваивание для создания более читаемого запроса
			$tree=$this->table;
			$f=$this->idField;
			$pid=$this->idParField;
			$level=$this->levelField;
			$name=$this->nameField;
			$tab=$this->nameTable;
		
			//Выбор
			$sql="	select c.id as cid, c.$name as cname, par.id as pid $dop
					from 
						$tree as t 
						join $tab as c on t.$f=c.id
						left outer join $tree as t2 on t.$f=t2.$f and t2.$level=1
						left outer join $tab as par on par.id=t2.$pid
					where t.$pid=$id
					order by t.$level, $sFiled c.$name";

			$DB=SQLDBFactory::getDB();
			$DB->select($sql);
			
			//Запись в массив
			$path=array();
			while($row=$DB->fetchAssoc()){
				if(!isset($path[$row["cid"]])){
					$path[$row["cid"]]=array("name"=>$row["cname"], "id"=>$row["cid"], "tree"=>array());
					if ($dop){
						foreach($dopFields as $k=>$v){
							$path[$row["cid"]][$k]=$row[$k];
						}
					}
				}
				if($row["pid"]){
					$path[$row["pid"]]["tree"][]=&$path[$row["cid"]];
				}
			}
			$tree=array();
			if ($path){
				reset($path);
				$tree[0]=$path[key($path)];
			}
			return $tree;
		}
	}