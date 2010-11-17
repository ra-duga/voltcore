<?php
	/**
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @version 2.0
	 * @category VoltCore
	 * @package VoltCoreFiles
	 * @subpackage Classes
	 */
	
	/**
	 * Класс для работы с деревом. Дерево хранится по принципу Adjacency List.
	 * 
	 * Данная реализация принципа предполагает:
	 * 1)Существует корневой элемент. 
	 * 2)Корневой элемент ссылается на себя как на родителя. 
	 * 
	 * @category VoltCore
	 * @package VoltCoreClasses
	 * @subpackage TreeAdapters
	 */
	class ALTree extends DBTree{

		/**
		 * Имя поля с идентификаторами родителей. 
		 * @var string
		 */
		private $idParField;
	
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
		 * 		(потомком - ALTree)
		 * 		idParField Имя поля с идентификаторами родителей. 
		 */
		protected function assignNames($arrNames){
			parent::assignNames($arrNames);
			$this->idParField=$this->DB->escapeKeys($arrNames['idParField']);
		}
		
		protected function findRoot(){
			$sql="select $this->idField from $this->table where $this->idField=$this->idParField";
			$id=$this->DB->getVal($sql);
			if (is_null($id) || $id===false) throw new SqlException("Корневой элемент не найден","Нет данных",$sql);
			$this->rootId=$id;			
		}
		
		
		protected function getChildsQuery($idParent){
			return "select $this->idField from $this->table where $this->idParField=$idParent";
		}		

		protected function getFamilyNextNum($idParent){
			$sorder=$this->orderField;
			$table=$this->nameTable;
			$tree=$this->table;
			$id=$this->idNameField;
			$idChild=$this->idField;
			$idPar=$this->idParField;
			
			$num=$this->DB->getVal("select max($sorder) from $table join $tree on $table.$id=$tree.$idChild where $idPar=$idParent");
			return $num+1;
		}
		
		protected function doAddInsert($idChild, $idParent, $orderNum=null){
			$this->DB->insert("insert into $this->table($this->idField, $this->idParField) values ($idChild, $idParent)");
		}
		
		protected function getSelectParent($idChild){
			return "select $this->idParField from $this->table where $this->idField=$idChild";
		}
				
		protected function doChangePar($idChild, $idParent, $orderNum=null){
			$this->DB->update("update $this->table set $this->idParField=$idParent where $this->idField=$idChild");
		}
	
		protected function doDeleteSubTree($idChild){
			$DB=$this->DB;
			$numDeleted=0;
			$numDeleted=$DB->delete("delete from $this->table where $this->idField=$idChild or $this->idParField=$idChild");
			while ($numDeleted>0){
				$ids=implode(",",$DB->getColumn("select $this->idNameField from $this->nameTable where $this->idNameField not in (select $this->idField from $this->table)"));
				$numDeleted=$DB->delete("delete from $this->table where $this->idParField in ($ids)");
			}
			$DB->delete("delete from $this->nameTable where $this->idNameField not in (select $this->idField from $this->table)");
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
			$name=$this->nameField;
			$tab=$this->nameTable;
			
			//Выбор
			$sql="select c.$id as cid, c.$name as cname, t.$pid as pid $extra
			    from $tree as t join $tab as c on t.$f=c.$id
			    order by $sFiled c.$name";
			
			$DB=$this->DB;
			$DB->select($sql);
			
			//Запись в массив
			$path=array();
			while($row=$DB->fetchAssoc()){
				if(!isset($path[$row["cid"]])){
					$path[$row["cid"]]=array("name"=>$row["cname"], "id"=>$row["cid"], "tree"=>array());
					if ($extra){
						foreach($extraFields as $k=>$v){
							$path[$row["cid"]][$k]=$row[$k];
						}
					}
									}
				else{
					$path[$row["cid"]]["name"]=$row["cname"];
					$path[$row["cid"]]["id"]=$row["cid"];
					if ($extra){
						foreach($extraFields as $k=>$v){
							$path[$row["cid"]][$k]=$row[$k];
						}
					}
				}
				if ($row["pid"]!=$row["cid"]){
					$path[$row["pid"]]["tree"][]=&$path[$row["cid"]];
				}
			}
			$tree=array();
			if ($path && isset($path[$subTreeRoot])){
				$tree[0]=$path[$subTreeRoot];
			}
			return $tree;
		}
	}