<?php
	/**
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @version 1.0
	 * @package classes
	 * @subpackage tests
	 */
	
	/**
	 * Класс для тестирования.
	 *  
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @package classes
	 * @subpackage tests
	 * @abstract
	 */
	 abstract class Tester{
	 	
	 	static $logFile;
	 	
	 	/**
		 * Объект для работы с БД
		 * @var SQLDB
		 */
		protected $db;
		
		/**
		 * Выводить ли информацию на экран или писать в логи.
		 * @var bool
		 */
		protected $print;
	 	
		/**
		 * Запускает общее тестирование.
		 * 
		 * @param array $arrClasses Массив с именами классов.
		 * @param bool $print Выводить ли информацию на экран или писать в логи.
		 * @param SQLDB $db Объект для работы с БД.
		 */
		static public function startTesting($arrClasses, $print=false, $db){
			Tester::$logFile=VCROOT."/reports/test_".date("d-m-Y_H-i").".log";
			//self::createDB($db);
			foreach($arrClasses as $class){
				$tester=$class."_Tester";
				$obj=new $tester($print, $db);
				$obj->goTest();
			}
		}	

	 	/**
	 	 * Определяет СУБД.
	 	 * 
	 	 * @return string Имя СУБД (mssql, mysql) 
	 	 */
	 	static protected function determineDB($db){
	 		if ($db instanceof MSSQLDB){
	 			return "mssql";
	 		}elseif($db instanceof MySQLDB){
	 			return "mysql";
	 		}else{
	 			throw new FormatException("Не удаются определить СУБД","Неверные данные");
	 		}
	 	}
	 	
	 	/**
	 	 * Создает тестовую базу данных в MS SQL Server.
	 	 */
	 	static protected function createmssql($db){
	 		global $vf;
	 		$dbName=$vf["test"]["db"];
	 		$db->exec("use master");
			$db->exec("if exists (select 'true' from sys.databases where name='$dbName') drop DATABASE $dbName");
			$db->exec("CREATE DATABASE [$dbName] ON  PRIMARY 
				( NAME = N'$dbName', FILENAME = N'D:\\MSSQLDataBases\\$dbName.mdf' , SIZE = 3072KB , FILEGROWTH = 1024KB )
 					LOG ON 
				( NAME = N'$dbName"."_log', FILENAME = N'D:\\MSSQLDataBases\\$dbName"."_log.ldf' , SIZE = 1024KB , FILEGROWTH = 10%)");
			$db->exec("EXEC dbo.sp_dbcmptlevel @dbname=N'$dbName', @new_cmptlevel=90");
			$db->exec("IF (1 = FULLTEXTSERVICEPROPERTY('IsFullTextInstalled'))
				begin
					EXEC [$dbName].[dbo].[sp_fulltext_database] @action = 'disable'
				end");
			$db->exec("ALTER DATABASE [$dbName] SET ANSI_NULL_DEFAULT OFF"); 
			$db->exec("ALTER DATABASE [$dbName] SET ANSI_NULLS OFF ");
			$db->exec("ALTER DATABASE [$dbName] SET ANSI_PADDING OFF ");
			$db->exec("ALTER DATABASE [$dbName] SET ANSI_WARNINGS OFF ");
			$db->exec("ALTER DATABASE [$dbName] SET ARITHABORT OFF ");
			$db->exec("ALTER DATABASE [$dbName] SET AUTO_CLOSE OFF ");
			$db->exec("ALTER DATABASE [$dbName] SET AUTO_CREATE_STATISTICS ON ");
			$db->exec("ALTER DATABASE [$dbName] SET AUTO_SHRINK OFF ");
			$db->exec("ALTER DATABASE [$dbName] SET AUTO_UPDATE_STATISTICS ON ");
			$db->exec("ALTER DATABASE [$dbName] SET CURSOR_CLOSE_ON_COMMIT OFF ");
			$db->exec("ALTER DATABASE [$dbName] SET CURSOR_DEFAULT  GLOBAL ");
			$db->exec("ALTER DATABASE [$dbName] SET CONCAT_NULL_YIELDS_NULL OFF ");
			$db->exec("ALTER DATABASE [$dbName] SET NUMERIC_ROUNDABORT OFF ");
			$db->exec("ALTER DATABASE [$dbName] SET QUOTED_IDENTIFIER OFF ");
			$db->exec("ALTER DATABASE [$dbName] SET RECURSIVE_TRIGGERS OFF ");
			$db->exec("ALTER DATABASE [$dbName] SET AUTO_UPDATE_STATISTICS_ASYNC OFF ");
			$db->exec("ALTER DATABASE [$dbName] SET DATE_CORRELATION_OPTIMIZATION OFF ");
			$db->exec("ALTER DATABASE [$dbName] SET PARAMETERIZATION SIMPLE ");
			$db->exec("ALTER DATABASE [$dbName] SET  READ_WRITE ");
			$db->exec("ALTER DATABASE [$dbName] SET RECOVERY FULL ");
			$db->exec("ALTER DATABASE [$dbName] SET  MULTI_USER ");
			$db->exec("ALTER DATABASE [$dbName] SET PAGE_VERIFY CHECKSUM  ");
			$db->exec("USE [$dbName]");
			$db->exec("IF NOT EXISTS (SELECT name FROM sys.filegroups WHERE is_default=1 AND name = N'PRIMARY') ALTER DATABASE [$dbName] MODIFY FILEGROUP [PRIMARY] DEFAULT");
	 	}
	 	
	 	/**
	 	 * Создает тестовую базу данных в MySQL.
	 	 */
	 	static protected function createmysql($db){
	 		global $vf;
	 		$dbName=$vf["test"]["db"];
	 		$db->exec("DROP DATABASE IF EXISTS $dbName");
	 		$db->exec("CREATE DATABASE $dbName DEFAULT CHARACTER SET = utf8");
	 	}
	 	
	 	/**
	 	 * Создает тестовую базу данных. 
	 	 */
	 	static protected function createDB($db){
	 		$subd=Tester::determineDB($db);
	 		$method="create$subd";
 			Tester::$method($db);
	 	}		
		
		/**
		 * Запускает тестирование класса.
		 */
		abstract public function goTest(); 

		/**
		 * Печатает заголовок.
		 * 
		 * @param string $header Текст заголовка.
		 * @param bool $h1 true - заголовок модуля, false - метода
		 */
		protected function printHeader($header, $h1=true){
			if ($this->print){
				if ($h1){
					echo "<h1>$header</h1>";
				}else{
					echo "<h2>$header</h2>";
				}
			}else{
				logToFile($header.PHP_EOL, Tester::$logFile, 'test');
			}
		} 
		
		/**
		 * Печатает заголовок.
		 * 
		 * @param string $header Текст заголовка.
		 * @param bool $h1 true - заголовок модуля, false - метода
		 */
		protected function printEnd($header){
			if ($this->print){
				echo "<p class='end'>Конец работы $header</p>";
			}else{
				logToFile("Конец работы $header".PHP_EOL, Tester::$logFile, 'test');
			}
		} 
		
		
		/**
		 * Печатает текст.
		 * 
		 * @param string $text Что нужно вывести.
		 */
		protected function printText($text){
			if ($this->print){
				echo "<div>$text</div>";
			}else{
				$text=strip_tags($text);
				logToFile($text.PHP_EOL, Tester::$logFile, 'test');
			}
		}
		
		/**
		 * Проверяет на равенство и выводит результат.
		 * 
		 * @param mixed $rez Результат дейсвий.
		 * @param mixed $right Правильный результат.
		 * @param string $title Заголовок.
		 */
		protected function check($rez, $right, $title){
			if ($rez==$right){
				$this->printText($title." => <span class='ok'>Успешно</span>");
			}else{
				$textRez=logVar("rez", $rez, true);
				$textRight=logVar("right", $right, true);
				$this->printText("<span class='testTitle'>$title</span> => <span class='err'>Ошибка</span>");
				$this->printText("<p class='rez'>Получилось</p>");
				$this->printText("<pre>$textRez</pre>");
				$this->printText("<p class='mustBe'>Должно быть</p>");
				$this->printText("<pre>$textRight</pre>");
				throw new TestException("Тест на равенство не пройден", "Тест не пройден");
			}
			
		}
		
		/**
		 * Конструктор.
		 *  
		 * @param bool $print Выводить ли информацию на экран или писать в логи.
		 * @param SQLDB $DBCon Объект для работы с БД.
		 */
	 	public function __construct($print=false, $DBCon=null){
			$this->db=$DBCon ? $DBCon : SQLDBFactory::getDB();
			$this->print=$print;
	 	}
		
	 	/**
	 	 * Создает таблицы для теста.
	 	 */
	 	protected function createTables(){
	 		$subd=Tester::determineDB($this->db);
	 		$method="createTables$subd";
 			$this->$method();
	 	}
	 	
	 	/**
	 	 * Удаляет тестовые таблицы.
	 	 */
	 	protected function deleteTables(){
	 		$subd=Tester::determineDB($this->db);
	 		$method="deleteTables$subd";
 			$this->$method();
	 	}
	 	
	 }
