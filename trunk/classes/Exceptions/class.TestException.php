<?php
	/**
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @version 1.0
	 * @package classes
	 * @subpackage exceptions
	 */
	
	/**
	 * Класс исключения при тестировании.
	 *  
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @package classes
	 * @subpackage exceptions
	 */
	class TestException extends VoltException{

		/**
		 * Файл для логирования.
		 * 
		 * Индекс в массиве $vf["log"], указывающий на файл для логирования данного типа исключения.  
		 * @var string
		 */
		protected $logFile="testExcLog";
		
		/**
		 * Переменная в массиве $vf["exc"], определяющая нужно ли логировать данный тип исключения.
		 * @var string
		 */
		protected $logType="testExcLog";
		
		
		/**
		 * Создает исключение
		 * @param string $mes Сообщение исключения
		 * @param string $type Тип исключения
		 * @param int $code Код и исключения
		 * @param Exception $previous Исключение вызвавшее текущее исключени
		 */
		public function __construct($mes, $type, $code=0, Exception $previous = NULL){
			parent::__construct($mes,$type,$code, $previous);
		}
	}
?>