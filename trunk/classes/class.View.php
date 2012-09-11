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
 * Класс отвечающий за представление.
 * 
 * @author Костин Алексей Васильевич
 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
 * @version 2.0
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @category VoltCore
 * @package VoltCoreClasses
 * @subpackage OtherClasses
 */
class View {
	
	/**
	 * Возможные типы представления
	 * @var array 
	 */
	public static $types = array('json', 'html', 'test');
	
	/**
	 * Тип представления
	 * @var string 
	 */
	protected $type;
	
    /**
     * Конструктор. Устанавливает тип представления по умолчанию 
     */
    public function __construct(){
        $this->type = Registry::getConfig()->getVC('viewType');
    }
    
    /**
     * Устанавливает тип представления
     * 
     * @param string $type Тип представления
     */
    public function setViewType($type){
        if (in_array($type, self::$types)){
            $this->type = $type;
        }
    }
    
	/**
	 * Выдает данные в нужном виде. 
	 */
	public function show(){
		$response = Registry::getResponse();
        $response->addDebugData();
        switch($this->type){
			case 'json':
				echo json_encode($response->getClearData());
			break;
			case 'html':
                echo $response;
			break;
			case 'test':
                if (Registry::getConfig()->testMode){
                    var_dump($response->getResponseData());
                }
			break;
		}
	}
}