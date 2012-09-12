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
 * Класс главного контроллера.
 * 
 * @author Костин Алексей Васильевич
 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
 * @version 2.0
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @category VoltCore
 * @package VoltCoreClasses
 * @subpackage Controllers
 */
class GlobalController extends AbstractController {
	
	/**
	 * Контроллер для запрошенного тействия.
	 * @var AbstractController 
	 */
	protected $controller;
	
	/**
	 * Данные для передачи контроллеру при вызове.
	 * @var array 
	 */
	protected $controllerData;

	/**
	 * Инициализация. 
	 */
	protected function init(){
        Logger::init();
		$a = Registry::getAuth();
		$a->init();
	
		Registry::getView()->setViewType(Registry::getRequest()->getREQUEST('viewType'));
	}

	/**
	 * Действия до основных. 
	 */
	protected function beforeCompile(){
		
	}
	
	/**
	 * Действия после основных. 
	 */
	protected function afterCompile(){
		
	}
	
    /**
     * Указывает показать страницу с ошибкой
     * 
     * @param Exception $data Неперехваченное контроллером исключение.
     */
    protected function setErrorInfo($data){
        Registry::getResponse()->showError($data->getMessage());
    }
    
	/**
	 * Принимает запрос, проверяет права, запускает нужную обработку и возвращает результат.
	 * 
	 * @global array $conf Настройки.
	 * @return View Представление для отображения. 
	 */
	public function compileResponse(){
		try{
			$this->init();
            $this->loadController();
            $this->beforeCompile();
            if($this->controller){
                $this->controller->compileResponse($this->controllerData);
            }
			$this->afterCompile();
		}catch(Exception $e){
			Error::addException($e);
            $this->setErrorInfo($e);
		}
	}
    
    /**
     * Выбирает и устонавливает нужный контроллер
     */
    protected function loadController(){
        $controllerName = Registry::getRequest()->getController(); 
        if (!$controllerName){
            $controllerName = Registry::getConfig()->defaultController;
        }
        $controllerName   = ucfirst($controllerName);
        $this->controller = new $controllerName;

        if (!$this->controller || !($this->controller instanceof AbstractController)){
            $this->controller = null;
            Logger::logToFile("Не удалось загрузить контроллер".$controllerName, EVENTDIR."/wrongController.log", 'Несуществующего контроллера или не контроллера');
            Registry::getResponse()->show404();
        }
        
    }
}