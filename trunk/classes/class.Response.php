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
class Response extends Template{
	
    /**
     * Конструктор
     */
    public function __construct(){
        //TODO Добавить в конфиг
        $tpl = Registry::getConfig()->tpl;
        parent::__construct($tpl['global']);
        //TODO Добавить в конфиг
        $this->set('content', new Template($tpl['defaultContent']));
        $this->set('js', array());
        $this->set('css', array());
        $this->set('dopJs', '');
        
    }

    /**
     * Посылает заголовок "404 Not Found" и устанавливает шаблон 404 ошибки
     */
    public function show404(){
        header("HTTP/1.0 404 Not Found"); 
        header("HTTP/1.1 404 Not Found"); 
        header("Status: 404 Not Found");
        $tpl = Registry::getConfig()->tpl;
        $this->setTpl($tpl['404']);
    }

    /**
     * Посылает заголовок "403 Forbidden" и устанавливает шаблон 403 ошибки
     */
    public function show403(){
        header("HTTP/1.0 403 Forbidden"); 
        header("HTTP/1.1 403 Forbidden"); 
        header("Status: 403 Forbidden");
        $tpl = Registry::getConfig()->tpl;
        $this->setTpl($tpl['403']);
    }
    
    /**
     * Устанавливает шаблон страницы ошибки
     */
    public function showError($msg){
        $tpl = Registry::getConfig()->tpl;
        $this->setTpl($tpl['error']);
        $this->setContentVar('msg', $msg);
    }
    
    /**
     * Устанавливает шаблон контента
     * @param type $tpl
     */
    public function setTpl($tpl){
        $c = $this->get('content');
        $c->setPath($tpl);
    }
    
    /**
     * Магическое получение значения переменной из контента
     * 
     * @param string $var Имя переменной
     * @return mixed Значение переменной
     */
    public function __get($var){
        return $this->getContentVar($var);
    }
    
    /**
     * Магическая запись переменной в контент.
     * 
     * @param string $var Имя переменной
     * @param mixed $val Значение переменной
     */
    public function __set($var, $val){
        $this->setContentVar($var, $val);
    }

    /**
     * Получение значения переменной из контента.
     * 
     * @param string $var Имя переменной
     * @return mixed Значение переменной
     */
    public function getContentVar($var){
        $c = $this->get('content');
        return $c->$var;
    }
    
    /**
     * Запись переменной в контент.
     * 
     * @param string $var Имя переменной
     * @param mixed $val Значение переменной
     */
    public function setContentVar($var, $val){
        $c = $this->get('content');
        $c->$var = $val;
    }
    
    
    /**
     * Возвращает данные ответа
     * 
     * @return array Запрошенные данные
     */
    public function getResponseData(){
        return $this->vars;
    }
    
	/**
	 * Добавляет ошибки и лог в ответ. 
	 */
	public function addDebugData(){
		$errors = Registry::getError()->getErrors();
		$eConf  = Refistry::getConfig()->error;
        if ($eConf['toOutput'] && $errors){
			$this->set('sysErrors', $errors);
		}
		$logs = Logger::getLog();
		$lConf  = Refistry::getConfig()->log;
		if ($lConf['toOutput'] && $logs){
			$this->set('log', $logs);
		}
	}
    
	/**
	 * Устанавливает статус запроса как "Успешный"
	 * 
	 * @param string msg Сообщение об успехе
	 */
	public function success($msg = 'Сделано!'){
        $this->set('success', true);
        $this->set('msg', $msg);
	}

	/**
	 * Устанавливает статус запроса как "Неуспешный"
	 * 
	 * @param mixed $errors Строка с ошибкой или массив ошибок
	 * @param string msg Сообщение о провале
	 */
	public function fail($errors, $msg = ''){
		global $conf;
		if (is_string($errors)){
			$errors = array($errors);
		}
        
        $conf = Registry::getConfig()->error;
        $errorsArr = array();
        foreach($errors as $err){
            if(isset($conf['messeges'][$err])){
    			$errorsArr[] = array('msg' => $conf['messeges'][$err], 'code' => $err);
            }else{
    			$errorsArr[] = array('msg' => $conf['messeges'][0], 'code' => $err);
            }
        }
            
        $this->set('success', false);
        $this->set('msg', $msg);
        $this->setErrors($errorsArr);
	}
    
    /**
	 * Добавляет js в шаблон.
	 * 
	 * @param string $js Js файл для добавления
	 */
	public function addJs($js){
		$locJs = $this->get('js');
        if (is_array($js)){
			$locJs = array_merge($locJs, $js);
		}else{
			$locJs[] = $js;
		}
        $this->set('js', $locJs);
	}

	/**
	 * Добавляет css в шаблон.
	 * 
	 * @param string $css Css файл для добавления
	 */
	public function addCss($css){
		$locCss = $this->get('css');
		if (is_array($css)){
			$locCss = array_merge($locCss, $css);
		}else{
			$locCss[] = $css;
		}
	}
	
	/**
	 * Добавляет js код в шаблон
	 * 
	 * @param string $str Дополнительный js.
	 */
	public function addDopJs($str){
		$dj  = $this->get('dopJs');
        $dj .= $str;
        $this->set('dopJs', $dj);
	}
}