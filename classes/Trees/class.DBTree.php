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
	 * Абстрактный класс работы с деревьями. От него наследуют классы для работы с конкретным способом хранения дерева.
	 * 
	 * <p>Предполагается, что существуют две таблицы. Первая - таблица имен. В ней хранятся идентификаторы, имена и порядок сортировки узлов дерева.
	 * Вторая - собственно таблица с деревом.</p> 
	 * <p>Если используется возможность идентифицировать узел по имени в таблице имен, то имя в таблице имен должно быть уникальным.</p>
	 * <p>Сортировка:</p>
	 * <p>Элементам присваивается номер по-порядку в пределах одного родителя.</p>
	 * 
	 * @TODO Больше возможностей (выбор пути, выбор уровня) 
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @category VoltCore
	 * @package VoltCoreClasses
	 * @subpackage Tree
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
		 * Увеличивать порядковые номера.
		 * @var int
		 */
		const MOVE_DOWN=0;
		
		/**
		 * Уменьшать  порядковые номера.
		 * @var int
		 */
		const MOVE_UP=1;
		
		/**
		 * Сортировать по возрастанию.
		 * @var int
		 */
		const SORT_ASC=0;

		/**
		 * Сортировать по убыванию.
		 * @var int
		 */
		const SORT_DESC=1;
		
		
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
		 * Имя поля, в котором содержатся идентификаторы узлов таблицы имен.
		 * @var string
		 */
		protected $idNameField;
		
		/**
		 * Имя поля, по которому происходит сортировка. 
		 * @var string
		 */
		protected $orderField;
		
		/**
		 * Идентификатор корня.
		 * @var mixed
		 */
		protected $rootId;
		
		/**
		 * Объект для работы с БД
		 * @var SQLDB
		 */
		protected $DB;
		
		/**
		 * По какому полю сортировать.
		 * @var string
		 */
		static protected $sort;
		
		/**
		 * Как сортировать. 
		 * @var int
		 */
		static protected $direction;
	
		/**
		 * Сравнивает узлы дерева.
		 * 
		 * @param array $a Первый узел.
		 * @param array $b Второй узел.
		 * @return int 	 0 - если узлы равны по сортировочному параметру,
		 * 				-1 - если узел $a находится раньше узла $b,
		 * 				 1 - если узел $a находится позже узла $b.
		 */
		protected static function compareLeafs($a,$b){
			$f=self::$sort;
			if($a[$f]==$b[$f]){
				return 0;
			}elseif($a[$f]>$b[$f]){
				if (self::$direction==DBTree::SORT_ASC){
					return 1;
				}else{
					return -1;
				}
			}else{
				if (self::$direction==DBTree::SORT_ASC){
					return -1;
				}else{
					return 1;
				}
			}
			return $rez;
		}
		
		/**
		 * Сортирует дерево.
		 * 
		 * @param array $tree Массив с деревом.
		 * @param string $field Поле, по которому идет сортировка.
		 * @param string $direction Поле, по которому идет сортировка.
		 * @return array Отсортированное дерево.
		 */
		public static function sortTree($tree, $field='name', $direction=DBTree::SORT_ASC){
			self::$sort=$field;
			self::$direction=$direction;
			if (!is_array($tree)) logVar($tree);
			if(!count($tree)) return;
			foreach($tree as $k=>$subTree){
			    if($subTree['tree']){
      				$tree[$k]['tree']=DBTree::sortTree($subTree['tree'], $field, $direction);
  				}
			}
			usort($tree,array('DBTree', 'compareLeafs'));
			return $tree;
		} 
		
		/**
		 * Конструктор.
		 * 
		 * @param array $arrNames Массив с именами.
		 * @param SQLDB $DBCon Объект для работы с БД.
		 */
		public function __construct($arrNames, $DBCon=null){
			$this->DB=$DBCon ? $DBCon : SQLDBFactory::getDB();
			$this->assignNames($arrNames);
			$this->findRoot();
		}
		
		/**
		 * Записывает имена таблиц и полей.
		 * 
		 * @param array $arrNames Массив с именами. Обрабатываются поля:
		 * 		table Таблица, в которой лежит дерево.
		 * 		idField Имя поля с идентификаторами.
		 * 		nameTable Имя таблицы, в которой содержатся имена узлов. 
		 * 		idNameField Имя поля, в котором содержатся идентификаторы узлов в таблице имен.
		 * 		nameField Имя поля, в котором содержатся имена узлов.
		 * 		orderField Имя поля, по которому происходит сортировка.
		 * 		idPrefix Префикс для добавления к идентификаторам узлов.
		 */
		protected function assignNames($arrNames){
			$this->table=$this->DB->escapeKeys($arrNames['table']);
			$this->idField=$this->DB->escapeKeys($arrNames['idField']);
			$this->nameTable=(isset($arrNames['nameTable']) && $arrNames['nameTable']) ? $this->DB->escapeKeys($arrNames['nameTable']) : null;
			$this->idNameField=(isset($arrNames['idNameField']) && $arrNames['idNameField']) ? $this->DB->escapeKeys($arrNames['idNameField']) : null;
			$this->nameField=(isset($arrNames['nameField']) && $arrNames['nameField']) ? $this->DB->escapeKeys($arrNames['nameField']) : null;
			$this->orderField=(isset($arrNames['orderField']) && $arrNames['orderField']) ? $this->DB->escapeKeys($arrNames['orderField']) : null;
			$this->idPrefix=(isset($arrNames['idPrefix']) && $arrNames['idPrefix']) ? $arrNames['idPrefix'] : '';
		}
		
		/**
		 * Определяет идентификатор корня дерева.
		 */
		abstract protected function findRoot();
		
		/**
		 * Возвращает запрос для выбора идентифкаторов всех непосредственных потомков родителя $idParent.
		 * 
		 * @param mixed $idParent Идентификатор родителя.
		 * @return string Запрос для выбора потомков. 
		 */
		abstract protected function getChildsQuery($idParent);
		
		/**
		 * Возвращает следующий порядковый номер для родителя $idParent.
		 * 
		 * @param mixed $idParent Идентификатор родителя.
		 * @return int Следующий порядковый номер. 
		 */
		abstract protected function getFamilyNextNum($idParent);
		
		/**
		 * Выполняет запросы для вставки нового листа в дерево.
		 *
		 * @param int $idChild Идентификатор того, у кого меняем родителя.
		 * @param int $idParent Идентификатор нового родителя.
		 * @param int $orderNum Номер вставляемого узла по-порядку для сортировки.
		 * @return array Запросы для вставки нового листа в дерево.
		 */
		abstract protected function doAddInsert($idChild, $idParent, $orderNum=null);

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
		 * @param int $orderNum Номер вставляемого узла по-порядку для сортировки.
		 * @throws SqlException При ошибке работы с базой.
		 */
		abstract protected function doChangePar($idChild, $idParent, $orderNum=null);

		/**
		 * Выполняет запросы для удаления поддерева.
		 * 
		 * @param int $idChild Идентификатор того, у кого меняем родителя.
		 * @throws SqlException При ошибке работы с базой.
		 */
		abstract protected function doDeleteSubTree($idChild);
		
		/**
		 * Вытаскивает дерево из БД и создает соответствующий массив. 
		 * 
		 * @param array $extraFields дополнительные поля из таблицы с именами.
		 * 		Ключи массива - псевдонимы полей, которые станут ключами результирующего массива.
		 * 		Значения полей массива - имена полей для выборки или подзапрос типа (select smth from table where ...).   
		 * @param mixed $id Идентификатор корня поддерева. Если не указан, то возвращается все дерево.
		 * @param int $haveNames Определяет, какие параметры считать именами.
		 * @return array Массив с деревом. 
		 * 		Индексами этого массива является порядковый номер узла в уровне, начиная с 0, без пропусков.
		 * 		Узел – это массив, в которм содержатся следующие элементы:
		 * 			id – идентификатор узла дерева
		 * 			name – имя узла дерева
		 * 			поля переданные в $extraFields со значениями из БД
		 * 			tree – список дочерних узлов для этого узла. Если у этого узла нет дочерних узлов, то здесь содержится пустой массив.
		 * @throws SqlException При ошибке работы с базой.
		 * @throws FormatException Если указаны не все поля.
		 */
		public abstract function getTree($extraFields=null, $subTreeRoot=null, $haveNames=DBTree::NO_NAME);
		
		/**
		 * Определяет передано ли имя ребенка или его идентификатор.
		 * 
		 * @param int $haveNames Определяет, какие параметры считать именами.
		 * @return bool true - если указано, что передано имя ребенка, false - в противном случае. 
		 */
		protected function haveChildName($haveNames){
			return $haveNames & DBTree::CHILD_NAME;
		}

		/**
		 * Определяет передано ли имя родителя или его идентификатор.
		 * 
		 * @param int $haveNames Определяет, какие параметры считать именами.
		 * @return bool true - если указано, что передано имя родителя, false - в противном случае. 
		 */
		protected function haveParentName($haveNames){
			return $haveNames & DBTree::PARENT_NAME;
		}
		
		/**
		 * Возвращает идентификатор по имени.
		 * 
		 * Если в $haveNames указано, что передано имя, то метод вернет идентификатор записи по данному имени,
		 * иначе метод вернет переданное имя.
		 * 
		 * @param mixed $name Имя, по которому нужно определить идентификатор.
		 * @param int $haveNames Определяет, какие параметры считать именами.
		 * @param bool $child Кого искать ребенока(true) или родителя(false).
		 * @return mixed Идентификатор записи.
		 * @throws SqlException При ошибке работы с базой.
		 */
		protected function getIdByName($name, $haveNames, $child=true){
			$id=$name;
			$dbId=$this->DB->escapeString($name);
			$sql="select id from $this->nameTable where $this->nameField=$dbId";
			if ($child){
				if ($this->haveChildName($haveNames)){
					$id=$this->DB->getVal($sql);
				}
			}else{
				if ($this->haveParentName($haveNames)){
					$id=$this->DB->getVal($sql);
				}
			}
			if (is_null($id) || $id===false) throw new SqlException("Идентификатор не найден","Нет данных",$sql);
			return $id;
		}

		/**
		 * Возвращает номер по-порядку для узла с идентификатором $id.
		 * 
		 * @param mixed $idParent Идентификатор узла.
		 * @return int Номер узла по-порядку.
		 * @throws SqlException При ошибке работы с базой.
		 */
		public function getOrderNum($id){
			if (!$this->nameTable || !$this->idNameField || !$this->orderField) throw new FormatException("Недостаточно данных.","Указаны не все данные");
			$orderNum=$this->DB->getVal("select $this->orderField from $this->nameTable where $this->idNameField=$id");
			return intval($orderNum);
		}
	
		/**
		 * Подготовливает таблицу для вставки нового элемента или изменения существующего порядка.  
		 * 
		 * @param mixed $idParent Идентификатор родителя, того элемента для которого подготавливается новый порядок
		 * @param int $orderNum Новый желаемый порядковый номер потомка. Если не передан, то вычисляется.
		 * @return int Новый порядковый номер потомка.
		 * @throws SqlException При ошибке работы с базой.
		 */
		protected function prepareForNewOrder($idParent, $orderNum=null){
			$sorder=intval($orderNum);
			if ($sorder>0){
				$this->familyMove($idParent, $sorder);
			}else{
				$sorder=$this->getFamilyNextNum($idParent);
			}
			return $sorder;
		}
		
		/**
		 * Изменяет порядковый номер $count элементов с позиции $startNum на $positions позиций у родителя $idParent.
		 *
		 * @param mixed $idParent Идентификатор родителя
		 * @param int $startNum С какой позиции начинать.
		 * @param int $endNum На какой позиции закончить. 
		 * @param int $direction Куда двигать записи.
		 * @param $positions На сколько позиций смещать.
		 * @throws SqlException При ошибке работы с базой.
		 */
		protected function familyMove($idParent, $startNum, $direction=DBTree::MOVE_DOWN, $endNum=null, $positions=1){
			$startNum+=0;
			$endNum+=0;
			if ($startNum==$endNum) return;
			$set='';
			$where=$this->idNameField." in (".$this->getChildsQuery($idParent).")";
			
			if ($direction==DBTree::MOVE_DOWN){
				$symbol="+";
			}else{
				$symbol="-";
			}
			
			$where.=" and $this->orderField>=$startNum";
			if ($endNum){
				$where.=" and $this->orderField<=$endNum";
			}
			$set="$this->orderField=".$this->orderField.$symbol.$positions;
			
			$this->DB->update("update $this->nameTable	set $set where $where");
		}
		
		/**
		 * Вставляет запись о ребенке в таблицу имен.
		 * 
		 * @param string $idChild Имя ребенка.
		 * @param mixed $idParent Идентификатор родителя.
		 * @param int $orderNum Номер ребенка по порядку.
		 * @return string Идентификатор ребенка.
		 * @throws SqlException При ошибке работы с базой.
		 */
		protected function insertChild($idChild,$idParent=null, $orderNum=null){
			if ($this->orderField){
				$sorder=$this->prepareForNewOrder($idParent, $orderNum);
				$idChild=$this->DB->insert("insert into $this->nameTable($this->nameField, $this->orderField) values($idChild, $sorder)");
			}else{
				$idChild=$this->DB->insert("insert into $this->nameTable($this->nameField) values($idChild)");
			}
			return $idChild;
		}
		
		/**
		 * Возврщает строку для выборки дополнительных полей.
		 * 
		 * @param array $extraFields дополнительные поля из таблицы с именами.
		 * 		Ключи массива - псевдонимы полей, которые станут ключами результирующего массива.
		 * 		Значения полей массива - имена полей для выборки или подзапрос типа (select smth from table where ...).   
		 * @return string Строка для выборки дополнительных полей.
		 */
		protected function extraFieldsToQueryString($extraFields){
			if(!$extraFields || !is_array($extraFields)) return '';
			$rez="";
			foreach($extraFields as $name=>$field){
				if (strPos($field, "(")===0){
					$rez .= ", $field as $name";
				}else{
					$rez .= ", c.$field as $name";
				}
			}
			return $rez;
		}
		
		/**
		 * Добавляет новый лист в дерево.
		 *  
		 * @param mixed $id Идентификатор потомка или уникальная строка для вставки в таблицу имен.
		 * @param mixed $parId Идентификатор родителя или уникальная строка для поиска в таблице имен.
		 * @param int $haveNames Определяет, какие параметры считать именами.
		 * @param int $orderNum Номер нового узла по-порядку для сортировки.
		 * @throws SqlException При ошибке работы с базой.
		 * @throws FormatException Если указаны не все поля.
		 */
		public function add($id, $parId, $haveNames=DBTree::NO_NAME, $orderNum=null){
			if ($haveNames!=DBTree::NO_NAME && (!$this->nameTable || !$this->nameField)) throw new FormatException("Недостаточно данных.","Указаны не все данные");
			$DB=$this->DB;
			try{
				$DB->startTran();
				
				$idChild=$DB->escapeString($id);
				$idParent=$this->getIdByName($parId, $haveNames, false);

				if ($this->haveChildName($haveNames)){
					$idChild=$this->insertChild($idChild,$idParent,$orderNum);
				}

				$insert=$this->doAddInsert($idChild, $idParent, $orderNum);
				
				$DB->commit();
			}catch(SqlException $e){
				$DB->rollback();
				throw $e;
			}
		}
			
		/**
		 * Устанавливает номер узла по-порядку для сортировки.
		 * 
		 * @param mixed $id Идентификатор потомка или уникальная строка из таблицы имен.
		 * @param int $orderNum Номер нового узла по-порядку для сортировки.
		 * @param int $haveNames Определяет, какие параметры считать именами.
		 * @throws SqlException При ошибке работы с базой.
		 * @throws FormatException Если указаны не все поля.
		 */
		public function setOrderNum($id, $orderNum, $haveNames=DBTree::NO_NAME){
			if (!$this->nameTable || !$this->nameField || !$this->orderField) throw new FormatException("Недостаточно данных.","Указаны не все данные");
			
			$idChild=$this->getIdByName($id, $haveNames);
			$idParent=$this->getParent($idChild);

			$newOrder=intval($orderNum);
			if ($newOrder<1){
				$newOrder=$this->getFamilyNextNum($idParent)-1;
			}
			
			$oldOrder=$this->getOrderNum($idChild);
			if ($oldOrder==$newOrder) return;
			
			$DB=$this->DB;
			try{
				$DB->startTran();
				
				if ($oldOrder<$newOrder){
					$this->familyMove($idParent, $oldOrder, DBTree::MOVE_UP, $newOrder);
				}else{
					$this->familyMove($idParent, $newOrder, DBTree::MOVE_DOWN, $oldOrder);
				}
				
				$DB->update("update $this->nameTable set $this->orderField=$newOrder where $this->idNameField=$idChild");

				$DB->commit();
			}catch(SqlException $e){
				$DB->rollback();
				throw $e;
			}
		}
		
		/**
		 * Изменяет порядковый номер узла.
		 * 
		 * @param mixed $id Идентификатор узла.
		 * @param int $positions На сколько позиций перемещать.
		 * @param int $direction Куда перемещать.
		 * @param int $haveNames Определяет, какие параметры считать именами.
		 */
		public function moveNode($id, $positions=1, $direction=DBTree::MOVE_DOWN, $haveNames=DBTree::NO_NAME){
			$idChild=$this->getIdByName($id, $haveNames);
			$oldNum=$this->getOrderNum($idChild);
			if ($direction==DBTree::MOVE_UP){
				$newNum=$oldNum-$positions;
				$newNum=max($newNum,1);
			}else{
				$newNum=$oldNum+$positions;
			}
			$this->setOrderNum($idChild, $newNum);
		}
		
		/**
		 * Меняет родителя у узла.
		 * 
		 * @param mixed $id Идентификатор того, у кого меняем родителя.
		 * @param mixed $parId Идентификатор нового родителя.
		 * @param int $haveNames Определяет, какие параметры считать именами.
		 * @param int $orderNum Номер узла по-порядку для сортировки.
		 * @throws SqlException При ошибке работы с базой.
		 * @throws FormatException Если указаны не все поля.
		 */
		public function changePar($id, $parId, $haveNames=DBTree::NO_NAME, $orderNum=null){
			if ($haveNames!=DBTree::NO_NAME && (!$this->nameTable || !$this->nameField)) throw new FormatException("Недостаточно данных.","Указаны не все данные");
			$DB=$this->DB;
			try{
				$idChild=$this->getIdByName($id, $haveNames);
				$idParent=$this->getIdByName($parId, $haveNames, false);
				$oldParent=$this->getParent($idChild);
				
				if ($idChild==$idParent){
					throw new FormatException("Нельзя замкнуть узел на себя.","Неверные данные");
				}
				if ($oldParent==$idParent){
					if ($orderNum){
						$this->setOrderNum($idChild, $orderNum);
					}
					return;
				}
				
				$DB->startTran();
				if ($this->orderField){
					$oldNum=$this->getOrderNum($idChild);
					$this->familyMove($oldParent, $oldNum+1, DBTree::MOVE_UP);
					$sorder=$this->prepareForNewOrder($idParent, $orderNum);
					$this->DB->update("update $this->nameTable set $this->orderField=$sorder where $this->idNameField=$idChild");
				}
				
				$this->doChangePar($idChild, $idParent,$orderNum);
				
				
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
			$DB=$this->DB;
			try{
				$idChild=$this->getIdByName($id,$haveNames);
				if ($idChild==$this->rootId) throw new FormatException('Нельзя удалить корень дерева','Неверные данные');
				
				if ($this->orderField){
					$oldParent=$this->getParent($idChild);
					$oldNum=$this->getOrderNum($idChild);
					$this->familyMove($oldParent, $oldNum+1, DBTree::MOVE_UP);
				}
				
				$DB->startTran();
				$this->doDeleteSubTree($idChild);
				
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
		 * @return string Идентификатор предка.
		 * @throws SqlException При ошибке работы с базой.
		 */
		public function getParent($id, $haveNames=DBTree::NO_NAME){
			if (!$this->nameTable || !$this->nameField) throw new FormatException("Недостаточно данных.","Указаны не все данные");

			$idChild=$this->getIdByName($id, $haveNames);
			if($idChild==$this->rootId) return $this->rootId;
			
			$select=$this->getSelectParent($idChild);
			$pid=$this->DB->getVal($select);

			if (is_null($pid) || $pid===false) throw new SqlException("Идентификатор не найден","Нет данных",$select);
			return $pid;
		}
	} 