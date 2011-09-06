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
	 * Класс для работы с деревом. Дерево хранится по принципу Nested Sets.
	 * 
	 * @category VoltCore
	 * @package VoltCoreClasses
	 * @subpackage TreeAdapters
	 */
	class NSTree extends DBTree{

		/**
		 * Сжимать дерево.
		 * @var int
		 */
		const COLLAPSE=0;
		
		/**
		 * Расширять дерево.
		 * @var int
		 */
		const EXPAND=1;
		
		/**
		 * Имя поля с левой границей. 
		 * @var string
		 */
		private $leftField;
	
		/**
		 * Имя поля с правой границей. 
		 * @var string
		 */
		private $rightField;
		
		/**
		 * Имя поля с уровнем.
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
		 * 		idPrefix Префикс для добавления к идентификаторам узлов.
		 * 		(потомком - NSTree)
		 * 		leftField Имя поля с левой границей. 
		 * 		rightField Имя поля с правой границей. 
		 * 		levelField Имя поля с уровнем.
		 */
		protected function assignNames($arrNames){
			parent::assignNames($arrNames);
			$this->leftField=$this->DB->escapeKeys($arrNames['leftField']);
			$this->rightField=$this->DB->escapeKeys($arrNames['rightField']);
			$this->levelField=$this->DB->escapeKeys($arrNames['levelField']);
		}
		
		protected function findRoot(){
			$sql="select $this->idField from $this->table where $this->leftField in (select min($this->leftField) from $this->table)";
			$id=$this->DB->getVal($sql);
			if (is_null($id) || $id===false) throw new SqlException("Корневой элемент не найден","Нет данных",$sql);
			$this->rootId=$id;			
		}
		
		
		protected function getChildsQuery($idParent){
			return "select $this->idField from $this->table as tree join 
				(select $this->leftField as lf, $this->rightField as rf, $this->levelField as lev from $this->table where $this->idField=$idParent)as t on
				t.lf<=tree.$this->leftField and t.rf>=tree.$this->rightField and t.lev=tree.$this->levelField-1";
		}		
		
		protected function getFamilyNextNum($idParent){
			$sorder=$this->orderField;
			$table=$this->nameTable;
			$tree=$this->table;
			$id=$this->idNameField;
			$idChild=$this->idField;
			
			$num=$this->DB->getVal("select max($sorder) from $table join $tree on $table.$id=$tree.$idChild join
				(select $this->leftField as lf, $this->rightField as rf, $this->levelField as lev from $this->table where $this->idField=$idParent)as t on
				t.lf<=$tree.$this->leftField and t.rf>=$tree.$this->rightField and t.lev=$tree.$this->levelField-1");
			return $num+1;
		}
		
		protected function doAddInsert($idChild, $idParent, $orderNum=null){
			
			list($pLeft, $pRight, $pLev)=$this->getNode($idParent);

			$childLeft=$this->getBorderByNum($orderNum, $pLeft, $pRight, $pLev);
			$childLeft=$this->prepareTree($childLeft, null, 2);
			
			$lev=$pLev+1;
			$childRight=$childLeft+1;
			$this->DB->insert("insert into $this->table($this->idField, $this->leftField, $this->rightField, $this->levelField)values ($idChild, $childLeft, $childRight, $lev)");
		}
		
		protected function doSelectParent($idChild){
			$rez=$this->DB->getVal("select $this->idField from $this->table as tree join (select $this->leftField as lf, $this->rightField as rf, $this->levelField as lev from $this->table where $this->idField=$idChild)as t on
					t.lf>tree.$this->leftField and t.rf<tree.$this->rightField and t.lev=tree.$this->levelField+1");
			return $rez; 
		}
				
		protected function doChangePar($idChild, $idParent, $orderNum=null){
			$center=$this->getCenter();
			
			list($left, $right, $lev)=$this->getNode($idChild);
			
			$delta=$right-$left+1;
			//Находим идентификаторы перемещаемого узла и потомков, чтобы исключить их из коллапса и расширения.
			$ids=$this->DB->getColumn("select $this->idField from $this->table where $this->leftField>=$left and $this->rightField<=$right");
			$ids="(".implode(',', $ids).")";
			
			//"Убираем" элемент из дерева (схлопываем дерево).
			$this->prepareTree($left, $right, $delta, NSTree::COLLAPSE);
			
			list($parLeft, $parRight, $parLev)=$this->getNode($idParent);

			//Находим куда "вставлять" элемент и раздвигаем дерево.
			$childLeft=$this->getBorderByNum($orderNum, $parLeft, $parRight, $parLev);
			$childLeft=$this->prepareTree($childLeft, null, $delta, NSTree::EXPAND, $ids);

			//"Вставляем" элемент. По сути просто сдвигаем границы и изменяем уровни элемента и потомков. 
			$moveDelta=$left-$childLeft;
			$levelDelta=$lev-$parLev-1;
			$this->DB->update("update $this->table set $this->leftField=$this->leftField-($moveDelta), $this->rightField=$this->rightField-($moveDelta), $this->levelField=$this->levelField-($levelDelta) where $this->idField in $ids");
		}
	
		protected function doDeleteSubTree($idChild){
			list($left, $right, $lev)=$this->getNode($idChild);
			$this->DB->delete("delete from $this->table where $this->leftField>=$left and $this->rightField<=$right");
			$this->DB->delete("delete from $this->nameTable where $this->idNameField not in (select $this->idField from $this->table)");
			$delta=$right-$left+1;
			$this->prepareTree($left, $right, $delta, NSTree::COLLAPSE);
		}
		
		public function getTree($extraFields=null, $subTreeRoot=null, $haveNames=DBTree::NO_NAME){
			if (!$this->nameTable || !$this->nameField) throw new FormatException("Недостаточно данных для создания дерева.","Указаны не все данные");
			if (is_null($subTreeRoot)){
				$subTreeRoot=$this->rootId;
			}else{
				$subTreeRoot=$this->getIdByName($subTreeRoot, $haveNames);
			}
			$extra= $this->extraFieldsToQueryString($extraFields);
									
			//Переприсваивание для создания более читаемого запроса
			$tree=$this->table;
			$f=$this->idField;
			$left=$this->leftField;
			$right=$this->rightField;
			$lev=$this->levelField;
			$id=$this->idNameField;
			$name=$this->nameField;
			$tab=$this->nameTable;

			list($rootL, $rootR, $rootLev)=$this->getNode($subTreeRoot);
			if (!$rootL) return array();
			//Выбор
			$sql="select c.$id as cid, c.$name as cname, t.$left as lf, t.$right as rf, t.$lev as lev $extra
			    from $tree as t join $tab as c on t.$f=c.$id
			    where t.$left>=$rootL and t.$right<=$rootR
			    order by t.$left";
			
			$DB=$this->DB;
			$DB->select($sql);
			

			$tree=array();
			$stackTrees=array();
			$tempTree=array();
			$level=$rootLev;
			$i=0;
  			while($row=$DB->fetchAssoc()){
  				$arr=array();
				$arr['id']=$this->idPrefix.$row['cid'];
				$arr['name']=$row['cname'];
				$arr['tree']=array();
    			if ($extra){
    				foreach($extraFields as $k=>$v){
						$arr[$k]=$row[$k];
    				}
				}
				if ($i==0){
					$tree=array($arr);
					$level=$row['lev']+1;
					$tempTree=&$tree[0]['tree'];
					$i++;
					continue;
				}	
				if($level<$row['lev']){
					$stackTrees[$level]=&$tempTree;
					$last=count($tempTree)-1;
					$tempTree=&$stackTrees[$level][$last]['tree'];
					$level=$row['lev'];
				}elseif($level>$row['lev']){
					$level=$row['lev'];
					$tempTree=&$stackTrees[$level];
				}
				$tempTree[]=$arr;
			}
			return $tree;
		}
		
		public function getOrderNum($id){
			if ($this->orderField) return parent::getOrderNum($id);
			
			list($pLeft, $pRight, $pLev)=$this->getParentNode($id);
			$this->DB->select("select $this->idField as idf from $this->table as tree $pLeft<=tree.$this->leftField and $pRight>=tree.$this->rightField and $pLev=tree.$this->levelField-1 order by $this->leftField");
			
			$i=0;
			while ($row=$this->DB->fetchAssoc()){
				if ($id==$row['idf']){
					return $i;
				}
				$i++;
			}
		}
		
		public function setOrderNum($id, $orderNum, $haveNames=DBTree::NO_NAME){
			if ($this->orderField) parent::setOrderNum($id, $orderNum, $haveNames);
			
			$idChild=$this->getIdByName($id, $haveNames);

			list($left, $right, $lev)=$this->getNode($idChild);
			list($pLeft, $pRight, $pLev)=$this->getParentNode($idChild);
			
			//Находим идентификаторы перемещаемого узла и потомков, чтобы исключить их из перемещения.
			$delta=$right-$left+1;
			$ids=$this->DB->getColumn("select $this->idField from $this->table where $this->leftField>=$left and $this->rightField<=$right");
			$ids="(".implode(',', $ids).")";
			
			$newOrder=intval($orderNum);
			if ($newOrder<1){ //Если перемещаем в конец
				$childRight=$pRight-1;
				$childLeft=$childRight-$delta+1;
			}elseif($newOrder==1){ //Если перемещаем в начало
				$childLeft=$pLeft+1;
				$childRight=$childLeft+$delta-1;
			}else{
				//Выбор всех братьев.
				$this->DB->select("select $this->leftField as lf, $this->rightField as rf, $this->levelField as lev from $this->table as tree 
					where $pLeft<=tree.$this->leftField and $pRight>=tree.$this->rightField and $pLev=tree.$this->levelField-1 order by $this->leftField");
			
				//Определяем правую и левую границу брата, занимающего нужный нам порядковый номер.
				$i=1;
				$tLeft=$pLeft;	
				$tRight=$pRight;
				while ($row=$this->DB->fetchAssoc()){
					$tLeft=$row['lf'];
					$tRight=$row['rf'];
					if ($i==$orderNum){
						$childLeft=$tLeft;
						$childRight=$tRight;
						break;					
					}
					$i++;
				}
				
				if (!$childLeft){
					if ($i==1){ //Если родитель без потомков
						$childLeft=$tRight-$delta;
						$childRight=$tRight-1;
					}else{ //Если элементов менгше чем указанные номер
						$childLeft=$tLeft;
						$childRight=$tRight;
					}
				}
			}
			
			$moveDelta=0;
			if($left-$childLeft>0){ //Если сдвигаем справа налево...
				$moveDelta=$left-$childLeft; // ...то найденная левая граница брата станет новой левой границей элемента.
				if($moveDelta==0) return;
				$this->DB->update("update $this->table set $this->leftField=$this->leftField+$delta, $this->rightField=$this->rightField+$delta where $this->rightField<$left and $this->leftField>=$childLeft and $this->idField not in $ids");
			}else{ //Если сдвигаем слева направо...
				$moveDelta=$right-$childRight; // ...то найденная правая граница брата станет новой правой границей элемента
				if($moveDelta==0) return;
				$this->DB->update("update $this->table set $this->leftField=$this->leftField-$delta, $this->rightField=$this->rightField-$delta where $this->leftField>$right and  $this->rightField<=$childRight and $this->idField not in $ids");
			}
			$this->DB->update("update $this->table set $this->leftField=$this->leftField-($moveDelta), $this->rightField=$this->rightField-($moveDelta) where $this->idField in $ids");
		}
		
		
		/**
		 * Возвращает значение середины дерева.
		 * 
		 * @return int Середина дерева.
		 */
		protected function getCenter(){
			list($min, $max)=$this->DB->getRow("select $this->leftField, $this->rightField
				from $this->table where $this->idField=$this->rootId");
			return ($max+$min)/2;
		}
		
		/**
		 * Возвращает информацию об узле по идентификатору.
		 * 
		 * @param mixed $id Идентификатор узла.
		 */
		protected function getNode($id){
			return $this->DB->getRow("select $this->leftField as lf, $this->rightField as rf, $this->levelField as lev from $this->table where $this->idField=$id");;
		}

		/**
		 * Возвращает информацию о родителе по идентификатору.
		 * 
		 * @param mixed $id Идентификатор узла.
		 */
		protected function getParentNode($id){
			return $this->DB->getRow("select $this->leftField as lf, $this->rightField as rf, $this->levelField as lev from $this->table as tree join (select $this->leftField as lf, $this->rightField as rf, $this->levelField as lev from $this->table where $this->idField=$id)as t on
					t.lf>tree.$this->leftField and t.rf<tree.$this->rightField and t.lev=tree.$this->levelField+1");
		}
		
		/**
		 * Подгтавливает дерево к основному действию.
		 * 
		 * Основная задача метода - передвинуть узлы и расширить/сократить область предка.
		 * Это нужно, для того чтобы освободить место для нового узла (или занять вакуум в случае удаления/перемещения узла).
		 * Метод пытается определить с какой стороны от целевого места меньше узлов и изменяет именно эту сторону. 
		 * 
		 * @param int $left Левая граница элемента.
		 * @param int $right Правая граница элемента.
		 * @param int $delta На сколько сдвигать узлы.
		 * @param int $direction Что делать сжимать или расширять дерево.
		 * @param string $ids Строка с идентификаторами, которые не должны подвергнуться изменениям.
		 * @return int Левая граница потомка.
		 */
		protected function prepareTree($left, $right, $delta, $direction=NSTree::EXPAND, $ids=null){
			$center=$this->getCenter();
			
			/*От направления зависит последовательность комманд update*/
			if ($direction==NSTree::EXPAND){
				$andWhere=$ids ? "and $this->idField not in $ids" : '';
				if ($left>=$center){
					//Сдвигаем все узлы, которые находятся справа от нужной точки вправо. 
					$this->DB->update("update $this->table set $this->leftField=$this->leftField+$delta, $this->rightField=$this->rightField+$delta where $this->leftField>=$left $andWhere");
					//Расширяем область предков за счет смещения правой границы. 
					$this->DB->update("update $this->table set $this->rightField=$this->rightField+$delta where $this->leftField<$left and $this->rightField>=$left $andWhere");
				}else{
					//Сдвигаем все узлы, которые находятся слева от нужной точки влево. 
					$this->DB->update("update $this->table set $this->leftField=$this->leftField-$delta, $this->rightField=$this->rightField-$delta where $this->rightField<$left $andWhere");
					//Расширяем область предков за счет смещения левой границы. 
					$this->DB->update("update $this->table set $this->leftField=$this->leftField-$delta where $this->leftField<$left and $this->rightField>=$left $andWhere");
					$left=$left-$delta;
				}
				return $left;
			}else{	
				if ($right>=$center){
					//Сжимаем область предков за счет смещения правой границы. 
					$this->DB->update("update $this->table set $this->rightField=$this->rightField-$delta where $this->leftField<$left and $this->rightField>$right");
					//Сдвигаем все узлы, которые находятся справа от нужной точки влево. 
					$this->DB->update("update $this->table set $this->leftField=$this->leftField-$delta, $this->rightField=$this->rightField-$delta where $this->leftField>$right");
				}else{
					//Сжимаем область предков за счет смещения левой границы. 
					$this->DB->update("update $this->table set $this->leftField=$this->leftField+$delta where $this->leftField<$left and $this->rightField>$right");
					//Сдвигаем все узлы, которые находятся слева от нужной точки вправо. 
					$this->DB->update("update $this->table set $this->leftField=$this->leftField+$delta, $this->rightField=$this->rightField+$delta where $this->rightField<$left");
				}
			}
		}
		
		/**
		 * Возвращает значение левой границы вставляемого элемента.
		 * 
		 * @param int $orderNum Требуемый порядковый номер.
		 * @param int $pLeft Левая граница родителя.
		 * @param int $pRight Правая граница родителя.
		 * @param int $pLev ровень родителя.
		 * @return int Левая граница потомка.
		 */
		protected function getBorderByNum($orderNum, $pLeft, $pRight, $pLev){
			if (!$orderNum) return $pRight;
			
			$this->DB->select("	select $this->leftField as lf, $this->rightField as rf, $this->levelField as lev from $this->table as tree 
					where $pLeft<=tree.$this->leftField and $pRight>=tree.$this->rightField and $pLev=tree.$this->levelField-1 order by $this->leftField");
			
			$i=1;
			$childLeft=null;
			$tLeft=$pLeft;					
			$tRight=$pRight;
			while ($row=$this->DB->fetchAssoc()){
				$tLeft=$row['lf'];
				if ($i==$orderNum){
					$childLeft=$tLeft;
					break;					
				}
				$tRight=$row['rf'];
				$i++;
			}

			if (!$childLeft){
				if ($i==1){ //Если родитель без потомков
					$childLeft=$tRight;
				}else{
					$childLeft=$tRight+1;
				}
			}
			
			return $childLeft;
		}
		
	}