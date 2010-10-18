<?php
	/**
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @version 1.0
	 * @package Exceptions
	 */
	
	/**
	 * Класс исключения.
	 * 
	 * Клас расширяет стандартное исключение и вводит понятие типа исключения.
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @package Exceptions
	 */
	class VoltException extends Exception{
		
		/**
		 * Тип исключения.
		 * 
		 * Переменная вводится для упрощения группировки исключений.
		 * @var string
		 */
		protected $type;

		/**
		 * Файл для логирования.
		 * 
		 * Индекс в массиве $vf["log"], указывающий на файл для логирования данного типа исключения.  
		 * @var string
		 */
		protected $logFile="excLog";
		
		/**
		 * Переменная в массиве $vf["exc"], определяющая нужно ли логировать данный тип исключения.
		 * @var string
		 */
		protected $logType="voltExcLog";
		
	
		/**
		 * Создает исключение
		 * @param string $mes Сообщение исключения
		 * @param string $type Тип исключения
		 * @param int $code Код и исключения
		 * @param Exception $previous Исключение вызвавшее текущее исключени
		 */
		public function __construct($mes, $type, $code=0, Exception $previous = NULL){
			global $vf;
			parent::__construct($mes, $code);
			$this->type=$type;
			if ($vf["exc"]["excLog"] && $vf["exc"][get_class($this)]){
				$this->log();
			}
		}
		
		/**
		 * Возвращает тип исключения
		 * @return string
		 */
		public function getType(){
			return $this->type;
		}
		
		/**
		 * Логирует исключение 
		 */
		protected function log(){
			excLog($this);
		}
	}