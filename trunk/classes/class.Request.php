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
 * Класс запроса.
 * 
 * @category VoltCore
 * @package VoltCoreClasses
 * @subpackage OtherClasses
 */

class Request {

    /**
     * Массив GET данных.
     * @var array 
     */
    private $get = array();

    /**
     * Массив POST данных.
     * @var array 
     */
    private $post = null;

    /**
     * Массив REQUEST
     * @var array 
     */
    private $request = null;

    /**
     * Массив COOKIE.
     * @var array 
     */
    private $cookie = null;

    /**
     * Массив SERVER.
     * @var array 
     */
    private $server = null;

    /**
     * Данные пришедшие POST'ом
     * @var string 
     */
    private $postData = '';
    
    /**
     * Адрес по которому пришли.
     * @var string 
     */
    private $url = '';

    /**
     * Адрес по которому пришли, за вычетом строки запроса.
     * @var string 
     */
    private $clearUrl = '';
    
    /**
     * Какой конроллер надо вызвать
     * @var string
     */
    private $controller;
    
    /**
     * Какое действие должен выполнить контроллер
     * @var string
     */
    private $action;
    
    /**
     * Возвращает данное $var из массива $container.
     * 
     * @param string $container Имя массива с данным.
     * @param string $var Имя данного.
     * @return mixed Данное или сам массив при отсутствии данного.
     */
    private function get($container, $var = null){
        if(is_null($this->$container)){
            switch($container){
                case 'get'    : $globalArr = $_GET;     break;
                case 'post'   : $globalArr = $_POST;    break;
                case 'cookie' : $globalArr = $_COOKIE;  break;
                case 'server' : $globalArr = $_SERVER;  break;
                case 'request': $globalArr = $_REQUEST; break;
            }
            $this->$container = $globalArr;
        }
        if (is_null($var)){
            return $this->$container;
        }
        
        return isset($this->$container[$var]) ? $this->$container[$var] : null;
    }
    
    /**
     * Возвращает GET данное.
     * 
     * @param string $var Имя данного.
     * @return mixed Данное или сам массив GET, если имя данного не указано. 
     */
    public function getGET($var = null){
        return $this->get('get', $var);
    }

    /**
     * Возвращает POST данное.
     * 
     * @param string $var Имя данного.
     * @return mixed Данное или сам массив POST, если имя данного не указано. 
     */
    public function getPOST($var = null){
        return $this->get('post', $var);
    }

    /**
     * Возвращает Cookie данное.
     * 
     * @param string $var Имя данного.
     * @return mixed Данное или сам массив COOKIE, если имя данного не указано. 
     */
    public function getCookie($var = null){
        return $this->get('cookie', $var);
    }
    
    /**
     * Возвращает данное если оно пришло от пользователя.
     * 
     * @param string $var Имя данного
     * @return mixed Данное или совокупность GET, POST и COOKIE данных, если имя данного не указано.  
     */
    public function getREQUEST($var = null){
        if(!$var){
            return $this->get('request');
        }
        
        $val = $this->getGET($var);
        if ($val){
            return $val;
        }
        
        $val = $this->getPOST($var);
        if ($val){
            return $val;
        }

        $val = $this->getCookie($var);
        return $val;
    }
    
    /**
     * Возвращает Server данное.
     * 
     * @param string $var Имя данного.
     * @return mixed Данное или сам массив SERVER, если имя данного не указано. 
     */
    public function getServer($var = null){
        return $this->get('server', $var);
    }
    
    /**
     * Возвращает данные пришедшие POST'ом.
     * 
     * @return string Строка POST данных
     */
    public function getPostRawData(){
        if(is_null($this->postData)){
            $this->postData = file_get_contents('php://input');
        }
        return $this->postData;
    }
    
    /**
     * URL, по которому пришли на страницу
     * 
     * @return string URL 
     */
    public function getEnterUrl(){
        if (is_null($this->url)){
            $this->url = $_SERVER['REQUEST_URI'];
        }
        return $this->url;
    }
    
    /**
     * URL, по которому пришли на страницу, за вычетом строки запроса
     * 
     * @return string URL 
     */
    public function getEnterUrlWithoutQuery(){
        if (is_null($this->clearUrl)){
            $this->clearUrl = str_replace($_SERVER['QUERY_STRING'],'', $_SERVER['REQUEST_URI']);
        }
        return $this->clearUrl;
    }
    
    /**
     * Вохвращает имя контроллера, который должен обработать запрос
     * 
     * @return string Имя контроллера
     */
    public function getController(){
        return $this->controller;
    }

    /**
     * Вохвращает имя действия, которое должен выполнить контроллер
     * 
     * @return string Имя контроллера
     */
    public function getAction(){
        return $this->action;
    }
    
    /**
     * Конструктор.
     * 
     * @param array $data Данные запроса.
     */
    public function __construct($data = array()){
        if (!$data){
            $this->get = $_GET;
            $this->parseUrlData();
        }else{
            $this->get      = $data['get'];
            $this->post     = $data['post'];
            $this->cookie   = $data['cookie'];
            $this->request  = array_merge($data['get'], $data['post'], $data['cookie']);
            $this->server   = $data['server'];
            $this->postData = $data['postData'];
            $this->url      = $data['url'];
        }
    }
    
    /**
     * Вытаскивает данные из URL'а
     */
    private function parseUrlData(){
        $this->controller = null;
        $this->action     = null;
        $url = trim($this->getEnterUrlWithoutQuery(),'/');
        $urlParts = explode('/', $url);
        $c = count($urlParts);
        if ($c<1){
            return;
        }

        $this->controller = $urlParts[0];

        $params = array();
        for($i = $c-1; $i > 0;$i = $i-2){
            $params[$urlParts[$i-1]] = $urlParts[$i];
        }

        //Если количество частей оказалось четным, значит есть action и его надо удалить из get'a
        if ($i != 0){
            $this->action = $urlParts[1];
            unset($params[$urlParts[0]]);
        }
        $this->get = array_merge($this->get, $params);
    }
}