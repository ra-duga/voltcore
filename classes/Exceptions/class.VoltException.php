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
     * Класс исключения.
     * 
     * Клас расширяет стандартное исключение и вводит понятие типа исключения.
     * 
     * @category VoltCore
     * @package VoltCoreClasses
     * @subpackage Exceptions
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
            if ($vf["exc"]["excLog"] && (!isset($vf["exc"][get_class($this)]) || $vf["exc"][get_class($this)])){
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
            Logger::excLog($this);
        }
    }