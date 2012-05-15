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
 * Класс конфигурации системы.
 * 
 * @category VoltCore
 * @package VoltCoreClasses
 * @subpackage OtherClasses
 */

class Config {

    /**
     * Конфигурация фреймворка.
     * 
     * @var type array
     */
    private $VCConf = array();

    /**
     * Конфигурация сайта.
     * 
     * @var type array
     */
    private $siteConf = array();

    /**
     * Конструктор.
     */
    public function __construct($fConf, $sConf){
        $this->VCConf   = $fConf;
        $this->siteConf = $sConf;
    }
    
    /**
     * Получение данных из конфига.
     * 
     * @param string $var Имя переменной
     * @return mixed Значение переменной
     */
    public function __get($var){
        if ($var == 'VCConf')   return $this->VCConf;
        if ($var == 'siteConf') return $this->siteConf;
        
        if(isset($this->VCConf[$var])){
            return $this->VCConf[$var];
        }elseif(isset($this->siteConf[$var])){
            return $this->siteConf[$var];
        }else{
            return null;
        }
    }  
    
    /**
     * Возвращает данное из конфигурации фреймворка.
     * 
     * @param string $var Имя переменной.
     * @return mixed Требуемое данное. 
     */
    public function getVC($var){
        if(isset($this->VCConf[$var])){
            return $this->VCConf[$var];
        }else{
            return null;
        }
    }

    /**
     * Возвращает данное из конфигурации сайта.
     * 
     * @param string $var Имя переменной.
     * @return mixed Требуемое данное. 
     */
    public function getSite($var){
        if(isset($this->siteConf[$var])){
            return $this->siteConf[$var];
        }else{
            return null;
        }
    }
}