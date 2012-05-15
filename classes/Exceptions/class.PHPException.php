<?php
/**
 * @author Костин Алексей Васильевич aka Volt(220)
 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @version 2.0
 * @category VoltCore
 * @package VoltCoreFiles
 * @subpackage Classes
 */

/**
 * Класс исключений при PHP ошибках.
 * 
 * @category VoltCore
 * @package VoltCoreClasses
 * @subpackage Exceptions
 */
class PHPException extends VoltException{

    /**
     * Создает исключение
     * @param string $mes Сообщение исключения
     * @param string $type Тип исключения
     * @param int $code Код и исключения
     * @param Exception $previous Исключение вызвавшее текущее исключени
     */
    public function __construct($errno, $errmsg, $file, $line, Exception $previous = NULL){
        switch ($errno) { 
            case E_USER_ERROR : 
                $type = 'UserError'; 
                break; 
            case E_USER_DEPRECATED : 
                $type = 'UserDeprecated'; 
                break; 
            case E_USER_WARNING : 
                $type = 'UserWarning'; 
                break; 
            case E_USER_NOTICE : 
                $type = 'UserNotice'; 
                break; 
            case E_NOTICE : 
                $type = 'Notice'; 
                break; 
            case E_ERROR : 
                $type = 'Error'; 
                break; 
            case E_WARNING : 
                $type = 'Warning'; 
                break; 
            case E_PARSE : 
                $type = 'Parse'; 
                break; 
            case E_NOTICE : 
                $type = 'Notice'; 
                break; 
            default : 
                $type = 'UNKNOWN'; 
                break; 
        } 
        $errmsg .= '|'.$file.'|'.$line;
        parent::__construct($errmsg,$type,$errno, $previous);
    }

    public function getSql(){
        return $this->sql;
    }
}