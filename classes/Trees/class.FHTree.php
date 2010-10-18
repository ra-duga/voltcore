<?php
	/**
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @version 2.0
	 * @package Trees
	 * @subpackage Adapters
	 */
	
	/**
	 * Класс для работы с деревом. Дерево хранится по принципу Full Hierarchy (Redundant Adjacency List по другим источникам). 
	 * 
	 * Данная реализация принципа предполагает:
	 * 1)Существует корневой элемент. 
	 * 2)В таблице есть три колонки: идентификатор узла, идентификатор родителя, уровень.
	 * 3)У узла создается запись для каждого родителя с указанием уровня (первый родитель - 1, родитель родителя - 2 и т.д.).
	 * 4)Каждый узел, кроме корня, имеет как минимум 2 записи - ссылка на себя с уровнем 0 и ссылка на корневой элемент. 
	 * 
	 * @package Trees
	 * @subpackage Adapters
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
	
		public function __construct($arrNames, $DBCon=null){
			parent::__construct($arrNames, $DBCon);
		}
		
		/**
		 * Записывает имена таблиц и полей.
		 * 
		 * @param array $arrNames Массив с именами. Обрабатываются поля:
		 * 		(родителем - DBTree)
		 * 		table Таблица, в которой лежит дерево.
		 * 		idField Имя поля с идентификаторами.
		 * 		nameTable Имя таблицы, в которой содержатся имена узлов. 
		 * 		idNameField Имя поля, в котором содержатся идентификаторы узлов в таблице имен.
		 * 		nameField Имя поля, в котором содержатся имена узлов.
		 * 		orderField Имя поля, по которому происходит сортировка.
		 * 		(потомком - FHTree)
		 * 		idParField Имя поля с идентификаторами родителей. 
		 * 		levelField Имя поля с уровнем.
		 */
		protected function assignNames($arrNames){
			parent::assignNames($arrNames);
			$this->idParField=$this->DB->escapeKeys($arrNames['idParField']);
			$this->levelField=$this->DB->escapeKeys($arrNames['levelField']);
		}

		protected function findRoot(){
			$sql="select $this->idField from $this->table where $this->idField=$this->idParField";
			$id=$this->DB->getVal($sql);
			if (is_null($id) || $id===false) throw new SqlException("Корневой элемент не найден","Нет данных",$sql);
			$this->rootId=$id;			
		}
		
		protected function getChildsQuery($idParent){
			return "select $this->idField from $this->table where $this->idParField=$idParent and $this->levelField=1";
		}		

		protected function getFamilyNextNum($idParent){
			$sorder=$this->orderField;
			$table=$this->nameTable;
			$tree=$this->table;
			$id=$this->idNameField;
			$idChild=$this->idField;
			$idPar=$this->idParField;
			$level=$this->levelField;
			
			$num=$this->DB->getVal("select max($sorder) from $table join $tree on $table.$id=$tree.$idChild where $idPar=$idParent and $level=1");
			return $num+1;
		}
		
		protected function doAddInsert($idChild, $idParent, $orderNum=null){
			$this->DB->insert("insert into $this->table($this->idField, $this->idParField, $this->levelField)
					select $idChild, $this->idParField, $this->levelField+1 from $this->table where $this->idField=$idParent");
			$this->DB->insert("insert into $this->table($this->idField, $this->idParField, $this->levelField)	values($idChild, $idChild, 0)");
		}
	
		protected function getSelectParent($idChild){
			return "select $this->idParField from $this->table where $this->idField=$idChild and $this->levelField=1";
		}
		
		protected function doChangePar($idChild, $idParent, $orderNum=null){
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
				
			$deleteName="delete from $this->nameTable where $this->idNameField in $childs";
			$delete="delete from $this->table where $this->idField in $childs";
				
			$DB->delete($delete);
			$DB->delete($deleteName);
		}
	
		public function getTree($extraFields=null, $subTreeRoot=null, $haveNames=DBTree::NO_NAME){
			if (!$this->nameTable || !$this->nameField) throw new FormatException("Недостаточно данных для создания дерева.","Указаны не все данные");
			if (is_null($subTreeRoot)){
				$subTreeRoot=$this->rootId;
			}else{
				$subTreeRoot=$this->getIdByName($subTreeRoot, $haveNames);
			}
			$sFiled= $this->orderField ? "c.$this->orderField," : '';
			$extra= $this->extraFieldsToQueryString($extraFields);
			//Переприсваивание для создания более читаемого запроса
			$tree=$this->table;
			$f=$this->idField;
			$id=$this->idNameField;
			$pid=$this->idParField;
			$level=$this->levelField;
			$name=$this->nameField;
			$tab=$this->nameTable;
		
			//Выбор
			$sql="	select c.$id as cid, c.$name as cname, par.$id as pid $extra
					from 
						$tree as t 
						join $tab as c on t.$f=c.$id
						left outer join $tree as t2 on t.$f=t2.$f and t2.$level=1
						left outer join $tab as par on par.$id=t2.$pid
					where t.$pid=$subTreeRoot
					order by t.$level, $sFiled c.$name";

			$this->DB->select($sql);
			
			//Запись в массив
			$path=array();
			while($row=$this->DB->fetchAssoc()){
				if(!isset($path[$row["cid"]])){
					$path[$row["cid"]]=array("name"=>$row["cname"], "id"=>$row["cid"], "tree"=>array());
					if ($extra){
						foreach($extraFields as $k=>$v){
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
				$tree[0]=reset($path);
			}
			return $tree;
		}
	}