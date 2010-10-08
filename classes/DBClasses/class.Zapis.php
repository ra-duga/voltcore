<?php
	/**
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @version 1.0
	 * @package classes
	 * @subpackage DBClasses
	 */
	
	/**
	 * Класс работы со строкой таблицы БД. От него наследуют классы для работы с конкретной таблицей.
	 * 
	 * Если класс будет сериализирован, то при восстановении возмется последнее используемое соединение или создасться соединение по умолчанию.
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @package classes
	 * @subpackage DBClasses
	 * @abstract
	 */
	class Zapis {
		
		/**
		 * Объект для работы с БД.
		 * @var object
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
		 * Конструктор.
		 * 
		 * @param string $table Имя таблицы, из которой беруться записи.
		 * @param mixed $id Если задан этот параметр, то запись инициализируется строчкой из таблицы с соответствующим идентификатором.
		 * @param string $idField Имя поля идентификатора.
		 * @param object $db Объект для работы с БД.
		 */
		public function __construct($table, $id=null, $idField='id', $db=null){
			if (is_array($id)){
				$dbId=$id;
			}elseif(!is_null($id)){
				$dbId=$id+0;
			}
			$this->db=$db ? $db : SQLDBFactory::getDB(); 
			$this->table=$table;
			$this->idField=$idField;
			if(!is_null($id)){
				$this->select($dbId);
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
			return $this->fields[$var];
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
			return array("table", "idField", "fields", "emptyFields");
		}
		
		/**
		 * Восстановление соединения при десериализации.
		 */
		public function __wakeup(){
			$this->db=SQLDBFactory::getDB(); 
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
		 * Вставка записи в таблицу.
		 * 
		 * Вставляет текущую запись в таблицу. 
		 * Затем выбирает ее в текущий объект.
		 * Это нужно для заполнения значений по умолчанию и прочих значений, которые выставляются самой СУБД. 
		 */
		public function insert(){
			$fields=$this->fields;
			if (isset($fields[$this->idField])){
				unset($fields[$this->idField]);
			}
			$newId=$this->db->insert($fields, $this->table);
			$this->select($newId+0);
		}
		
		/**
		 * Обновляет информацию в таблице в соответствии с данными объекта.
		 */
		public function update(){
			$fields=$this->fields;
			if (isset($fields[$this->idField])){
				$id=$fields[$this->idField];
				unset($fields[$this->idField]);
			}
			$this->db->update($fields, $this->table, array($this->idField=>$id));
			$fields[$this->idField]=$id;
		}
		
		/**
		 * Обновляет существующую запись или вставляет новую если запись не существует.
		 */
		public function insertOrUpdate(){ 
			if ($this->id==-1){
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
			$this->db->delete($this->table, array($this->idField=>$this->id));
		}
		
		/**
		 * Определяет определен ли объект записью из базы.
		 * 
		 * @return bool true - если объект соответствует записи в базе, false - в противном случае.
		 */
		public function exists(){
			return $this->id!=-1;
		}
		
	}
