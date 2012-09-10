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
	protected function beforeOutput(){
		
	}
	
	/**
	 * Действия после основных. 
	 */
	protected function afterOutput(){
		
	}
	
	/**
	 * Возвращает данные по запросу.
	 * 
	 * @return array Результат запроса 
	 */
	public function getOutput($data = null){
        return $this->fail('Не указано действие');
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
			$this->controller = Registry::getRequest()->getController();

			if (!$this->controller){
				$this->controller = $this;
			}
			$this->beforeOutput();

			$this->controller->compileResponse($this->controllerData);

			$this->afterOutput();
		}catch(Exception $e){
			Error::addException($e);
		}
	}
	
	/**
	 * Добавляет ошибки и лог на выход. 
	 */
	protected function addDebugData(){
		$errors = Registry::getError()->getErrors();
		$eConf = Refistry::getConfig()->error;
        if ($eConf['toOutput'] && isset($this->viewData['errors']) && is_array($this->viewData['errors'])){
			$this->viewData['errors'] = array_merge($this->viewData['errors'], $errors);
		}
		$logs = Logger::getLog();
		if ($conf['log']['toOutput'] && $logs){
			$this->viewData['log'] = $logs;
		}
	}
}