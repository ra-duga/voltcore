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
 * Класс ответа.
 * 
 * @author Костин Алексей Васильевич
 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
 * @version 2.0
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @category VoltCore
 * @package VoltCoreClasses
 * @subpackage OtherClasses
 */
class Response {
	
    /**
     * Буфер переменных.
     * @var array
     */
    private $vars = array();

    /**
     * Магическое получение данного из ответа
     * 
     * @param string $var Имя переменной
     * @return mixed Значение переменной
     */
    public function __get($var){
		return $this->get($var);
	}

    /**
     * Получение данного из ответа
     * 
     * @param string $var Имя переменной
     * @return mixed Значение переменной
     */
	public function get($var){
        if(isset($this->vars[$var])){
            return $this->vars[$var];
        }else{
            return null;
        }
	}
    
    /**
     * Магическая запись в ответ.
     * 
     * @param string $var Имя переменной
     * @param mixed $val Значение переменной
     */
    public function __set($var, $val){
        $this->set($var, $val);
    }
	
    /**
     * Запись в ответ.
     * 
     * @param string $var Имя переменной
     * @param mixed $val Значение переменной
     */
    public function set($var, $val){
        $this->vars[$var] = $val;
    }

    /**
     * Возвращает данные ответа
     * 
     * @return array Запрошенные данные
     */
    public function getResponseData(){
        return $this->vars;
    }
    
}