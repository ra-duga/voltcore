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
		 * @var string
		 */
		static protected $alterKey=-1;
		
		
		/**
		 * Возвращает олбъект для работы с БД.
		 * 
		 * @param mixed $config Конфигурационный массив
		 * @return object Объект для работы с БД
		 * @throws FormatException Если переданы неверные данные.
		 */
		public static function getDB($userConfig=null){

			global $vf;
			/////////////////////////////////////
			//Обработка конфигурационных данных//
			/////////////////////////////////////
			
			//Если не указан конфигурационый массив
			$config=$userConfig;
			if (is_null($userConfig)){
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
				if (is_string($userConfig)){
					if(isset(self::$arrInst[$userConfig])) return self::$arrInst[$userConfig];
					$config=$vf["db"];
					$config["subd"]=$userConfig;
				}
				//Если дано непонятно что.
				elseif (!is_array($userConfig)){
					throw new FormatException("Неверный формат конфигурационных данных", "Неверный тип данных");
				}
				//Если дан массив
				else{
					$config=array_merge($vf["db"], $userConfig);
				}
			}
			
			////////////////
			//Выбор класса//
			////////////////
			switch ($config["subd"]){
				case "mysql": $DB= new MySQLDB($config); break; 
				case "mssql": $DB= new MSSQLDB($config); break;
				default : throw new FormatException("Данный тип СУБД не поддерживается", "Некорректные данные");
			}

			///////////////
			//Регистрация//
			///////////////
			
			$newId=uniqid();
			$DB->setId($newId);
			self::$arrInst[$newId]=$DB;
			if (is_null($userConfig)){
				self::$curKey=$newId;
			}
			return $DB;
		} 
		
		/**
		 * Удаляет объект из массива {@link $arrInst}. 
		 * 
		 * @param string $key Идентификатор удаляемого объекта. Если не задан, то удаляется текущее соединение.
		 * @throws FormatException Если неверно задан ключ.
		 */
		public static function unsetDB($key=null){
			if (is_null($key)) {
				if(self::$curKey!=-1){
					$key=self::$curKey;
					self::$curKey=-1;
				}else{
					$key=self::$alterKey;
					self::$alterKey=-1;
				}
			}
			if ($key instanceof SQLDB) $key=$key->getId();
			if (!is_string($key) || !array_key_exists($key,self::$arrInst)) throw new FormatException("Неверный формат ключа", "Неверный тип данных");
			
			unset(self::$arrInst[$key]);
		}
		
		/**
		 * Возвращает объект, соответствующий альтернативному соединению.
		 *
		 * Если альтернативного объекта нет, то он создается. 
		 * 
		 * @param mixed $config Конфигурационный массив
		 * @return object Объект для работы с БД
		 * @throws FormatException - если заданы неверные данные.
		 */
		public static function getAlterDB($userConfig=null){
			//Если альтернативного соединения нет
			global $vf;
			if (is_null($userConfig)){
				if (self::$alterKey==-1){
					$config["base"]=$vf["db"]["base2"];
					$DB=self::getDB($config);
					self::$alterKey=$DB->getId();
					return $DB;
				}else{
					return self::$arrInst[self::$alterKey];
				}
			}else{
				$config["base"]=$vf["db"]["base2"];
				if (is_string($userConfig)){
					$config["subd"]=$userConfig;
				}elseif(is_array($userConfig)){
					$config=array_merge($config, $userConfig);
				}
				$DB=self::getDB($config);
				return $DB;
			}
		}
	}
?>