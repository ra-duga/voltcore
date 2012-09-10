<?php
abstract class AbstractController{
	
	/**
	 * Данные пришедшие от MainController'а
	 * @var array 
	 */
	protected $mainData;
	
	/**
	 * Выполняет полезнию работу 
	 */
	abstract public function getOutput($data = null);
	
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