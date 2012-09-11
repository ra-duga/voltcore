<?php
abstract class AbstractController{
	
	/**
	 * Данные пришедшие от MainController'а
	 * @var array 
	 */
	protected $mainData;
	
    /**
     * Обработка по умолчанию.
     */
    protected function getIndexInfo(){
        Registry::getView()->show404();
    }
    
    protected function setErrorInfo($data){
        Registry::getView()->show404();
        $file = EVENTDIR.'/wrongAction.log';
        $type = 'Запрос несуществующего действия';
        Logger::logToFile($data, $file, $type,$_SERVER['REMOTE_ADDR']);
        
    }
    
	/**
	 * Определяет какой метод вызвать, выполняет полезную работу
	 */
	public function compileResponse($data = null){
        $this->mainData = $data;
        $requestAction = Registry::getRequest()->getAction();
        $action =  $requestAction  ?  $requestAction  : 'index';
        $action =  $data['action'] ? $data['action'] : $action;
        $method = "set".ucfirst($action)."Info";
        
        if (method_exists($this, $method)){
            $this->$method();
        }else{
            $this->setErrorInfo($method);            
        }

    }    

	/**
	 * Создает массив ответа при удачном выполнении запроса.
	 * 
	 * @param array $data Данные для ответа
	 * @return array Успешный ответ. 
	 */
	protected function success($data = array(), $dopData = array()){
		if (!isset($data['msg'])){
			$data['msg'] = 'Сделано!';
		}
		$rez = array_merge(array('success' => true, 'errors' => array(), 'data' => $data), $dopData);
		return $rez;
	}

	/**
	 * Создает массив ответа при неудачном выполнении запроса.
	 * 
	 * @param mixed $errors Строка с ошибкой или массив ошибок
	 * @param array $data Данные для ответа
	 * @return array Успешный ответ. 
	 */
	protected function fail($errors, $data = array(), $dopData = array()){
		global $conf;
		if (is_string($errors)){
			$key = array_search($errors, $conf['error']['codes']);
			if ($key === false){
				$key = 0;
			}
			$errorsArr[] = array('msg' => $errors, 'code' => $key);
		}else{
			$errorsArr = $errors;
		}
		$rez = array_merge(array('success' => false, 'errors' => $errorsArr, 'data' => $data), $dopData);
		return $rez;
	}
}