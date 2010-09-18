<?php
	/**
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @version 1.0
	 * @package tests
	 * @subpackage trees
	 */
	
	/**
	 * Класс для тестирования деревьев.
	 *  
	 * @package tests
	 * @subpackage trees
	 * @abstract
	 */
	 abstract class DBTree_Tester extends Tester{
	 	
	 	/**
	 	 * Строка заголовка.
	 	 * @var string.
	 	 */
	 	protected $header;
	 	
	 	/**
	 	 * Объект с деревом.
	 	 * @var DBTree.
	 	 */
	 	protected $tree;
	 	
	 	/**
	 	 * Массив с правильными деревьями.
	 	 * @var array.
	 	 */
	 	protected $rightTrees;
	 	
	 	
	 	/**
	 	 * Создает тестовые таблицы в MS SQL Server.
	 	 */
	 	abstract protected function createTablesmssql();
	 	
	 	/**
	 	 * Создает тестовые таблицы в MySQL.
	 	 */
	 	abstract protected function createTablesmysql();
	 	
	 	protected function setRightTrees(){
	 		$this->rightTrees["empty"]=array(array('id' =>1,'name' =>'0','tree' => array()));
	 		
	 		$this->rightTrees["add"]=array(
  				array('id' =>1,'name' =>'0','tree' => array(
    				array('id' =>2,'name' =>'1','tree' => array(
	      				array('id' =>3,'name' =>'1.1','tree' => array()),
    	  				array('id' =>4,'name' =>'1.2','tree' => array(
        					array('id' =>6,'name' =>'1.2.1','tree' => array()),
        					array('id' =>6,'name' =>'1.2.2','tree' => array(
        						array('id' =>6,'name' =>'1.2.2.1','tree' => array())
        					)),
        					array('id' =>6,'name' =>'1.2.3','tree' => array())
        				)),
        				array('id' =>5,'name' =>'1.3','tree' => array(
        					array('id' =>6,'name' =>'1.3.1','tree' => array()),
        					array('id' =>6,'name' =>'1.3.2','tree' => array()),
        					array('id' =>6,'name' =>'1.3.3','tree' => array())
      					))
      				)),
    				array('id' =>7,'name' =>'2','tree' => array()),
    				array('id' =>8,'name' =>'3','tree' => array(
      					array('id' =>9,'name' =>'3.1','tree' => array()),
      					array('id' =>10,'name' =>'3.2','tree' => array())
      				)),
    				array('id' =>11,'name' =>'4','tree' => array(
      					array('id' =>12,'name' =>'4.1','tree' => array())
      				)),
	  				array('id' =>13,'name' =>'5','tree' => array()),
  					array('id' =>14,'name' =>'6','tree' => array(
    					array('id' =>15,'name' =>'6.1','tree' => array())
    				))
    			))
    		);
  			
	 		$this->rightTrees["move"][0]=array(
  				array('id' =>1,'name' =>'0','tree' => array(
    				array('id' =>2,'name' =>'1','tree' => array(
	      				array('id' =>3,'name' =>'1.1','tree' => array()),
        				array('id' =>5,'name' =>'1.3','tree' => array(
        					array('id' =>6,'name' =>'1.3.1','tree' => array()),
        					array('id' =>6,'name' =>'1.3.2','tree' => array()),
        					array('id' =>6,'name' =>'1.3.3','tree' => array())
      					))
      				)),
      				array('id' =>7,'name' =>'2','tree' => array()),
    				array('id' =>8,'name' =>'3','tree' => array(
      					array('id' =>9,'name' =>'3.1','tree' => array()),
      					array('id' =>10,'name' =>'3.2','tree' => array())
      				)),
    				array('id' =>11,'name' =>'4','tree' => array(
      					array('id' =>12,'name' =>'4.1','tree' => array())
      				)),
	  				array('id' =>13,'name' =>'5','tree' => array()),
  					array('id' =>14,'name' =>'6','tree' => array(
    					array('id' =>15,'name' =>'6.1','tree' => array(
    	  					array('id' =>4,'name' =>'1.2','tree' => array(
        						array('id' =>6,'name' =>'1.2.1','tree' => array()),
        						array('id' =>6,'name' =>'1.2.2','tree' => array(
        							array('id' =>6,'name' =>'1.2.2.1','tree' => array())
        						)),
        						array('id' =>6,'name' =>'1.2.3','tree' => array())
        					))
        				))
    				))
    			))
    		);
    		
	 		$this->rightTrees["move"][1]=array(
  				array('id' =>1,'name' =>'0','tree' => array(
    				array('id' =>2,'name' =>'1','tree' => array(
	      				array('id' =>3,'name' =>'1.1','tree' => array())
      				)),
      				array('id' =>7,'name' =>'2','tree' => array()),
    				array('id' =>8,'name' =>'3','tree' => array(
      					array('id' =>9,'name' =>'3.1','tree' => array()),
      					array('id' =>10,'name' =>'3.2','tree' => array())
      				)),
    				array('id' =>11,'name' =>'4','tree' => array(
      					array('id' =>12,'name' =>'4.1','tree' => array())
      				)),
	  				array('id' =>13,'name' =>'5','tree' => array()),
  					array('id' =>14,'name' =>'6','tree' => array(
    					array('id' =>15,'name' =>'6.1','tree' => array(
    	  					array('id' =>4,'name' =>'1.2','tree' => array(
        						array('id' =>6,'name' =>'1.2.1','tree' => array()),
        						array('id' =>6,'name' =>'1.2.2','tree' => array(
        							array('id' =>6,'name' =>'1.2.2.1','tree' => array(
        								array('id' =>5,'name' =>'1.3','tree' => array(
        									array('id' =>6,'name' =>'1.3.1','tree' => array()),
        									array('id' =>6,'name' =>'1.3.2','tree' => array()),
        									array('id' =>6,'name' =>'1.3.3','tree' => array())
      									))
      								))
      							)),
        						array('id' =>6,'name' =>'1.2.3','tree' => array())
        					))
        				))
    				))
    			))
    		);
    		
	 		$this->rightTrees["delete"][0]=array(
  				array('id' =>1,'name' =>'0','tree' => array(
    				array('id' =>2,'name' =>'1','tree' => array(
	      				array('id' =>3,'name' =>'1.1','tree' => array())
      				)),
      				array('id' =>7,'name' =>'2','tree' => array()),
    				array('id' =>8,'name' =>'3','tree' => array(
      					array('id' =>9,'name' =>'3.1','tree' => array()),
      					array('id' =>10,'name' =>'3.2','tree' => array())
      				)),
    				array('id' =>11,'name' =>'4','tree' => array(
      					array('id' =>12,'name' =>'4.1','tree' => array())
      				)),
	  				array('id' =>13,'name' =>'5','tree' => array())
    			))
    		);
    		
	 		$this->rightTrees["delete"][1]=array(
  				array('id' =>1,'name' =>'0','tree' => array(
    				array('id' =>2,'name' =>'1','tree' => array(
	      				array('id' =>3,'name' =>'1.1','tree' => array()),
    	  				array('id' =>4,'name' =>'1.2','tree' => array(
        					array('id' =>6,'name' =>'1.2.1','tree' => array()),
        					array('id' =>6,'name' =>'1.2.2','tree' => array(
        						array('id' =>6,'name' =>'1.2.2.1','tree' => array())
        					)),
        					array('id' =>6,'name' =>'1.2.3','tree' => array())
        				)),
        				array('id' =>5,'name' =>'1.3','tree' => array(
        					array('id' =>6,'name' =>'1.3.1','tree' => array()),
        					array('id' =>6,'name' =>'1.3.2','tree' => array()),
        					array('id' =>6,'name' =>'1.3.3','tree' => array())
      					))
      				)),
  					array('id' =>14,'name' =>'6','tree' => array(
    					array('id' =>15,'name' =>'6.1','tree' => array())
    				))
    			))
    		);
    		
    		
	 	}
	 	
	 	/**
	 	 * Пересоздает таблицы и добавляет данные. 
	 	 */
	 	protected function reset(){
	 		$this->createTables();
	 		$this->addData();
	 	}

	 	
	 	/**
	 	 * Добавляет данных в дерево. 
	 	 */
	 	protected function addData(){
	 		
	 	}

	 	/**
	 	 * Тестирует выбор пустого дерева и поддерева.
	 	 */
	 	protected function testGetEmptyData(){
 			$this->printHeader("Тест пустого выбора", false);
 			$arr=$this->tree->getTree();
 			$this->check($arr, $this->rightTrees["empty"], "Выбор всего дерева");
 			$arr=$this->tree->getTree(1);
 			$this->check($arr, $this->rightTrees["empty"], "Выбор всего дерева с передачей параметра");	 		
 			$arr=$this->tree->getTree(2);
 			$this->check($arr, array(), "Выбор пустого поддерева");
	 	}
	 	
	 	/**
	 	 * Тестирует добавление данных в дерево и выбор данных. 
	 	 */
	 	protected function testAddData(){
	 		
	 	}
	 	
	 	/**
	 	 * Тестирует определение родителя.
	 	 */
	 	protected function testGetParent(){
	 		
	 	}
	 	
	 	/**
	 	 * Тестирует смену родителей.
	 	 */
	 	protected function testMoveData(){
	 		
	 	}
	 	
	 	/**
	 	 * Тестирует удаление данных.
	 	 */
	 	protected function testDeleteData(){
	 		
	 	}
	 	
	 	
		public function goTest(){
	 		if ($this->print){
	 			$this->printHeader($this->header);
	 		}
				$this->createTables();
	 			$this->setRightTree();
			try{
	 			$this->testGetEmptyData();
				$this->testAddData();
	 			$this->testGetParent();
	 			$this->testMoveData();
	 			$this->testDeleteData();
	 			$this->printEnd($this->header);
			}catch(TestException $e){
	 			$this->printEnd($this->header);
	 		}
	 	}
	 	
	 	
	 	
	 }
