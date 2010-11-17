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
	 * Класс для тестирования одиночного процеса.
	 *  
	 * @category VoltCore
	 * @package VoltCoreClasses
	 * @subpackage Tests
	 */
	class SingleProcess_Tester extends Tester{
		
		public function goTest(){
			global $vf;
			$this->printHeader("SingleProcess тестер!");
			
			//TODO Сделать так, чтобы строка выполнялась и под Linux.
			$fileAddr=str_replace("/","\\",VCROOT);
			pclose(popen("start /B php $fileAddr\\classes\\tests\\file.runSingleProcessTest.php", 'r'));
			sleep(2);
			$proc=new SingleProcessTest("2", new AdminRights());
			$proc->run();
			
			$file=file_get_contents(EVENTDIR."/SingleProcessTest.log");
			$file=explode("|",$file);
			$file=$file[0];
			$this->check($file,"Уже запущен 2","Проверка на невозможность запустить процесс второй раз");
			
			$vf['security']['userRights']=0;
			$proc=new SingleProcessTest("3");
			$rez=$proc->run();
			$this->check($rez,"Вы не можете выполнить данное действие.","Проверка на невозможность запустить процесс из-за отсутствия прав");

			$this->printEnd("SingleProcess тестер!");
		} 
		
	}

	/**
	 * Конкретный класс SingleProcess для тестов.
	 * 
	 * @category VoltCore
	 * @package VoltCoreClasses
	 * @subpackage Tests
	 */
	class SingleProcessTest extends SingleProcess{

		public function __construct($data, $rights=null){
			$this->data=$data;
			parent::__construct($rights);
			$this->logProgress("Создался ".$this->data);
		}
		
		protected function doActions(){
			$this->logProgress("Засыпаю ".$this->data);
			sleep(10);
			$this->logProgress("Просыпаюсь ".$this->data);
		}
		
		protected function errorEnd($e){
			$this->logError($e->getMessage()." ".$this->data);
		} 
		
		
		protected function init(){
			parent::init();
			$this->logProgress("Инициализация ".$this->data);
		}
		
		protected function endWork(){
			$this->logProgress("Завершился ".$this->data);
		}
		
		protected function alreadyWork(){
			$this->logError("Уже запущен ".$this->data);
		}
		
	}