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
        Registry::getResponse()->show404();
    }
    
    protected function setErrorInfo($data){
        Registry::getResponse()->show404();
        $logConf = Registry::getConfig()->log;
        if($logConf['logUnknownAction']){
            $file = EVENTDIR.'/wrongAction.log';
            $type = 'Запрос несуществующего действия';
            Logger::logToFile($data, $file, $type,$_SERVER['REMOTE_ADDR']);
        }
        
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
}