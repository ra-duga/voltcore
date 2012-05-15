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
    private $get = null;

    /**
     * Массив POST данных.
     * @var array 
     */
    private $post = null;

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
     * @var type 
     */
    private $url = '';
    
    /**
     * Возвращает данное $var из массива $container.
     * @param string $container Имя массива с данным.
     * @param string $var Имя данного.
     * @return mixed Данное или сам массив при отсутствии данного.
     */
    private function get($container, $var = null){
        if(is_null($this->$container)){
            $globalArr = "_".strtoupper($container);
            $this->$container = $$globalArr;
        }
        if (is_null($var)){
            return $this->$container;
        }
        return $this->$container[$var];
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
     * @return string 
     */
    public function getPostRawData(){
        if(is_null($this->postData)){
            $this->postData = file_get_contents('php://input');
        }
        return $this->postData;
    }
    
    public function getEnterUrl(){
        if (is_null($this->url)){
            $this->url = $_SERVER['REQUEST_URI'];
        }
        return $this->url;
    }
    
    /**
     * Конструктор.
     * 
     * @param array $data Данные запроса.
     */
    public function __construct($data = array()){
        if (!$data) return;

        $this->get      = $data['get'];
        $this->post     = $data['post'];
        $this->cookie   = $data['cookie'];
        $this->server   = $data['server'];
        $this->postData = $data['postData'];
        $this->url      = $data['url'];
    }
}