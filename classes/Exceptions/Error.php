<?php
/**
 * @author Костин Алексей Васильевич
 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
 * @version 2.0
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @category VoltCore
 * @package VoltCoreFiles
 * @subpackage Classes
 */

/**
 * Класс данных о произошедших ошибках.
 * 
 * @author Костин Алексей Васильевич
 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
 * @version 2.0
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @category VoltCore
 * @package VoltCoreClasses
 * @subpackage Exceptions
 */
class Error {

    /**
     * Объект ошибок.
     * @var Error
     */
    static private $instance = null;
    
	/**
     * Массив ошибок.
     * @var array
     */
    static private $errors = array();
	
    /**
     * Возвращает объект реестра.
     * 
     * @return Registry Реестр.
     */
    public static function getInstance(){
        if (!self::$instance){
            self::$instance = new Erorr();
        }
        return self::$instance;
        
    }
    
	/**
	 * Возвращает возникшие ошибки.
	 * 
	 * @return array Перехваченные ошибки и исключения. 
	 */
	public static function getErrors(){
		return self::$errors;
	}
	
	/**
	* Обрабатывает ошибки, возникающие во время выполнения.
	* 
	* @param int $errno Номер ошибки
	* @param string $errmsg Сообщение об ошибке
	* @param string $file Файл, в котором возникла ошибка
	* @param int $line Строка, в которой возникла ошибка
	*/
	public static function errorHandler($errno, $errmsg, $file, $line){
        if ($errno < ERR_LOG_LEVEL) return;
        
		self::$errors[] = $errno.'|'.$errmsg.'|'.$file.'|'.$line;
		$erConf = Registry::getConfig()->error;
        if ($erConf['errorToException']){
            throw new PHPException($errno, $errmsg, $file, $line);
		}else{
			Logger::logMsg($errno.'|'.$errmsg.'|'.$file.'|'.$line);
		}
	}

	/**
	* Обрабатывает не пойманные исключения.
	* 
	* @param Exception $e Исключение.
	*/
	public static function exceptionHandler($e){
		self::$errors[] = $e->getMessage()."|".$e->getFile()."|".$e->getLine();
		Logger::excLog($e, 'НЕ ПОЙМАЛИ!!!');
	}
	
	/**
	 * Добавляет исключение в лог ошибок.
	 * 
	 * @param Exception $e Исключение для добавления
	 */
	public static function addException($e){
		self::$errors[] = $e->getMessage()."|".$e->getFile()."|".$e->getLine();
		Logger::excLog($e);
	}


}