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
	 * Фабрика классов для работы с СУБД.
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @category VoltCore
	 * @package VoltCoreClasses
	 * @subpackage DBClasses
	 */
	class SQLDBFactory{
		
		/**
		 * Массив объектов для работы с БД
		 * @var array
		 */		
		static protected $arrInst=array();
		
		/**
		 * Ключ в массиве {@link $arrInst}. Указывает на последний объект.
		 * @var string
		 */
		static protected $curKey=-1;

		/**
		 * Ключ в массиве {@link $arrInst}. Указывает на объект соответствующий альтернативному подключению.
		 * 
		 * Если альтернативный объект существует, то он находится в начале массива {@link $arrInst}.
		 * 
		 * @var string
		 */
		static protected $alterKey=-1;
		
		
		/**
		 * Возвращает олбъект для работы с БД.
		 * 
		 * @throws VoltException, FormatException
		 * @param mixed $config Конфигурационный массив
		 * @return object Объект для работы с БД
		 */
		public static function getDB($config=null){

			global $vf;
			/////////////////////////////////////
			//Обработка конфигурационных данных//
			/////////////////////////////////////
			
			//Если не указан конфигурационый массив
			if (!$config){
				//если уже с чем-то работали
				if (self::$curKey!=-1) {
					$lastIns=self::$arrInst[self::$curKey];
					return $lastIns;
				}
				else{
					$config=$vf["db"];
				}
			}
			else{			
				//Если задана СУБД
				if (is_string($config)){
					$temp=$config;
					$config=$vf["db"];
					$config["subd"]=$temp;
				}
				//Если дано непонятно что.
				elseif (!is_array($config)){
					throw new FormatException("Неверный формат конфигурационных данных", "Неверный тип данных");
				}
				//Если дан массив
				else{
					$config=array_merge($vf["db"], $config);
				}
			}
			
			////////////////
			//Выбор класса//
			////////////////
			switch ($config["subd"]){
				case "mysql": $DB= new MySQLDB($config); break; 
				case "mssql": $DB= new MSSQLDB($config); break;
				default : throw new VoltException("Данный тип СУБД не поддерживается", "Неверная СУБД");
			}

			///////////////
			//Регистрация//
			///////////////
			
			$newId=uniqid();
			$DB->setId($newId);
			self::$arrInst[$newId]=$DB;
			self::$curKey=$newId;
			return $DB;
		} 
		
		/**
		 * Удаляет объект из массива {@link $arrInst}. 
		 * 
		 * @param string $key Идентификатор удаляемого объекта. Если не задан, то удаляется текущее соединение.
		 * @throws FormatException Если неверно задан ключ.
		 */
		public static function unsetDB($key=null){
			if ($key==null) 
				$key=self::$curKey!=-1 ? self::$curKey : self::$alterKey;
			if ($key instanceof SQLDB) $key=$key->getId();
			if (!is_string($key) || !array_key_exists($key,self::$arrInst)) throw new FormatException("Неверный формат ключа", "Неверный тип данных");
			
			//Если уничтожается альтернативное подключение
			if ($key==self::$alterKey){
				self::$alterKey =-1;
			}
			
			unset(self::$arrInst[$key]);
			
			// Ищем последний объект
			end(self::$arrInst);
			self::$curKey=key(self::$arrInst);
			
			//Если объектов больше нет 
			if (!self::$curKey) {
				self::$curKey = -1; 
			}
		}
		
		/**
		 * Возвращает объект, соответствующий альтернативному соединению и устанавливает его для работы п оумолчанию
		 *
		 * Если альтернативного объекта нет, то он создается. 
		 * Если есть альтернативный объект и задано $config, то выбрасывается SqlException 
		 * 
		 * @throws VoltException, FormatException, SqlException
		 * @param mixed $config Конфигурационный массив
		 * @return object Объект для работы с БД
		 */
		public static function getAlterDB($config=null){
			//Если альтернативного соединения нет
			global $vf;
			if (self::$alterKey==-1){
				if (!$config){
					$config["base"]=$vf["db"]["base2"];
				}
				$DB=self::getDB($config);
				self::$alterKey=self::$curKey;
				//Записываем новый объект в начало массива.
				$tempArr[self::$curKey]=array_pop(self::$arrInst);
				self::$arrInst=array_merge($tempArr,self::$arrInst);
				end(self::$arrInst);
				self::$curKey=key(self::$arrInst);

				return $DB;
			}

			if ($config){
				throw new SqlException("Альтернативное соединение уже существует","Уже есть","Нет запроса");
			}
			else {
				return self::$arrInst[self::$alterKey];
			}
		}
	}
?>