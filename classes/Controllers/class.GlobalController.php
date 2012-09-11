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

			$this->controller->compileResponse($this->controllerData);

			$this->afterCompile();
		}catch(Exception $e){
			Error::addException($e);
		}
	}
    
    /**
     * Выбирает и устонавливает нужный контроллер
     */
    protected function loadController(){
        $controllerName = Registry::getRequest()->getController(); 
        if (!$controllerName){
            $this->controller = $this;
        }else{
            $controllerName   = ucfirst($controllerName);
            $this->controller = new $controllerName;
        }

        if (!$this->controller || !($this->controller instanceof AbstractController)){
            $this->controller = new ErrorController();
        }
        
    }
}