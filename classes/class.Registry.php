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
     * Подключение к БД.
     * @var SQLDB
     */
    private $db = null;

    /**
     * Объект ошибок.
     * @var Error
     */
    private $error = null;

    /**
     * Объект авторизации.
     * @var Auth
     */
    private $auth = null;

    /**
     * Объект ответа.
     * @var Response
     */
    private $response = null;

    /**
     * Объект представления.
     * @var View
     */
    private $view = null;
        
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
     * Магическое получение данных из реестра.
     * 
     * @param string $var Имя переменной
     * @return mixed Значение переменной
     */
    public function __get($var){
		return $this->get($var);
	}
	
    /**
     * Получение данных из реестра.
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
     * Получение объекта настроек.
     * 
     * @return Config Объект настроек
     */
	public static function getConfig(){
        $r = self::getInstance();
        if(!isset($r->config)){
            global $vf, $siteConf;
            $r->config = new Config($vf, $siteConf);
		}
		return $r->config;
	}
    
	/**
     * Получение подключения к БД.
     * 
     * @return SQLDB Подключение к БД
     */
	public static function getDB(){
        $r = self::getInstance();
		if(!isset($r->db)){
			$r->db = SQLDBFactory::getDB();
		}
		return $r->db;
	}

	/**
     * Получение объекта ошибок.
     * 
     * @return Error Объект ошибок
     */
	public static function getError(){
        $r = self::getInstance();
        if(!isset($r->error)){
			$r->error = new Error();
		}
		return $r->error;
	}

	/**
     * Получение объекта авторизации.
     * 
     * @return Auth Объект авторизации
     */
	public static function getAuth(){
        $r = self::getInstance();
        if(!isset($r->auth)){
			$r->auth = new Auth();
		}
		return $r->auth;
	}

	/**
     * Получение объекта запроса.
     * 
     * @return Request Объект запроса
     */
	public static function getRequest(){
        $r = self::getInstance();
        if(!isset($r->request)){
			$r->request = new Request();
		}
		return $r->request;
	}
    
    /**
     * Получение объекта ответа.
     * 
     * @return Response Объект ответа
     */
	public static function getResponse(){
        $r = self::getInstance();
        if(!isset($r->response)){
			$r->response = new Response();
		}
		return $r->response;
	}

	/**
     * Получение объекта представления.
     * 
     * @return View Объект представления
     */
	public static function getView(){
        $r = self::getInstance();
        if(!isset($r->view)){
			$r->view = new View();
		}
		return $r->view;
	}
    
    
    /**
     * Магическая запись в реестр.
     * 
     * @param string $var Имя переменной
     * @param mixed $val Значение переменной
     */
    public function __set($var, $val){
        $this->set($var, $val);
    }
	
    /**
     * Запись в реестр.
     * 
     * @param string $var Имя переменной
     * @param mixed $val Значение переменной
     */
    public function set($var, $val){
        $this->vars[$var] = $val;
    }
	
}