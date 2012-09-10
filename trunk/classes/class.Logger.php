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
 * Класс логирования
 * 
 * @author Костин Алексей Васильевич
 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
 * @version 2.0
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @category VoltCore
 * @package VoltCoreClasses
 * @subpackage OtherClasses
 */
class Logger{
    
	/**
	 * Логирование в файл. 
	 */
	const TYPE_FILE = 1;

	/**
	 * Логирование во внутренний лог. 
	 */
	const TYPE_LOG = 2;

    /**
     * Лог
     * @var array 
     */
    private static $log = array();

    /**
     * Настройки логгера
     * @var array 
     */
    private static $conf;
    
    /**
     * Инициализирует логгер 
     */
    public static function init(){
        self::$conf = Registry::getConfig()->getVC('log');
    }
    
	/**
	 * Возвращает текущее состояние лога
	 * 
	 * @return array 
	 */
	public static function getLog(){
		return self::$log;
	}
    
    /**
     * Записывает сообщение в файл.
     * 
     * Функция записывает сообщение $mes с типом $type и параметрами $maspar в файл $file.
     * Файл находится в директории $vf["log"]["dir"]. В случае отсутствия файла - файл создается.
     * Сообщение записывается в виде $mes."|".$type."|".$maspar."|".date("d-m-Y H:i:s").PHP_EOL.
     *  
     * @param string $mes Сообщение для логирования
     * @param string $file Файл в который писать лог
     * @param string $type Тип сообщения.
     * @param array $masPar Дополнительные данные которые надо приписать к сообщению.
     */
    public static function logToFile($mes, $file, $type='debug', $masPar=null){
        
        if (is_array($masPar)) {
            $masPar=implode("|",$masPar);
        }
        $logText=$mes."|".$masPar."|".$type."|".date("d-m-Y H:i:s").PHP_EOL;

		if(self::$conf['type'] & self::TYPE_FILE){
            makeDirs($file);
            file_put_contents($file, $logText, FILE_APPEND);
		}
		if(self::$conf['type'] & self::TYPE_LOG){
			self::$log[] = $logText;
        }
    }

    /**
     * Создает на основе исключения cообщение и записывает его в файл. 
     * 
     * Функция записывает информацию об исключении в файл $file.
     * Файл находится в директории $vf["log"]["dir"]. В случае отсутствия файла - файл создается.
     * Сообщение записывается в виде $mes."|".$type."|File: ...; Line:...; |".date("d-m-Y H:i:s").PHP_EOL.
     * 
     * @param Exception $e исключение для логирования
     * @param string $file Файл в который писать лог
     */
    public static function excToFile($e, $file){
        $par  = "File:".$e->getFile().";";
        $par .= "Line:".$e->getLine().";";
        $type='debug';
        if (method_exists($e, "getType")){
            $type=$e->getType();
        }
        if (method_exists($e, "getSql")){
            $par=$e->getSql()."|".$par;
        }
        
        self::logToFile($e->getMessage(), $file, $type, $par);
    }
        
    /**
     * Создает на основе исключения cообщение и записывает его в стандартный файл. 
     * 
     * Функция записывает информацию об исключении в файл указанный в $vf["log"][$fil].
     * Файл находится в директории $vf["log"]["dir"]. В случае отсутствия файла - файл создается.
     * Сообщение записывается в виде $mes."|".$type."|File: ...; Line:...; |".date("d-m-Y H:i:s").PHP_EOL.
     * 
     * @param Exception $e исключение для логирования.
     * @param string $extMsg Дополнительное сообщение.
     */
    public static function excLog($e, $extMsg){
        $par  = "File:".$e->getFile().";";
        $par .= "Line:".$e->getLine().";";
        $type='debug';
        if ($e instanceof VoltException){
            $type=$e->getType();
        }
        if ($e instanceof SqlException){
            $par=PHP_EOL.$e->getSql().PHP_EOL."|".$par;
        }
        $fil=get_class($e);
        if(!in_array($fil, self::$conf)){
            $fil=get_parent_class($e);
        }
        if(!in_array($fil, self::$conf)){
            $fil='log';
        }
        $par .= '|'.$extMsg;
        self::logMsg($e->getMessage(), $type, $fil, $par);
    }
        
    /**
     * Записывает сообщение в стандартный файл. 
     *
     * Функция записывает сообщение $mes с типом $type и параметрами $maspar в файл указанный в $vf["log"][$fil].
     * Файл находится в директории $vf["log"]["dir"]. В случае отсутствия файла - файл создается.
     * Сообщение записывается в виде $mes."|".$type."|".$maspar."|".date("d-m-Y H:i:s").PHP_EOL.
     * 
     * @param string $mes Сообщение для логирования.
     * @param string $type Тип сообщения.
     * @param string $fil Ключ в массиве $vf["log"], соответствующий нужному файлу.
     * @param array $maspar Дополнительные данные которые надо приписать к сообщению.
     */
    public static function logMsg($mes, $type='debug', $fil='log', $masPar=null){
        if (is_array($masPar)) {
            $masPar=implode("|",$masPar);
        }
        $logText=$mes."|".$masPar."|".$type."|".date("d-m-Y H:i:s")."\r\n";
        $logFile=self::$conf[$fil];
        
		if(self::$conf['type'] & self::TYPE_FILE){
            makeDirs($logFile);
            file_put_contents($logFile, $logText, FILE_APPEND);
		}
		if(self::$conf['type'] & self::TYPE_LOG){
			self::$log[] = $logText;
        }
    }
    
    /**
     * Логирует переменную
     * 
     * Функция записывает информацию о переменной в файл указанный в $vf["log"]["var"].
     * Файл находится в директории $vf["log"]["dir"]. В случае отсутствия файла - файл создается.
     * Сообщение записывается в виде $name." => ".var_export($var)."|".$type."|".$maspar."|".date("d-m-Y H:i:s").PHP_EOL. 
     * 
     * @param string $name Имя переменной
     * @param mixed $var Переменная для логирования
     * @param bool $return Нужно вернуть результат(true) или залогировать(false).
     * @param string $type Тип сообщения.
     * @param array $maspar Дополнительные данные которые надо приписать к сообщению.
     * @return string Если $return=true, то возвращается строка вида $name." => ".var_dump($var);
     */
    public static function logVar($var, $name='par', $return=false, $type='debug', $masPar=null){
        ob_start();
            var_dump($var);
        $msg=ob_get_clean();
        if ($return){
            return $name." => ".$msg;
        }else{
            self::logMsg($name." => ".$msg, $type, 'var', $masPar);
        }
    }
}