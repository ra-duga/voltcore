<?php
	/**
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 	 * @version 1.0
	 * @category VoltCore
	 * @package VoltCoreFiles
	 * @subpackage Classes
	 */

	/**
	 * Класс единственного процесса.
	 * 
	 * Класс позволяет реализовать схему, когда конкретный процесс(например посылка писем)
	 * в каждый момент времени работате в единственном экземпляре.   
	 * 
	 * @category VoltCore
	 * @package VoltCoreClasses
	 * @subpackage OtherClasses
	 */
	abstract class SingleProcess{
		
		/**
		 * Статус "работает"
		 * @var int
		 */
		const WORK_STATUS=1;

		/**
		 * Статус "не работает"
		 * @var int
		 */
		const STOP_STATUS=0;

		/**
		 * Стратегия прав доступа к процессу.
		 * @var object
		 */
		protected $rights;
		
		/**
		 * Объект для работы с БД.
		 * @var object
		 */
		protected $db;
		
		/**
		 * Файл с логами процесса.
		 * @var string
		 */
		protected $logFile;

		/**
		 * Файл с прогрессом процесса.
		 * @var string
		 */
		protected $progressFile;

		/**
		 * Файл-индикатор работы процесса.
		 * @var string
		 */
		protected $workFile;
		
		/**
		 * Возвращает объект для работы с БД.
		 * 
		 * @return SQLDB Объект для работы с БД.
		 */
		protected function getDB(){
			if(!$this->db){
				$db=SQLDBFactory::getDB();
			}
			return $db;
		}

		/**
		 * Выполняется при попытке запустить второй экземпляр процесса.
		 * 
		 * @return mixed Значение, которое должно быть передано инициализатору процесса.
		 */
		protected function alreadyWork(){return null;}

		/**
		 * Заканчивает рабоу процесса. 
		 */
		protected function endWork(){}
		
		/**
		 * Вызывается при завершении процесса из-за выкинутого исключения.
		 * 
		 * @param Exception $e Исключение.
		 */
		protected function errorEnd($e){
			$this->logError($e->getMessage());
		} 
		
		/**
		 * Выполняет основные действия процесса. 
		 */
		abstract protected function doActions();

		/**
		 * Конструктор.
		 * 
		 * @param UserRights $rights Стратегия прав пользователя.
		 * @param SQLDB $db Объект для работы с БД.
		 */
		public function __construct($rights=null, $db=null){
			$this->db=$db; 
			$this->rights=$rights ? $rights : new DefaultUserRights();

			$this->logFile=EVENTDIR."/".get_class($this).".log";
			$this->workFile=EVENTDIR."/".get_class($this).".work";
			$this->progressFile=EVENTDIR."/".get_class($this)."Progress.log";
		}
		
		/**
		 * Основной метод обработки.
		 */
		public function run(){
			$may=$this->rights->may(get_class($this));
			if ($may!==true){
				return $may;
			}
			
			if(SingleProcess::getStatus(get_class($this))==SingleProcess::WORK_STATUS){
				return $this->alreadyWork();
			}
			
			$this->setStatus(SingleProcess::WORK_STATUS);
			try{		
				$this->logProgress("Инициализируюсь");
				$this->init();
				$this->logProgress("Начинаю работу");
				$this->doActions();
				$this->logProgress("Заканчиваю работу");
				$this->endWork();
			}catch(Exception $e){
				$this->errorEnd($e);
			}
			$this->setStatus(SingleProcess::STOP_STATUS);
			$this->logProgress("Все сделал");
		}
		
		/**
		 * Инициализация процесса.
		 */
		protected function init(){
			smartUnlink($this->logFile);
			smartUnlink($this->progressFile);
		}
		
		/**
		 * Определяет, должен ли процесс остановиться.
		 * 
		 * @return boolean true - если надо остановиться, false - в противном случае 
		 */
		protected function mustStop(){
			if (SingleProcess::getStatus(get_class($this))==SingleProcess::WORK_STATUS){			
				return false;
			}else{
				return true;
			}
		}
		
		/**
		 * Возвращает статус работы процесса.
		 * 
		 * @param string $class Класс процесса.
		 * @return int Статус индексатора.
		 */
		public static function getStatus($class){
			if (file_exists(EVENTDIR."/".$class.".work")){
				return SingleProcess::WORK_STATUS; 
			}else{
				return SingleProcess::STOP_STATUS; 
			}
		}
		
		/**
		 * Установка статуса индексатора.
		 * 
		 * @param int $status Новый статус
		 */
		public function setStatus($status){
			switch ($status){
				case SingleProcess::WORK_STATUS:
					makeDirs($this->workFile);
					file_put_contents($this->workFile,"");
					break;
				case SingleProcess::STOP_STATUS:
					smartUnlink($this->workFile);
					break;
				default :
					throw new FormatException("Статус указан неверно","Неверные данные");
			}
		}
		
		/**
		 * Логирует ход работы процесса.
		 * 
		 * @param string $msg Сообщение которое нужно залогировать.
		 */
		protected function logProgress($msg){
			logToFile($msg, $this->progressFile);
		}
		
		/**
		 * Логирует ошибку в файл.
		 * 
		 * @param string $msg Сообщение для записи.
		 * @param string $type Тип ошибки.
		 */
		protected function logError($msg, $type='Ошибка в данных'){
			logToFile($msg, $this->logFile, $type);
		}
	}