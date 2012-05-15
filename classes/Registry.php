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
 * Класс реестра.
 * 
 * @category VoltCore
 * @package VoltCoreClasses
 * @subpackage OtherClasses
 */

class Registry {

    /**
     * Объект реестра.
     * @var Registry
     */
    static private $instance = null;
    
    /**
     * Буфер переменных.
     * @var array
     */
    private $vars = array();
    
    /**
     * Возвращает объект реестра.
     * 
     * @return Registry Реестр.
     */
    public static function getInstance(){
        if (!self::$instance){
            self::$instance = new Registry();
        }
        return self::$instance;
        
    }
    
    /**
     * Конструктор.
     */
    private function __construct(){
        
    }
    
    /**
     * Получение данных из реестра.
     * 
     * @param string $var Имя переменной
     * @return mixed Значение переменной
     */
    public function __get($var){
        if(isset($this->vars[$var])){
            return $this->vars[$var];
        }else{
            return null;
        }
    }
    
    /**
     * Запись в реестр.
     * 
     * @param string $var Имя переменной
     * @param mixed $val Значение переменной
     */
    public function __set($var, $val){
        $this->vars[$var] = $val;
    }
}