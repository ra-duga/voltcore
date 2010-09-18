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
	 * 4)Каждый узел, кроме корня, имеет как минимум 2 записи - ссылка на себя с уровнем 0 и ссылка на корневой элемент с id = 1. 
	 * 
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
		 * @param string $idParName Имя поля с идентификаторами родителей. 
		 * @param string $levelName Имя поля уровня.
		 */
		public function __construct($tab, $idName, $idParName, $levelName, $nameTab=null,  $idNameField='id', $nameField=null, $orderField=null, $DBCon=null){
			parent::__construct($tab, $idName, $nameTab,$idNameField, $nameField, $orderField, $DBCon);
			$this->idParField=$this->DB->escapeKeys($idParName);
			$this->levelField=$this->DB->escapeKeys($levelName);
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
		
		protected function getAddInsert($idChild, $idParent){
			return array(	
				"insert into $this->table($this->idField, $this->idParField, $this->levelField)
					select $idChild, $this->idParField, $this->levelField+1 from $this->table where $this->idField=$idParent",
				"insert into $this->table($this->idField, $this->idParField, $this->levelField)	values($idChild, $idChild, 0)");		
		}
	
		protected function getSelectParent($idChild){
			return "select $this->idParField from $this->table where $this->idField=$idChild and $this->levelField=1";
		}
		
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
				
			$deleteName="delete from $this->nameTable where $this->idNameField in $childs";
			$delete="delete from $this->table where $this->idField in $childs";
				
			$DB->delete($delete);
			$DB->delete($deleteName);
		}
		
		
	
		public function getTree($extraFields=null, $subTreeRoot=1){
			if (!$this->nameTable || !$this->nameField) throw new FormatException("Недостаточно данных для создания дерева.","Указаны не все данные");
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
				reset($path);
				$tree[0]=$path[key($path)];
			}
			return $tree;
		}
	}