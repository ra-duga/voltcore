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
		 * Объект для работы с БД.
		 * @var SQLDB
		 */
		protected $db;
		
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
			if(!is_null($id)){
				$this->select($id);
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
			$method="set".ucfirst($var);
			if (method_exists($this, $method)){
				$this->$method($val);
			}else{
				$this->fields[$var]=$val;
			}
		}
		
		/**
		 * Подготовка с сериализации
		 */
		public function __sleep(){
			return array("table", "idField", "fields", "emptyFields", "useDatabaseId", "useIntId");
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
		}
		
		/**
		 * Устанавливает значения по умолчанию.
		 */
		public function selectDefault(){
			$this->fields=$this->emptyFields;
		}
		/**
		 * Вставка записи в таблицу.
		 * 
		 * Вставляет текущую запись в таблицу. 
		 * Затем выбирает ее в текущий объект.
		 * Это нужно для заполнения значений по умолчанию и прочих значений, которые выставляются самой СУБД. 
		 */
		public function insert(){
			$fields=$this->fields;
			if ($this->useDatabaseId && isset($fields[$this->idField])){
				unset($fields[$this->idField]);
			}
			$newId=$this->db->insert($fields, $this->table);
			if ($this->useIntId){
				$newId=(int)$newId;		
			}
			$this->select($newId);
		}
		
		/**
		 * Обновляет информацию в таблице в соответствии с данными объекта.
		 */
		public function update(){
			$fields=$this->fields;
			$id=$fields[$this->idField];
			if ($this->useDatabaseId){
				unset($fields[$this->idField]);
			}
			$this->db->update($fields, $this->table, array($this->idField=>$id));
			if ($this->useDatabaseId){
				$fields[$this->idField]=$id;
			}
			$this->select($fields[$this->idField]);
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
				$this->insert();
			}								
		}
		
		/**
		 * Удаляет запись из таблицы.
		 */
		public function delete(){
			$this->db->delete($this->table, array($this->idField=>$this->fields[$this->idField]));
		}
		
		/**
		 * Определяет определен ли объект записью из базы.
		 * 
		 * @return bool true - если объект соответствует записи в базе, false - в противном случае.
		 */
		public function exists(){
			return (bool)$this->fields && $this->id!=-1;
		}
		
	}
