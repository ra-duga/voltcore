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
	 * Класс исключения неверного формата.
	 * 
	 * Исключение выбрасывается когда формат переменной не соответствует ожидаемому (при несоответствии типов, несоответствии шаблону и т.п.).
	 *  
	 * @category VoltCore
	 * @package VoltCoreClasses
	 * @subpackage Exceptions
	 */
	class FormatException extends VoltException{

		public function __construct($mes, $type, $code=0, Exception $previous = NULL){
			parent::__construct($mes,$type,$code, $previous);
		}
	}
?>