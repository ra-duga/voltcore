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
	 * Абстрактный класс работы с базой данных. От него наследуют классы для работы с конкретной СУБД.
	 * 
	 * Для правильной работы, кроме приведенных здесь абстрактных методов, классы потомки должны реализовывать
	 * статический метод getDB($config), который реализует шаблон singleton без применения фабрики.
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @package classes
	 * @subpackage DBClasses
	 * @abstract
	 */
	abstract class SQLDB {
		
		/**
		 * Уникальный идентификатор. Создается фабрикой.
		 * @access protected
		 * @var string 
		 */
		protected $factId;
		
		/**
		 * Определяет нужно ли производить перекодирование между БД и сайтом
		 * @access protected
		 * @var boolean 
		 */
		protected $needEnc;
		
		/**
		 * Кодировка БД
		 * @access protected
		 * @var string
		 */
		protected $encDB;

		/**
		 * Кодировка сайта
		 * @access protected
		 * @var string
		 */
		protected $encFile;
		
		/**
		 * Адрес сервера БД
		 * @access protected
		 * @var string
		 */
		protected $host;
		
		/**
		 * Логин
		 * @access protected
		 * @var string
		 */
		protected $login;
		
		/**
		 * Пароль
		 * @access protected
		 * @var string
		 */
		protected $pass;
		
		/**
		 * База данных
		 * @access protected
		 * @var string
		 */
		protected $db;

		/**
		 * Определяет показывать ли sql запросы
		 * @access protected
		 * @var boolean
		 */
		protected $sqlShow;
	
		/**
		 * Определяет логировать ли sql запросы
		 * @access protected
		 * @var boolean
		 */
		protected $sqlLog;
		
		/**
		 * Определяет проверять ли на наличие union
		 * @access protected
		 * @var boolean
		 */
		protected $checkU;
	
		/**
		 * Определяет проверять ли на наличие присоединенных запросов
		 * @access protected
		 * @var boolean
		 */
		protected $checkD;
		
		/**
		 * Ссылка на открытое соединение с БД
		 * @access protected
		 * @var resource
		 */
		protected $link;

		/**
		 * Результат запроса
		 * @access protected
		 * @var resource
		 */
		protected $res;
		
		/**
		 * Последний запрос
		 * @access protected
		 * @var string
		 */
		protected $sql;
		
		/**
		 * Символ для обрамления ключа слева.
		 * 
		 * Символ для обрамления ключа слева(Left Key Symbol).  
		 * Используется в {@link escapeKeys}
		 * @access protected
		 * @var string
		 */
		protected $LKS="";
		
		/**
		 * Символ для обрамления ключа справа.
		 * 
		 * Символ для обрамления ключа справа(Right Key Symbol).  
		 * Используется в {@link escapeKeys}
		 * @access protected
		 * @var string
		 */
		protected $RKS="";
		
											//////////////////////
											//Абстрактные методы//
											//////////////////////
		
		/**
		 * Закрывает соединение 
		 */
		abstract protected function closeConnection();
		
		/**
		 * Выполняет запрос $sql.
		 * 
		 * @access protected
		 * @param string $sql запрос для выполнения
		 * @return resource результат выпонения запроса
		 */
		abstract protected function query($sql);
		
		/**
		 * Возвращает код последней ошибки
		 * 
		 * @return string Код последней ошибки
		 */
		abstract public function getErrorCode();
		
		/**
		 * Возвращает последнее сообщение об ошибке
		 * 
		 * @return string Сообщение об ошибке
		 */
		abstract public function getErrorMsg();
		
		/**
		 * Возвращает id только что добавленной записи.
		 *
		 * @access protected
		 * @return int id только что добавленной записи
		 */
		abstract protected function getLastID();
		
		/**
		 * Возвращает количество строк, обработанных последним запросом.
		 *
		 * @access protected
		 * @return int количество строк, обработанных последним запросом
		 */
		abstract protected function affectRows();

		/**
		 * Начинает транзакцию
		 *
		 * Отключает autocommit и посылает серверу команду начать транзакцию.
		 */
		abstract public function startTran();

		/**
		 * Подтверждает транзакцию
		 *
		 * Подтверждает транзакцию и включает autocommit.
		 */
		abstract public function commit();

		/**
		 * Откатывает транзакцию
		 *
		 * Производит откат транзакции и включает autocommit.
		 */
		abstract public function rollback();

		/**
		 * Возвращает очередную строку из результата запроса в виде ассоциативного массива
		 *
		 * @param resource $res Если параметр $res задан, то строка берется из него, в противном случае строка берется из $this->res
		 * @return array Ассоциативный массив, содержащий значения из очередной строки результата
		 */
		abstract public function fetchAssoc($res=null);

		/**
		 * Возвращает очередную строку из результата запроса в виде объекта
		 *
		 * @param resource $res Если параметр $res задан, то строка берется из него, в противном случае строка берется из $this->res
		 * @return array Объект, содержащий значения из очередной строки результата
		 */
		abstract public function fetchObj($res=null);

		/**
		 * Возвращает очередную строку из результата запроса в виде пронумерованного массива
		 *
		 * @param resource $res Если параметр $res задан, то строка берется из него, в противном случае строка берется из $this->res
		 * @return array Пронумерованный массив, содержащий значения из очередной строки результата
		 */
		abstract public function fetchRow($res=null);

		/**
		 * Возвращает единственное значение из результата запроса
		 *
		 * Этот метод следует использовать, когда ожидется выбор единственного значения
		 *
		 * @param resource $res Если параметр $res задан, то строка берется из него, в противном случае строка берется из $this->res
		 * @return mixed Значение из результата запроса
		 */
		abstract public function fetchField($res=null);

		/**
		 * Обрабатывает спецсимволы в строке для безопасного ее использования в запросе
		 *
		 * @param mixed $str Строка, в которой надо экранировать спецсимволы.
		 * @return mixed Строка с экранированными спецсимволы.
		 */
		abstract public function escape($str);
		
		
											/////////////////////
											//Магические методы//
											/////////////////////

		/**
		 * Запрещение клонирования
		 * @throws Exception Клонирование запрещено
		 */
		public function __clone(){
			throw new Exeption('Клонирование запрещено!');
		}

		
		/**
		 * Преобразует объект в строковое представление. 
		 * @return string Строковое представление объекта
		 */
		public function __toString(){
			return get_class($this)."://$login:$pass@$host/$db";
		}

		/**
		 * Деструктор.
		 * 
		 * Закрывает соединение. Удаляет себя из списка фабрики. 
		 */
		public function __destruct(){
			$this->rollback();
			$this->closeConnection();
		}
											////////////////////
											//Сеттеры, геттеры//
											////////////////////
		/**
		 * Устанавливает значение {@link factId}
		 * @param string $id Новый идентификатор 
		 */
		public function setId($id){
			if (!is_string($id)) throw new FormatException("Неверный формат идентификатора","Неверный тип данных");
			$this->factId=$id;
		}
		
		/**
		 * Возвращает значение {@link factId}
		 * @return string Текущий идентификатор
		 */
		public function getId(){
			return $this->factId;
		}
		
		/**
		 * Конфигурирует объект в соответствии с массивом конфигурации
		 * @throws FormatException
		 * @param array $config Массив для установки конфигурации.
		 */
		protected function setConfig($config){
			if (!is_array($config)) throw new FormatException("Неверный формат конфигурационных данных","Неверный тип данных");
			$this->host=$config["host"];
			$this->login=$config["login"];
			$this->pass=$config["pass"];
			$this->db=$config["base"];
			$this->needEnc=$config["needEnc"];
			$this->encDB=$config["encDB"];
			$this->encFile=$config["encFile"];
			$this->sqlShow=$config["sqlShow"];
			$this->sqlLog=$config["sqlLog"];
			$this->checkU=$config["checkUnion"];
			$this->checkD=$config["checkDoubleQuery"];
		}
		
		
		/**
		 * Возвращает текущую конфигурацию объекта
		 * @return array массив с конфигурационными переменными
		 */
		public function getConfig(){
			$config["host"]=$this->host;
			$config["login"]=$this->login;
			$config["pass"]=$this->pass;
			$config["base"]=$this->db;
			$config["needEnc"]=$this->needEnc;
			$config["encDB"]=$this->encDB;
			$config["encFile"]=$this->encFile;
			$config["sqlShow"]=$this->sqlShow;
			$config["sqlLog"]=$this->sqlLog;
			$config["checkUnion"]=$this->checkU;
			$config["checkDoubleQuery"]=$this->checkD;
			return $config;
		}
		
		/**
		 * Возвращает последний запрос.
		 * @return string Последний запрос
		 */
		public function getLastQuery(){
			return $this->sql;
		}
													///////////
													//Запросы//
													///////////
		/**
		 * Выполняет запрос select.
		 * 
		 * Выполняет запрос $sql. Запрос должен быть запросом типа select. 
		 * Для предотвращения несанкционированных действий
		 * перед выполнением запрос проверяется на наличие оператора union. 
		 * Если оператор union будет найден, то будет выброшено исключение. 
		 * Если в запросе необходимо использовать оператор union,
		 * то количество таких операторов нужно указать в параметре $numUnion.
		 * 
		 * @throws SqlException, FormatException
		 * @param mixed $arr Если $arr массив, то он должен содержать поля для выбора.
		 * Если $arr это строка, то она трактуется как sql-запрос для выполнения.  
		 * @param string $tab Таблица, в которой выполняется обновление.
		 * @param mixed $where Условие, по которому происходит поиск, или id записи для поиска, 
		 * или массив где ключи массива трактуются как поля таблицы, а соответствующие значения как значения этих полей
		 * @param int $numUnion Количество операторов union в запросе.
		 * @return resource Результат запроса
		 */													
		public function select($arr, $tab=null, $where=null, $numUnion=0){
			$sql=$this->getSelect($arr, $tab, $where, $numUnion);
			$this->exec($sql);
			return $this->res;
		}

		/**
		 * Выполняет вставку строки в таблицу.
		 * 
		 * @throws SqlException
		 * @param mixed $arr Если $arr массив, то ключи массива трактуются как поля таблицы,
		 * а соответствующие значения как значения этих полей. Значения проходят обработку {@link escapeString}
		 * Если $arr это строка, то она трактуется как sql-запрос для выполнения.  
		 * @param string $tab Таблица, в которую нужно вставить значения.
		 * @return mixed id только что вставленной записи 
		 */
		public function insert($arr, $tab=null){
			$newId=0;
			$sql=$this->getInsert($arr, $tab);
			$this->exec($sql);
			return $this->getLastID();
		}

		/**
		 * Выполняет обновление данных в таблице.
		 * 
		 * @throws SqlException, FormatException
		 * @param mixed $arr Если $arr массив, то ключи массива трактуются как поля таблицы,
		 * а соответствующие значения как значения этих полей.
		 * Если $arr это строка, то она трактуется как sql-запрос для выполнения.  
		 * @param string $tab Таблица, в которой выполняется обновление.
		 * @param mixed $where Условие, при котором выполняется обновление, или id записи для обновления, 
		 * или массив где ключи массива трактуются как поля таблицы, а соответствующие значения как значения этих полей
		 * @return int Количество обновленных строк.
		 */
		public function update($arr, $tab=null, $where=null){
			$sql=$this->getUpdate($arr, $tab, $where);
			$this->exec($sql);
			return $this->affectRows();
		}

		/**
		 * Выполняет удаление из таблицы.
		 * 
		 * @throws SqlException, FormatException
		 * @param string $tab Таблица из которой удалять или sql-запрос.
		 * @param mixed $where Условие, при котором выполняется удаление, или id записи для удаления, 
		 * или массив где ключи массива трактуются как поля таблицы, а соответствующие значения как значения этих полей
		 * Если $where ничего из вышеперечисленного, то $tab считается sql-запросом.
		 * @return int Количество удаленных строк.
		 */
		public function delete($tab, $where=null){
			$sql=$this->getDelete($tab, $where);
			$this->exec($sql);
			return $this->affectRows();
		}
		
		/**
		 * Определяет id по уникальному значению.
		 * 
		 * @param string $tab Имя таблицы.
		 * @param array $where Массив для определения id. Ключи - имена полей, значения - значения полей. 
		 * @return mixed Идентификатор.
		 */
		public function findOrInsert($tab, $where){
			$this->select(array("id"), $tab, $where);
			if ($mas=$this->fetchRow()){
				$id=$mas[0];
			}
			else{
				$id=$this->insert($where,$tab);	
			}
			return $id;
		}

		/**
		 * Находит id по уникальному значению.
		 * 
		 * @param string $tab Имя таблицы.
		 * @param array $where Массив для определения id. Ключи - имена полей, значения - значения полей. 
		 * @return mixed Идентификатор.
		 */
		public function findId($tab, $where){
			return $this->getVal(array("id"), $tab, $where);
		}
		
												////////////////////
												//Запросы значений//
												////////////////////
		/**
		 * Возвращает первую строку из результата запроса $sql в виде ассоциативного массива. 
		 * 
		 * @throws SqlException, FormatException
		 * @param string $sql Запрос для выполнения
		 * @param int $numUnion Количество операторов union в запросе
		 * @return array Ассоциативный массив, содержащий значения из первой строки результата запроса
		 */
		public function getAssoc($arr, $tab=null, $where=null, $numUnion=0){
			$this->select($arr, $tab, $where, $numUnion);
			return $this->fetchAssoc();
		}

		/**
		 * Возвращает первую строку из результата запроса $sql в виде пронумированного массива. 
		 * 
		 * @throws SqlException, FormatException
		 * @param string $sql Запрос для выполнения
		 * @param int $numUnion Количество операторов union в запросе
		 * @return array Пронумированный массив, содержащий значения из первой строки результата запроса
		 */
		public function getRow($arr, $tab=null, $where=null, $numUnion=0){
			$this->select($arr, $tab, $where, $numUnion);
			return $this->fetchRow();
		}

		/**
		 * Возвращает первый столбец из результата запроса $sql в виде пронумированного массива. 
		 * 
		 * @throws SqlException, FormatException
		 * @param string $sql Запрос для выполнения
		 * @param int $numUnion Количество операторов union в запросе
		 * @return array Пронумированный массив, содержащий значения из первого столбца результата запроса
		 */
		public function getColumn($arr, $tab=null, $where=null, $numUnion=0){
			$this->select($arr, $tab, $where, $numUnion);
			$rez=array();
			while ($nextVal=$this->fetchField()){
				$rez[]=$nextVal;
			}
			return $rez;
		}
		
		/**
		 * Возвращает первую строку из результата запроса $sql в виде объекта 
		 * 
		 * @throws SqlException, FormatException
		 * @param string $sql Запрос для выполнения
		 * @param int $numUnion Количество операторов union в запросе
		 * @return object Объект, содержащий значения из первой строки результата запроса
		 */
		public function getObj($arr, $tab=null, $where=null, $numUnion=0){
			$this->select($arr, $tab, $where, $numUnion);
			return $this->fetchObj();
		}
		
		/**
		 * Возвращает единственное значение из результата запроса $sql. 
		 * 
		 * @throws SqlException, FormatException
		 * @param string $sql Запрос для выполнения
		 * @param int $numUnion Количество операторов union в запросе
		 * @return mixed Значение из первой строки результата запроса
		 */
		public function getVal($arr, $tab=null, $where=null, $numUnion=0){
			$this->select($arr, $tab, $where, $numUnion);
			return $this->fetchField();
		}
		
		/**
		 * Возвращает весь результат запроса в виде двумерного массива. 
		 * 
		 * @throws SqlException, FormatException
		 * @param string $sql Запрос для выполнения
		 * @param int $numUnion Количество операторов union в запросе
		 * @return array Двумерный массив с результатом запроса.
		 */
		public function getTable($sql,$numUnion=0){
			$this->select($sql,$numUnion);
			return $this->fetchTable();
		}
		
		/**
		 * Возвращает единственное значение из результата запроса $sql.
		 * 
		 * В отличии от {@link getVal} функция вызывает не {@link select}, а непосредственно {@link exec}.
		 * Таким образом запрос не проверяется. Также запрос не сохраняется в свойство {@link sql}.
		 * 
		 * @access protected
		 * @throws SqlException, FormatException
		 * @param string $sql Запрос для выполнения
		 * @return mixed Значение из первой строки результата запроса
		 */
		protected function getSystemVal($sql){
			$this->exec($sql, false);
			return $this->fetchField();
		}
		
														//////////////////////
														//Подгтовка запросов//
														//////////////////////
		/**
		 * Проверяет запрос select.
		 * 
		 * @see select
		 * @throws SqlException, FormatException
		 * @param mixed $arr Запрос для проверки или массив со значениями.
		 * @param string $tab Таблица, в которой обновляются значения.
		 * @param mixed $where Условие, по которому обновляются значения, или id записи для поиска.
		 * @param int $numUnion Количество операторов uniond в запросе.
		 * @return string Проверенный запрос
		 */
		public function getSelect($arr, $tab=null, $where=null, $numUnion=0){
			if (is_array($arr)){
				if (empty($tab)  || !is_string($tab)) throw new FormatException("Не задана таблица для выбора","Неверный тип данных");
				
				$fields="";
				foreach($arr as $val){
					$fields .=$this->escapeKeys($val).",";
				}
				$fields=trim($fields,",");
				
				$expr=$this->getWhere($where);
				
				$sql="select $fields from $tab ".$expr; //Создаем запрос
			}
			else{
				$sql=trim($arr."");
			}
			$this->checkQuery($sql, $numUnion);
			$this->checkSelect($sql);
			return $sql;
		}

		/**
		 * Формирует и проверяет запрос insert.
		 * @see insert
		 * @throws SqlException, FormatException
		 * @param mixed $arr Запрос для проверки или массив со значениями
		 * @param string $tab Таблица для вставки значения
		 * @return string Проверенный запрос
		 */
		public function getInsert($arr, $tab=null){
			$sql="";
			if (is_array($arr)){
				// Если таблица задана неверно, то выбросить исключение
				if (empty($tab)  || !is_string($tab)) throw new FormatException("Не задана таблица для вставки строки","Неверный тип данных");
				
				$keys="";
				$vals="";
	
				// Создаем строку со значениями полей
				foreach($arr as $key=>$val){
					$keys .=$this->escapeKeys($key).",";
					$vals .=$this->escapeString($val).",";
				}
				$vals="(".trim($vals,",").")";
				$keys="(".trim($keys,",").")";
				
				//Создаем запрос
				$sql="insert into ".$tab.$keys." values".$vals;
			}
			else{
				$sql=trim($arr."");
			}

			$this->checkQuery($sql);
			$this->checkInsert($sql);
			return $sql;
		}

		/**
		 * Формирует и проверяет запрос update.
		 * @see update
		 * @throws SqlException, FormatException
		 * @param mixed $arr Запрос для проверки или массив со значениями
		 * @param string $tab Таблица, в которой обновляются значения
		 * @param mixed $where Условие, по которому обновляются значения, или id записи для обновления
		 * @return string Проверенный запрос
		 */
		public function getUpdate($arr, $tab=null, $where=null){
			$sql="";
			if (is_array($arr)){
				if (empty($tab)  || !is_string($tab)) throw new FormatException("Не задана таблица для вставки строки","Неверный тип данных");
				
				$sets=$this->getAssignmentString($arr, ",");

				$expr=$this->getWhere($where);
								
				$sql="update $tab set".$sets.$expr; //Создаем запрос
			}
			else{
				$sql=trim($arr."");
			}

			$this->checkQuery($sql);
			$this->checkUpdate($sql);
			return $sql;
		}

		/**
		 * Формирует и проверяет запрос delete.
		 * @see delete
		 * @throws SqlException, FormatException
		 * @param string $tab Запрос для проверки или таблица, в которой обновляются значения
		 * @param mixed $where Условие, при котором удаляется запись, или id записи для удаления
		 * @return string Проверенный запрос
		 */
		public function getDelete($tab, $where=null){
			$sql="";
			
			$expr=$this->getWhere($where);
			if($expr){
				$sql="delete from ".$tab.$expr;
			}
			else{
				$sql=trim($tab."");
			}

			$this->checkQuery($sql);
			$this->checkDelete($sql);
			return $sql;
		}
		
		/**
		 * Формирует условие where.
		 * 
		 * @param mixed $where Условие, при котором выполняется действие, или id записи, 
		 * или массив где ключи массива трактуются как поля таблицы, а соответствующие значения как значения этих полей
		 * @return string Сформированное условие.
		 */
		public function getWhere($where){
				if (is_array($where)) return " where ".$this->getAssignmentString($where, " and");
				if (is_int($where)) return " where id=".$where;
				if (!empty($where)) return " where ".$where;
				return'';
		} 
		
		/**
		 * Подготовливает выполнение sql-запроса
		 * 
		 * Функция запоминает sql-запрос в свойстве класса.
		 * Логирует и выводит его на экран, если это указано в настройках  $vf["db"]["sqlLog"] и $vf["db"]["sqlShow"].
		 * Производит перекодировку запроса, если нужно.
		 * Выбрасывает исключение в случае ошибки на sql-сервере.
		 *
		 * @param string sql-запрос для выполнения
		 */
		public function exec($sql, $save=true){
			global $vf;
			if ($save){
				$this->sql=$sql;
			}
			if ($vf["db"]["sqlShow"]) echo $sql, "<br>";
			if ($vf["db"]["sqlLog"]) logMsg($sql,"SQL Log","sqlLog");
			if ($this->needEnc){
				$sql = iconv($this->encFile, $this->encDB,$sql);
			}
			$this->res=$this->query($sql);
			if (!$this->res){
				throw new SqlException($this->getErrorCode()." ".$this->getErrorMsg(), "Ошибка на сервере", $sql);
			}
		}
		
		/**
		 * Функция создает строку присвоения/сравнения из массива.
		 * 
		 * Функция создает строку присвоения/сравнения из массива.
		 * Ключи массива трактуются как поля таблицы, а соответствующие значения, как те значения которые нужно присвоить/сравнить. 
		 * <code>
 		 * <?php
 		 * $arr["id"]=50;
 		 * $arr["title"]="Новая книга";
 		 * $arr["izd"]=null;
 		 * 
 		 * $DB->SQLDBFactory::getDB();
 		 * $str=$DB->getAssignmentString($arr, ", ");
 		 * echo $str;
 		 * 
 		 * ?>
 		 * В случае использования MSSQLDB результат будет таким:
 		 * [id]=50, [title]='Новая книга', [izd]=NULL
 		 * </code>
		 * 
		 * @param array $arr Массив из которого нужно сделать строку присвоения/сравнения
		 * @param string $delim Строка, которая вставляется между ассоциированными парами. 
		 * @return string Требуемая строка
		 */
		public function getAssignmentString($arr, $delim){
			$rez="";
			foreach($arr as $key=>$val){
				$rez .= $this->escapeKeys($key)."=".$this->escapeString($val).$delim;
			}
			$rez=substr($rez,0,-strlen($delim));
			return $rez;
		}

														////////////////////
														//Служебные методы//
														////////////////////
		
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
			if ($str==null) return "NULL";
			if (is_string($str)) return "'".$this->escape($str)."'";
			return $str;
		}

		/**
		 * Обрамляет имя поля в специальные символы.
		 * 
		 * В большинстве БД для использования некоторых символов (например, пробелов) в имени поля нужно все поле обрамить спецсимволами.
		 * Именно это и делает данная функция
		 *
		 * @param string $str Имя поля
		 * @return string имя поля заключенное в спецсимволы
		 */
		public function escapeKeys($key){
			if ($key==="*") return "*";
			return $this->LKS.$key.$this->RKS;
		}
		
		/**
		 * Возвращает результат запроса в виде двумерного массива.
		 * 
		 * @param resource $res Если параметр $res задан, то строка берется из него, в противном случае строка берется из $this->res
		 * @return array Двумерный массив с результатом запроса.
		 */
		public function fetchTable($rez=null){
			if (!$rez){
				$rez=$this->res;
			}
			
			$dataTable=array();
			while ($row=$this->fetchAssoc($rez)){
				$dataTable[]=$row;
			}
			
			return $dataTable;
		} 
		
														///////////////////
														//Перекодирование//
														///////////////////
		
		/**
		 * Запоминает кодироки сайта и БД для перекодирования запросов и их результатов
		 * @throws FormatException 
		 * @param string $encFile Кодировка сайта
		 * @param string $encDB Кодировка БД
		 */
		public function setEncodings($encFile, $encDB){
			if(!is_string($encFile) or !is_string($encDB)) throw new FormatException("Неверный формат имен кодировок","Неверный тип данных");
			$this->encDB=$encDB;
			$this->encFile=$encFile;
		}
		
															////////////
															//Проверки//
															////////////
		
		/**
		 * Проверяет запрос
		 * @throws SqlException, FormatException
		 * @param string $sql Запрос для проверки
		 * @param int $unionCol Количество операторов union
		 */
		protected function checkQuery($sql, $numUnion=0){
			if (!is_string($sql)) throw new FormatException("В качестве запроса передана не строка", "Неверный тип данных"); 
			$this->checkDoubleTir($sql);
				$this->checkDoubleQuery($sql,$numUnion);
		}

		
		/**
		 * Проверяет есть ли в запросе два тире подряд
		 * @throws SqlException
		 * @param string $sql Запрос для проверки
		 */
		protected function checkDoubleTir($sql){
			$pos=strpos($sql,"--");
			if ($pos!==false) {
				throw new SqlException("Два тире подряд", "Потенциально опасные данные", $sql);
			}
			$pos=strpos($sql,"/*");
			if ($pos!==false) {
				throw new SqlException("Открытие комментария", "Потенциально опасные данные", $sql);
			}
		}
		
		/**
		 * Проверка на вложенный или присоединенный запрос
		 * @throws SqlException
		 * @param string $sql Запрос для проверки
		 * @param int $unionCol Количество операторов union
		 */
		protected function checkDoubleQuery($sql,$unionCol=0){
			
			//Проверка на наличие Union
			if ($this->checkU){
				$pos=-1;
				$tekUnionCol=0;
				while ($pos !== false){
					$pos=stripos($sql,"union",$pos+1);
					if ($pos!==false && $this->notInQutes($sql,$pos)) {
						$tekUnionCol++;
						if ($tekUnionCol>$unionCol) {
							throw new SqlException("Использована команда Union", "Потенциально опасные данные", $sql);
						}
					}
				}
			}

			//Проверка на наличие подзапросов;
			if ($this->checkD){
				$pos=-1;
				while ($pos !== false){
					$pos=stripos($sql,";",$pos+1);
					if ($pos!==false && $this->notInQutes($sql,$pos)) {
						throw new SqlException("Передано более одного запроса", "Потенциально опасные данные", $sql);
					}
				}
			
			
			//Проверка на вложенный update
				$pos=0;
				while ($pos !== false){
					$pos=stripos($sql,"update",$pos+1);
					if ($pos!==false && $this->notInQutes($sql,$pos)) {
						throw new SqlException("Использована вложенная команда update", "Потенциально опасные данные", $sql);
					}
				}

			//Проверка на вложенный delete
				$pos=0;
				while ($pos !== false){
					$pos=stripos($sql,"delete",$pos+1);
					if ($pos!==false && $this->notInQutes($sql,$pos)) {
						throw new SqlException("Использована вложенная команда delete", "Потенциально опасные данные", $sql);
					}
				}
			}
		}
		
		/**
		 * Проверка того, что запрос является запросом Select
		 * 
		 * @throws SqlException
		 * @param string $sql Запрос для проверки
		 */
		protected function checkSelect($sql){
			$pos=stripos($sql,"Select");
			if ($pos!==0) {
				throw new SqlException("Не найден select в начале запроса", "Потенциально опасные данные", $sql);
			}
		}
		
		/**
		 * Проверка того, что запрос является запросом Insert
		 * 
		 * @throws SqlException
		 * @param string $sql Запрос для проверки
		 */
		protected function checkInsert($sql){
			$posI=stripos($sql,"Insert");
			$posR=stripos($sql,"Replace");
			if ($posI!==0 && $posR!==0) {
				throw new SqlException("Не найден Insert в начале запроса", "Потенциально опасные данные", $sql);
			}
		}

		/**
		 * Проверка того, что запрос является запросом Update
		 * 
		 * @throws SqlException
		 * @param string $sql Запрос для проверки
		 */
		protected function checkUpdate($sql){
			$pos=stripos($sql,"Update");
			if ($pos!==0) {
				throw new SqlException("Не найден Update в начале запроса", "Потенциально опасные данные", $sql);
			}
		}
		
		/**
		 * Проверка того, что запрос является запросом Delete
		 * 
		 * @throws SqlException
		 * @param string $sql Запрос для проверки
		 */
		protected function checkDelete($sql){
			$pos=stripos($sql,"Delete");
			if ($pos!==0) {
				throw new SqlException("Не найден Delete в начале запроса", "Потенциально опасные данные", $sql);
			}
		}
		
		/**
		 * Функция возвращает true, если данные в позиции $otkuda из строки $gde не взяты в одинарные кавычки
		 * 
		 * @param string $gde Строка в которой происходит проверка
		 * @param int $otkuda Позиция для которой происходит проверка
		 * @return boolean Резултат проверки
		 */
		protected function notInQutes($gde,$otkuda){
			$posQ2=-1;
			$posQ1=-1;
			while ($posQ1!==false){
				$posQ1=strpos($gde,"'",$posQ2+1); //Находим открывающий апостроф
				$flag=true;
				$posQ=$posQ1;
				while ($flag and $posQ2!==false) {
					$posQ2=strpos($gde,"'",$posQ+1); //Находим закрывающий апостроф
					$posQ=strpos($gde,"'",$posQ2+1); //Находим символ экранирования
					//Если нашли не экранированный апостроф, то выходим их цикла
					if ($posQ!==$posQ2+1){
						$flag=false;
					}
				}
				//Если нашли два апострофа
				if ($posQ1!==false && $posQ2!==false){
					//Если позиция между апострофами
					if ($posQ1<=$otkuda && $otkuda<=$posQ2){
						return false;
					}
					//Если первый апостроф правее позиции, значит все возможожные пары до этого уже проверены. 
					if ($otkuda<$posQ1){
						return true;
					}
				}
			}
			return true;
		}		
	}
?>