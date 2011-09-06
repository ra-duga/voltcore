<?php
	/**
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @version 1.0
	 * @category VoltCore
	 * @package VoltCoreFiles
	 * @subpackage Classes
	 */
	
	/**
	 * Класс работы со строкой таблицы БД. От него наследуют классы для работы с конкретной таблицей.
	 * 
	 * Если класс будет сериализирован, то при восстановении возмется последнее используемое соединение или создасться соединение по умолчанию.
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @category VoltCore
	 * @package VoltCoreClasses
	 * @subpackage DBClasses
	 * @abstract
	 */
	class Zapis {
		
		/**
		 * Направление связи от другой записи к данной.
		 * @var int
		 */
		const TOZAP=0;
		
		/**
		 * Направление связи от данной записи к другой.
		 * @var int
		 */
		const FROMZAP=1;
		
		/**
		 * Связь многие ко многим.
		 * @var int
		 */
		const MMZAP=2;

		/**
		 * Запись не изменялась.
		 * @var int
		 */
		const NOCH=0;
		
		/**
		 * Запись в процессе изменения.
		 * @var int
		 */
		const CHED=1;
		
		/**
		 * Запись изменена.
		 * @var int
		 */
		const CH=2;
		
		/**
		 * Объект для работы с БД.
		 * @var SQLDB
		 */
		protected $db;
		
		/**
		 * Изменялась ли запись
		 * @var bool
		 */
		protected $changed;
		
		/**
		 * Имя таблицы, из которой беруться записи. 
		 * @var string
		 */
		protected $table;

		/**
		 * Имя поля с идентификатором. 
		 * @var string
		 */
		protected $idField;
		
		/**
		 * Массив полей записи. Ключи - имена полей.
		 * @var array
		 */
		protected $fields=array();

		/**
		 * Массив ссылок на другие записи через внешние ключи.
		 * 
		 * Содержит в себе массивы типа:
		 * 		array(
		 * 			class - имя класса той записи
		 * 			table - имя таблицы той записи
		 * 			oId - имя поля индентификатора у той записи
		 * 			otherKey - имя ключа у той записи, 
		 * 			thisKey - имя ключа у текущей записи, 
		 * 			direction - направление связи (from, to),
		 * 			val - Zapis или массив Zapis,
		 * 			mtm - связь многие ко многим array(
		 * 				table - таблица со связями,
		 * 				otherKey - имя ключа к той записи, 
		 * 				thisKey - имя ключа к текущей записи
		 * 				data - дополнительные данные, которые надо ввести в таблицу связей array(
		 * 					имя поля => значение
		 * 				)
		 * 			) 
		 * @var array
		 */
		protected $fks=array();

		/**
		 * Массив полей пустой записи. Ключи - имена полей.
		 * @var array
		 */
		protected $emptyFields=array("id"=>-1);
		
		/**
		 * Какой идентификатор использовать, тот который генерирует база(true) или пользователь(false) 
		 * @var bool
		 */
		protected $useDatabaseId;
		
		/**
		 * Какого типа поле идентификатора int(true) или string(false). 
		 * @var bool
		 */
		protected $useIntId;
		
		/**
		 * Конструктор.
		 * 
		 * @param string $table Имя таблицы, из которой беруться записи.
		 * @param mixed $id Если задан этот параметр, то запись инициализируется соответствующей строчкой из таблицы.
		 * 				Если это строка или число, то считается идентификатором записи.
		 * 				Если это массив, то ключи - имена полей, значения - значения полей. 
		 * 				По сути $id передается в {@link Zapis::select()}
		 * @param string $idField Имя поля идентификатора.
		 * @param SQLDB $db Объект для работы с БД.
		 * @param bool $useDatabaseId Какой идентификатор использовать, тот который генерирует база(true) или пользователь(false) 
		 * @param bool $useIntId Какого типа поле идентификатора int(true) или string(false). 
		 */
		public function __construct($table, $id=null, $idField='id', $db=null, $useDatabaseId=true, $useIntId=true){
			$this->db=$db ? $db : SQLDBFactory::getDB(); 
			$this->table=$table;
			$this->idField=$idField;
			$this->useDatabaseId=$useDatabaseId;
			$this->useIntId=$useIntId;
			$this->setChangedStatus(self::NOCH);
			if(!is_null($id)){
				$this->select($id);
			}			
			
		}
		
		/**
		 * Используется для ввода дополнительных данных в таблицу связей многие ко многим.
		 * 
		 * @param string $method
		 * @param array $par 0 - объект с которым связывается данны, 1 - массив в формате "имя поля"=>"значение поля"
		 */
		public function __call($method, $par){
			if (isset($this->fks[$method]) && $this->fks[$method]['direction']==self::MMZAP){
				$this->setChangedStatus(self::CH);
				$num=count($this->fks[$method]['val']);
				$this->fks[$method]['val'][$num]['val']=$par[0];
				$this->fks[$method]['val'][$num]['data']=$par[1];
			}else{
				throw new MethodNotExistsException("Метод или связь $method не сущестует в классе ".get_class($this));
			}
		}
		
		/**
		 * Магическое получение значения поля.
		 * 
		 * @param string $var Имя поля
		 * @return mixed Значение поля
		 */
		public function __get($var){
			$method="get".ucfirst($var);
			if (method_exists($this, $method)){
				return $this->$method();
			}
			
			if (isset($this->fks[$var])){
				$this->setChangedStatus(self::CH);
				$oKey=$this->fks[$var]['otherKey'];
				$tKey=$this->fks[$var]['thisKey'];
				$class=$this->fks[$var]['class'];
				if (!$this->fks[$var]['val']){
					if ($this->fks[$var]['direction']==self::FROMZAP){
						$this->fks[$var]['val']=new $class(array($oKey=>$this->$tKey),$this->db);
						if (!$this->fks[$var]['val']->exists()){ 
							$this->fks[$var]['val']=null;
						}
					}elseif ($this->fks[$var]['direction']==self::TOZAP){
						$idKey=$this->fks[$var]['oId'];
						$oTable=$this->fks[$var]['table'];
						$tKeyVal=$this->$tKey;
						$ids=$this->db->getColumn("select $idKey from $oTable where $oKey=$tKeyVal");
						foreach($ids as $itemId){
							$this->fks[$var]['val'][]=new $class(array($idKey=>$itemId),$this->db);
						}
					}elseif ($this->fks[$var]['direction']==self::MMZAP){
						$idKey=$this->fks[$var]['oId'];
						$oTable=$this->fks[$var]['table'];
						$tKeyVal=$this->$tKey;
						$dopTKey=$this->fks[$var]['mtm']['thisKey'];
						$dopOKey=$this->fks[$var]['mtm']['otherKey'];
						$dopTable=$this->fks[$var]['mtm']['table'];
						
						$ids=$this->db->getColumn("select $idKey from $oTable join $dopTable on $oTable.$oKey=$dopTable.$dopOKey where $dopTKey=$tKeyVal");
						foreach($ids as $itemId){
							$this->fks[$var]['val'][]=new $class(array($idKey=>$itemId),$this->db);
						}
					}
				}
				return $this->fks[$var]['val'];
			} 
			if (isset($this->fields[$var])) return $this->fields[$var];
			
			return null;
		}
		
		/**
		 * Магическая запись значения поля.
		 * 
		 * @param string $var Имя поля
		 * @param mixed $val Значение поля
		 */
		public function __set($var, $val){
			$this->setChangedStatus(self::CH);
			$method="set".ucfirst($var);
			if (method_exists($this, $method)){
				$this->$method($val);
			}elseif (isset($this->fks[$var])){
				$oKey=$this->fks[$var]['otherKey'];
				$tKey=$this->fks[$var]['thisKey'];
				if ($this->fks[$var]['direction']==self::TOZAP){
					$this->fks[$var]['val'][]=$val;
					if (isset($this->fields[$tKey])){
						$val->$oKey=$this->fields[$tKey];
					}else{
						$val->$oKey=null;
					}
				}elseif ($this->fks[$var]['direction']==self::FROMZAP){
					$this->fks[$var]['val']=$val;
					$this->fields[$tKey]=$val->$oKey;
				}else{
					$this->fks[$var]['val'][]=$val;
				}
			}else{
				$this->fields[$var]=$val;
			}
		}
		
		/**
		 * Подготовка с сериализации
		 */
		public function __sleep(){
			return array("table", "idField", "fks","fields", "emptyFields", "useDatabaseId", "useIntId");
		}
		
		/**
		 * Восстановление соединения при десериализации.
		 */
		public function __wakeup(){
			$this->db=SQLDBFactory::getDB(); 
		}
		
		/**
		 * Возвращает md5 хеш записи. 
		 * 
		 * @return string md5 хеш записи.
		 */
		public function md5Fields(){
			return md5(serialize($this->fields));
		}
		
		/**
		 * Сравнивает две записи по их хешам.
		 * 
		 * @param mixed $hash Другая запись или хеш другой записи.
		 * @return true - если хеши (а значит и записи) равны, false - в противном случае.
		 */
		public function equalByHash($hash){
			if ($hash instanceof Zapis){
				$hash=$hash->md5Fields();
			}
			return $hash===$this->md5Fields();
		}
		
		/**
		 * Сбрасывает данные о полях записи.
		 */
		public function reset(){
			$this->fields=array();
			$this->setChangedStatus(self::CH);
		}
		
		/**
		 * Выбор записи по идентификатору
		 * 
		 * @param mixed $fields Если это строка или число, считается идентификатором записи.
		 * 					Если это массив, то ключи - имена полей, значения - значения полей.
		 */
		public function select($fields){
			if (is_null($fields) || is_object($fields)){
				$this->fields=$this->emptyFields;
				return;
			}
			if (!is_array($fields)){
				$dbFields=array($this->idField=>$fields);
			}else{
				$dbFields=$fields;
			}
			$row=$this->db->getAssoc(array("*"), $this->table, $dbFields);
			$this->fields= $row ? $row : $this->emptyFields;
			$this->setChangedStatus(self::NOCH);
		}
		
		/**
		 * Устанавливает значения по умолчанию.
		 */
		public function selectDefault(){
			$this->fields=$this->emptyFields;
			foreach($this->fks as $k=>$v){
				$this->fks[$k]['val']=null;
			}
			$this->setChangedStatus(self::NOCH);
		}

		/**
		 * Вставка записи в таблицу.
		 * 
		 * Вставляет текущую запись в таблицу. 
		 * Затем выбирает ее в текущий объект.
		 * Это нужно для заполнения значений по умолчанию и прочих значений, которые выставляются самой СУБД.
		 *  
		 * @return bool true - если запрос выпонен. false - если запрос не было изменений или запись уже находится в процессе изменений
		 */
		public function insert(){
			return $this->renewData('insert');
		}
		
		/**
		 * Обновляет информацию в таблице в соответствии с данными объекта.
		 * 
		 * @return bool true - если запрос выпонен. false - если запрос не было изменений или запись уже находится в процессе изменений
		 */
		public function update(){
			return $this->renewData('update');
		}
		
		/**
		 * Обновляет данные в таблице, по сути выполняет insert или update запрос.
		 * 
		 * @param string $action Какой запрос выполнять.
		 * @return bool true - если запрос выпонен. false - если запрос не было изменений или запись уже находится в процессе изменений
		 */
		protected function renewData($action){
			if ($this->changed==self::NOCH || $this->changed==self::CHED) return false;
			$fields=$this->fields;
			if (isset($fields[$this->idField])){
				$id=$fields[$this->idField];
			}
			try{
				$this->db->startTran();
				$this->setChangedStatus(self::CHED);
				
				foreach($this->fks as $link){
					if($link['val'] && $link['direction']==self::FROMZAP){
						$link['val']->insertOrUpdate();
						$oKey=$link['otherKey'];
						$tKey=$link['thisKey'];
						$fields[$tKey]=$link['val']->$oKey;
					}
				}
			
				if ($this->useDatabaseId && isset($fields[$this->idField])){
					unset($fields[$this->idField]);
				}
				
				if ($action=='update'){
					$this->db->update($fields, $this->table, array($this->idField=>$id));
					if ($this->useDatabaseId){
						$fields[$this->idField]=$id;
					}
					$newId=$fields[$this->idField];
				}elseif ($action=='insert'){
					$newId=$this->db->insert($fields, $this->table);
					if ($this->useIntId){
						$newId=(int)$newId;		
					}
				}
				$this->select($newId);
								
				foreach($this->fks as $link){
					$oKey=$link['otherKey'];
					$tKey=$link['thisKey'];
					if($link['val'] && $link['direction']==self::TOZAP){
						foreach($link['val'] as $zap){
							$zap->$oKey=$this->fields[$tKey];
							$zap->insertOrUpdate();
						}
					}
					if($link['val'] && $link['direction']==self::MMZAP){
						if (isset($fields[$tKey])){
							$this->db->delete('delete from '.$link['mtm']['table'].' where '.$link['mtm']['thisKey'].'='.$fields[$tKey]);
						}
						foreach($link['val'] as $zap){
							$data=array();
							if (is_array($zap)){
								$val=$zap['val'];
								$data=$zap['data'];
							}else{
								$val=$zap;
							}
							$val->insertOrUpdate();
							$dbTkey=$this->db->escapeString($this->fields[$tKey]);
							$dbOkey=$this->db->escapeString($val->$oKey);
							$data=array_merge($data,array($link['mtm']['thisKey']=>$dbTkey, $link['mtm']['otherKey']=>$dbOkey));
							$this->db->insert($data, $link['mtm']['table']);
						}
					}
				}
			}catch(Exception $e){
				$this->db->rollback();
				$this->fields=$fields;
				throw $e;
			}
			$this->db->commit();
			return true;
			
		}
		
		/**
		 * Обновляет существующую запись или вставляет новую если запись не существует.
		 */
		public function insertOrUpdate(){ 
			if (!$this->exists()){
				$this->insert();
			}else{
				$this->update();
			}								
		}

		/**
		 * Выбирает существующую запись или вставляет новую если запись не существует.
		 * 
		 * @param mixed $fields Если это строка или число, считается идентификатором записи.
		 * 					Если это массив, то ключи - имена полей, значения - значения полей.
		 */
		public function selectOrInsert($fields){ 
			$this->select($fields);
			if (!$this->exists()){
				$this->fields=$fields;
				$this->setChangedStatus(self::CH);
				$this->insert();
			}								
		}
		
		/**
		 * Удаляет запись из таблицы.
		 */
		public function delete(){
			if (!$this->exists()) return false;
			try{
				$this->db->startTran();
				foreach($this->fks as $link){
					$oKey=$link['otherKey'];
					$tKey=$link['thisKey'];
					if($link['val'] && $link['direction']==self::TOZAP){
						foreach($link['val'] as $zap){
							$zap->delete();
						}
					}
					if($link['val'] && $link['direction']==self::MMZAP){
						if (isset($this->fields[$tKey])){
							$this->db->delete('delete from '.$link['mtm']['table'].' where '.$link['mtm']['thisKey'].'='.$this->fields[$tKey]);
						}
					}
				}
				$this->db->delete($this->table, array($this->idField=>$this->fields[$this->idField]));
			}catch(Exception $e){
				$this->db->rollback();
				throw $e;
			}
			$this->db->commit();
			$this->setChangedStatus(self::CH);
			return true;
			
		}
		
		/**
		 * Определяет определен ли объект записью из базы.
		 * 
		 * @return bool true - если объект соответствует записи в базе, false - в противном случае.
		 */
		public function exists(){
			$idField=$this->idField;
			return (bool)$this->fields && !is_null($this->$idField) && $this->$idField!=-1;
		}
		
		/**
		 * Устанавливает статус изменений в записи.
		 * 
		 * @param int $status Состояние записи относительно изменений.
		 */
		protected function setChangedStatus($status){
			if ($status!=self::CH || $this->changed!=self::CHED){
				$this->changed=$status;
			}
		}
		
	}
