<?php

	/**
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @version 1.0
	 * @package DBClasses
	 * @subpackage Adapters
	 */
	
	/**
	 * Класс работы с СУБД MySQL
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @package DBClasses
	 * @subpackage Adapters
	 */
	class MySQLDB extends SQLDB{

		/**
		 * Символ для обрамления ключа слева.
		 * 
		 * Символ для обрамления ключа слева(Left Key Symbol).  
		 * Используется в {@link escapeKeys}
		 * @access protected
		 * @var string
		 */
		protected $LKS="`";
		
		/**
		 * Символ для обрамления ключа справа.
		 * 
		 * Символ для обрамления ключа справа(Right Key Symbol).  
		 * Используется в {@link escapeKeys}
		 * @access protected
		 * @var string
		 */
		protected $RKS="`";
		
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
		 * Создает объект для работы с MySQL
		 * @param array $config Конфигурация подключения к БД
		 * @return MSSQLDB Экземпляр класса
		 */
		public function __construct($config){
			$this->setConfig($config);

			$this->link=mysqli_connect($this->host, $this->login, $this->pass);
			if (!$this->link){
				throw new SqlException("Ошибка при подключении к серверу","Ошибка подключения","Connect");
			}
			$temp=mysqli_select_db($this->link,$this->db);
			if (!$temp){
				throw new SqlException("Ошибка при выборе БД","Ошибка подключения","Connect");
			}
			$this->exec("set names utf8", false);			
		}
		
		/**
		 * Закрывает соединение 
		 */
		protected function closeConnection(){
			mysqli_close($this->link);
		}
		
		/**
		 * Выполняет запрос $sql.
		 * 
		 * @access protected
		 * @param string $sql Запрос для выполнения
		 */
		protected function query($sql){
			return mysqli_query($this->link,$sql);
		}

		/**
		 * Возвращает код последней ошибки
		 * 
		 * @return string Код последней ошибки
		 */
		public function getErrorCode(){
			return mysqli_errno($this->link);
		}
		
		/**
		 * Возвращает последнее сообщение об ошибке
		 * 
		 * @return string Сообщение об ошибке
		 */
		public function getErrorMsg(){
			return mysqli_error($this->link);
		}
		
		/**
		 * Возвращает id только что добавленной записи.
		 *
		 * @access protected
		 * @return int id только что добавленной записи
		 */
		protected function getLastID(){
			return mysqli_insert_id($this->link);
		}
		
		/**
		 * Возвращает количество строк, обработанных последним запросом.
		 *
		 * @access protected
		 * @return int Количество строк, обработанных последним запросом
		 */
		public function affectRows(){
			return mysqli_affected_rows($this->link);
		}

		/**
		 * Начинает транзакцию
		 *
		 * Отключает autocommit и посылает серверу команду начать транзакцию.
		 */
		public function startTran(){
			mysqli_autocommit($this->link,false);
		}

		/**
		 * Подтверждает транзакцию
		 *
		 * Подтверждает транзакцию и включает autocommit.
		 */
		public function commit(){
			mysqli_commit($this->link);
			mysqli_autocommit($this->link,true);
		}

		/**
		 * Откатывает транзакцию
		 *
		 * Производит откат транзакции и включает autocommit.
		 */
		public function rollback(){
			mysqli_rollback($this->link);
			mysqli_autocommit($this->link,true);
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
				$nextRow = deepIconv($this->encDB, $this->encFile,mysqli_fetch_assoc($rez));
			}
			else{
				$nextRow=mysqli_fetch_assoc($rez);
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
				$nextRow = deepIconv($this->encDB, $this->encFile,mysqli_fetch_object($rez));
			}
			else{
				$nextRow=mysqli_fetch_object($rez);
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
				$nextRow = deepIconv($this->encDB, $this->encFile,mysqli_fetch_row($rez));
			}
			else{
				$nextRow=mysqli_fetch_row($rez);
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
			$row=mysqli_fetch_row($rez);
			if ($this->needEnc){
				$nextVal = iconv($this->encDB, $row[0]);
			}
			else{
				$nextVal=$row[0];
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
			return mysqli_real_escape_string($this->link, $str);
		}
		
	}
?>