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
	 * Класс работы с СУБД Microsoft SQL Server
	 * 
	 * Класс предназначен для работы с СУБД Microsoft SQL Server. 
	 * Использует odbtp функции.
	 * Проверялось на MS SQL Server 2005.
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @category VoltCore
	 * @package VoltCoreClasses
	 * @subpackage DBAdapters
	 */
	class MSSQLDB extends SQLDB{

		/**
		 * Символ для обрамления ключа слева.
		 * 
		 * Символ для обрамления ключа слева(Left Key Symbol).  
		 * Используется в {@link escapeKeys}
		 * @access protected
		 * @var string
		 */
		protected $LKS="[";
		
		/**
		 * Символ для обрамления ключа справа.
		 * 
		 * Символ для обрамления ключа справа(Right Key Symbol).  
		 * Используется в {@link escapeKeys}
		 * @access protected
		 * @var string
		 */
		protected $RKS="]";
		
		/**
		 * Метод реализует шаблон Singleton.
		 * 
		 * @access protected
		 * @param array $config Конфигурация подключения к БД
		 * @return object Объект для работы с БД
		 */
		static public function getDB($config){
			if (!self::$instance){
				self::$instance = new self($config);
			}
			return self::$instance;
		}
		
		/**
		 * Создает объект для работы с MS SQL Server
		 * @param array $config Конфигурация подключения к БД
		 * @return MSSQLDB Экземпляр класса
		 */
		public function __construct($config){
			$this->setConfig($config);
			$dsn="Driver={SQL Native Client};Server=$this->host;Database=$this->db;Uid=$this->login;Pwd=$this->pass";
			$this->link=odbtp_connect($this->host, $dsn);
			if (!$this->link){
				throw new SqlException("Ошибка при подключении к серверу","Ошибка подключения","Connect");
			}
			
			$temp=odbtp_set_attr(ODB_ATTR_TRANSACTIONS, ODB_TXN_NONE,$this->link);
			if (!$temp){
				throw new SqlException("Ошибка при выставлении параметра транзакций","Ошибка подключения","SET TRANSACTION ISOLATION LEVEL");
			}
			if (!$this->needEnc){
				$temp=odbtp_set_attr(ODB_ATTR_UNICODESQL, 1,$this->link);
				if (!$temp){
					throw new SqlException("Ошибка при выставлении параметра транзакций","Ошибка подключения","Нет запроса");
				}
			}
		}
		
		/**
		 * Закрывает соединение 
		 */
		protected function closeConnection(){
			odbtp_close($this->link);
		}
		
		/**
		 * Выполняет запрос $sql.
		 * 
		 * @access protected
		 * @param string $sql Запрос для выполнения
		 */
		protected function query($sql){
			return odbtp_query($sql,$this->link);
		}

		/**
		 * Возвращает код последней ошибки
		 * 
		 * @return string Код последней ошибки
		 */
		public function getErrorCode(){
			return odbtp_last_error_code($this->link);
		}
		
		/**
		 * Возвращает последнее сообщение об ошибке
		 * 
		 * @return string Сообщение об ошибке
		 */
		public function getErrorMsg(){
			return odbtp_last_error($this->link);
		}
		
		/**
		 * Возвращает id только что добавленной записи.
		 *
		 * @access protected
		 * @return int id только что добавленной записи
		 */
		protected function getLastID(){
			$newId=-1;
			return $this->getSystemVal("select @@IDENTITY as smth");
		}
		
		/**
		 * Возвращает количество строк, обработанных последним запросом.
		 *
		 * @access protected
		 * @return int Количество строк, обработанных последним запросом
		 */
		public function affectRows(){
			return $this->getSystemVal("select @@ROWCOUNT as smth");
		}

		/**
		 * Начинает транзакцию
		 *
		 * Отключает autocommit и посылает серверу команду начать транзакцию.
		 */
		public function startTran(){
			$temp=odbtp_set_attr(ODB_ATTR_TRANSACTIONS, ODB_TXN_DEFAULT,$this->link);
			if (!$temp){
				throw new SqlException("Ошибка при выставлении параметра транзакций","Ошибка подключения","SET TRANSACTION ISOLATION LEVEL");
			}
		}

		/**
		 * Подтверждает транзакцию
		 *
		 * Подтверждает транзакцию и включает autocommit.
		 */
		public function commit(){
			odbtp_commit($this->link);
			$temp=odbtp_set_attr(ODB_ATTR_TRANSACTIONS, ODB_TXN_NONE,$this->link);
			if (!$temp){
				throw new SqlException("Ошибка при выставлении параметра транзакций","Ошибка подключения","SET TRANSACTION ISOLATION LEVEL");
			}
			
		}

		/**
		 * Откатывает транзакцию
		 *
		 * Производит откат транзакции и включает autocommit.
		 */
		public function rollback(){
			odbtp_rollback($this->link);
			$temp=odbtp_set_attr(ODB_ATTR_TRANSACTIONS, ODB_TXN_NONE,$this->link);
			if (!$temp){
				throw new SqlException("Ошибка при выставлении параметра транзакций","Ошибка подключения","SET TRANSACTION ISOLATION LEVEL");
			}
		}

		/**
		 * Возвращает очередную строку из результата запроса в виде ассоциативного массива
		 *
		 * @param resource $res Если параметр $res задан, то строка берется из него, в противном случае строка берется из $this->res
		 * @return array Ассоциативный массив, содержащий значения из очередной строки результата
		 */
		public function fetchAssoc($rez=null){
			if (!$rez){
				$rez=$this->res;
			}
			if ($this->needEnc){
				$nextRow = deepIconv($this->encDB, $this->encFile,odbtp_fetch_assoc($rez));
			}
			else{
				$nextRow=odbtp_fetch_assoc($rez);
			}
			return $nextRow;
		}

		/**
		 * Возвращает очередную строку из результата запроса в виде объекта
		 *
		 * @param resource $res Если параметр $res задан, то строка берется из него, в противном случае строка берется из $this->res
		 * @return array Объект, содержащий значения из очередной строки результата
		 */
		public function fetchObj($rez=null){
			if (!$rez){
				$rez=$this->res;
			}
			if ($this->needEnc){
				$nextRow = deepIconv($this->encDB, $this->encFile,odbtp_fetch_object($rez));
			}
			else{
				$nextRow=odbtp_fetch_object($rez);
			}
			return $nextRow;
		}

		/**
		 * Возвращает очередную строку из результата запроса в виде пронумерованного массива
		 *
		 * @param resource $res Если параметр $res задан, то строка берется из него, в противном случае строка берется из $this->res
		 * @return array Пронумерованный массив, содержащий значения из очередной строки результата
		 */
		public function fetchRow($rez=null){
			if (!$rez){
				$rez=$this->res;
			}
			if ($this->needEnc){
				$nextRow = deepIconv($this->encDB, $this->encFile,odbtp_fetch_row($rez));
			}
			else{
				$nextRow=odbtp_fetch_row($rez);
			}
			return $nextRow;
		}

		/**
		 * Возвращает единственное значение из результата запроса
		 *
		 * Этот метод следует использовать, когда ожидется выбор единственного значения
		 *
		 * @param resource $res Если параметр $res задан, то строка берется из него, в противном случае строка берется из $this->res
		 * @return mixed Значение из результата запроса
		 */
		public function fetchField($rez=null){
			if (!$rez){
				$rez=$this->res;
			}
			$nextRow=$this->fetchRow();
			$nextVal=$nextRow[0];
			
			if ($this->needEnc){
				$nextVal = iconv($this->encDB, $this->encFile,$nextVal);
			}

			return $nextVal;
		}

		/**
		 * Обрабатывает спецсимволы в строке для безопасного ее использования в запросе
		 *
		 * @param mixed $str Строка, в которой надо экранировать спецсимволы.
		 * @return mixed Строка с экранированными спецсимволы.
		 */
		public function escape($str){
			$esc=str_replace("'", "''", $str);
			$esc=str_replace("/*", "", $esc);
			$esc=str_replace("*/", "", $esc);
			return $esc;
			
		}
		
		/**
		 * Обрабатывает спецсимволы в строке для безопасного ее использования в запросе
		 *
		 * @param mixed $str Строка, в которой надо экранировать спецсимволы или число или null.
		 * @return mixed Строка с экранированными спецсимволы, или число или строка "null".
		 */
		public function escapeString($str){
			if (is_array($str)){
				foreach($str as $key=>$val){
					$str[$key]=$this->escapeString($val);
				}
				return $str;
			}
			if (is_null($str)) return "NULL";
			if (is_string($str)) {
				$esc="'".$this->escape($str)."'";
				if ($this->encDB=="utf-8") $esc="N".$esc;
				return $esc;
			}
			return $str;
		}
		
		public function getColumnsInfo($table){
			$dbTable=$this->escapeKeys($table);
			return $this->select("select c.name as fname, c.max_length as flen, t.name as tname, c.is_nullable as allowBlank,
					(SELECT value
					FROM fn_listextendedproperty('MS_Description', 'SCHEMA', 'dbo', 'table', '$table', 'column', c.name)) as descr
				from sys.columns as c join sys.types as t 
				on c.system_type_id=t.system_type_id and c.user_type_id=t.user_type_id 
				where object_id = OBJECT_ID('$dbTable')");
		}
		
		
		
	}
?>