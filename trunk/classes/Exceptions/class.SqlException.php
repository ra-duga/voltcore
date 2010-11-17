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
	 * Класс sql исключения.
	 * 
	 * @category VoltCore
	 * @package VoltCoreClasses
	 * @subpackage Exceptions
	 */
	class SqlException extends VoltException{
		
		/**
		 * sql запрос вызвавший исключение
		 * @var string
		 */
		protected $sql;

		/**
		 * Создает исключение
		 * @param string $mes Сообщение исключения
		 * @param string $type Тип исключения
		 * @param int $code Код и исключения
		 * @param Exception $previous Исключение вызвавшее текущее исключени
		 */
		public function __construct($mes, $type, $sql, $code=0, Exception $previous = NULL){
			$this->sql=$sql;
			parent::__construct($mes,$type,$code, $previous);
		}
		
		public function getSql(){
			return $this->sql;
		}
	}
?>