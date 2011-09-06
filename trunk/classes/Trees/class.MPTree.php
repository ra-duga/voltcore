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
	 * Класс для работы с деревом. Дерево хранится по принципу Materialized path.
	 * 
	 * @category VoltCore
	 * @package VoltCoreClasses
	 * @subpackage TreeAdapters
	 */
	class MPTree extends DBTree{
	
		/**
		 * Имя поля с идентификаторами родителей. 
		 * @var string
		 */
		private $idParField;

		/**
		 * Имя поля с идентификаторами родителей. 
		 * @var string
		 */
		private $sep;
		
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
		 * 		idPrefix Префикс для добавления к идентификаторам узлов.
		 * 		(потомком - MPTree)
		 * 		idParField Имя поля с идентификаторами родителей. 
		 * 		sep Разделитель идентификаторов
		 */
		protected function assignNames($arrNames){
			parent::assignNames($arrNames);
			$this->idParField=$this->DB->escapeKeys($arrNames['idParField']);
			$this->sep=$arrNames['sep'] ? $arrNames['sep'] : '|';
		}
		
		protected function findRoot(){
			$sql="select $this->idField from $this->table where concat(concat('$this->sep',$this->idField),'$this->sep')=$this->idParField";
			$id=$this->DB->getVal($sql);
			if (is_null($id) || $id===false) throw new SqlException("Корневой элемент не найден","Нет данных",$sql);
			$this->rootId=$id;			
		}
		
		protected function getChildsQuery($idParent){
			return "select $this->idField from $this->table where $this->idParField like concat('%".$this->sep.$idParent.$this->sep."',concat($this->idField,'$this->sep'))";
		}		
		
		protected function getFamilyNextNum($idParent){
			$sorder=$this->orderField;
			$table=$this->nameTable;
			$tree=$this->table;
			$id=$this->idNameField;
			$idChild=$this->idField;
			$idPar=$this->idParField;
			
			$num=$this->DB->getVal("select max($sorder) from $table join $tree on $table.$id=$tree.$idChild where $idPar like concat('%".$this->sep.$idParent.$this->sep."',concat($this->idField,'$this->sep'))");
			return $num+1;
		}
		
		protected function doAddInsert($idChild, $idParent, $orderNum=null){
			$pars=$this->DB->getVal("select $this->idParField from $this->table where $this->idField=$idParent");
			$pars=$pars.$idChild.$this->sep;
			$this->DB->insert("insert into $this->table($this->idField, $this->idParField) values ($idChild, $pars)");
		}
		
		protected function doSelectParent($idChild){
			$rez=$this->DB->getVal("select $this->idParField from $this->table where $this->idField=$idChild");
			$ids=explode($this->sep,$rez);
			return $ids[count($ids)-3];
		}
		
		protected function doChangePar($idChild, $idParent, $orderNum=null){
			$oldPath=$this->DB->getVal("select $this->idParField from $this->table where $this->idField=$idChild");
			$newPath=$this->DB->getVal("select $this->idParField from $this->table where $this->idField=$idParent");
			$newPath=$newPath.$idChild.$this->sep;
			$this->DB->update("update $this->table set $this->idParField=replace('$oldPath',$this->idParField,'$newPath') where $this->idParField like '$oldPath%'");
		}
		
		protected function doDeleteSubTree($idChild){
			$allChilds="select $this->idField from $this->table where $this->idParField like '%".$this->sep.$idChild.$this->sep."%'";
			$childs="(".implode(",",$DB->getColumn($allChilds)).','.$idChild.")";
				
			$deleteName="delete from $this->nameTable where $this->idNameField in $childs";
			$delete="delete from $this->table where $this->idField in $childs";
			
			$this->DB->delete($delete);
			$this->DB->delete($deleteName);
		}
		
		public function getTree($extraFields=null, $subTreeRoot=null, $haveNames=DBTree::NO_NAME){
			if (!$this->nameTable || !$this->nameField) throw new FormatException("Недостаточно данных для создания дерева.","Указаны не все данные");
			if (is_null($subTreeRoot)){
				$subTreeRoot=$this->rootId;
			}else{
				$subTreeRoot=$this->getIdByName($subTreeRoot, $haveNames);
			}
			$sField= $this->orderField ? $this->orderField : $this->nameField;
			$extra= $this->extraFieldsToQueryString($extraFields);
									
			//Переприсваивание для создания более читаемого запроса
			$tree=$this->table;
			$f=$this->idField;
			$id=$this->idNameField;
			$pid=$this->idParField;
			$name=$this->nameField;
			$tab=$this->nameTable;
			
			$sql="select c.$id as cid, c.$name as cname $extra
			    from $tree as t join $tab as c on t.$f=c.$id
			    where $t.$pid like '%".$this->sep.$subTreeRoot.$this->sep."%'
			    order by t.$this->idParField";
			
			
			$this->DB->select($sql);
			
			
			
			
		}
		
		
		
		
		
		
		
		
		
	}