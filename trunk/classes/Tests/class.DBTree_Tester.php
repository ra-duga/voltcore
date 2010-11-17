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
	 * Класс для тестирования деревьев.
	 *  
	 * @category VoltCore
	 * @package VoltCoreClasses
	 * @subpackage TestTrees
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
	 	 * Объект с сортированным деревом.
	 	 * @var DBTree.
	 	 */
	 	protected $sortTree;
	 	
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
	 	
	 	/**
	 	 * Вводит данные для проверки addData.
	 	 */
	 	abstract protected function insertTestData();

	 	/**
	 	 * Удаляет таблицы из MS SQL Server.
	 	 */
	 	abstract protected function deleteTablesmssql();

	 	/**
	 	 * Удаляет таблицы из MySQL.
	 	 */
	 	abstract protected function deleteTablesmysql();
	 	
	 	public function __construct($print=false, $DBCon=null){
	 		parent::__construct($print, $DBCon);
	 		$this->createTables();
	 		
	 	}
	 	
	 	/**
	 	 * Устанавливает эталоны деревьев.
	 	 */
	 	protected function setRightTree(){
	 		$this->rightTrees["empty"]=array(array('id' =>1,'name' =>'0','tree' => array()));

	 		$this->rightTrees["add"][0]=array(
  				array('id' =>1,'name' =>'0','tree' => array(
    				array('id' =>2,'name' =>'1','tree' => array(
      				))
    			))
    		);
	 		
    		$this->rightTrees["add"][1]=array(
  				array('id' =>1,'name' =>'0','tree' => array(
    				array('id' =>2,'name' =>'1','tree' => array(
	      				array('id' =>3,'name' =>'1.1','tree' => array())
      				))
    			))
    		);
	 		
	 		$this->rightTrees["add"][2]=array(
  				array('id' =>1,'name' =>'0','tree' => array(
    				array('id' =>2,'name' =>'1','tree' => array(
	      				array('id' =>3,'name' =>'1.1','tree' => array()),
    	  				array('id' =>4,'name' =>'1.2','tree' => array(
        				))
      				))
    			))
    		);
	 		
    		$this->rightTrees["add"][3]=array(
  				array('id' =>1,'name' =>'0','tree' => array(
    				array('id' =>2,'name' =>'1','tree' => array(
	      				array('id' =>3,'name' =>'1.1','tree' => array()),
    	  				array('id' =>4,'name' =>'1.2','tree' => array(
        					array('id' =>5,'name' =>'1.2.1','tree' => array())
        				))
      				))
    			))
    		);
	 		
    		$this->rightTrees["add"][4]=array(
  				array('id' =>1,'name' =>'0','tree' => array(
    				array('id' =>2,'name' =>'1','tree' => array(
	      				array('id' =>3,'name' =>'1.1','tree' => array()),
    	  				array('id' =>4,'name' =>'1.2','tree' => array(
        					array('id' =>5,'name' =>'1.2.1','tree' => array()),
        					array('id' =>6,'name' =>'1.2.2','tree' => array(
        					))
        				))
      				))
    			))
    		);
	 		
	 		$this->rightTrees["add"][5]=array(
  				array('id' =>1,'name' =>'0','tree' => array(
    				array('id' =>2,'name' =>'1','tree' => array(
	      				array('id' =>3,'name' =>'1.1','tree' => array()),
    	  				array('id' =>4,'name' =>'1.2','tree' => array(
        					array('id' =>5,'name' =>'1.2.1','tree' => array()),
        					array('id' =>6,'name' =>'1.2.2','tree' => array(
        						array('id' =>7,'name' =>'1.2.2.1','tree' => array())
        					)),
        					array('id' =>8,'name' =>'1.2.3','tree' => array())
        				)),
        				array('id' =>9,'name' =>'1.3','tree' => array(
        					array('id' =>10,'name' =>'1.3.1','tree' => array()),
        					array('id' =>11,'name' =>'1.3.2','tree' => array()),
        					array('id' =>12,'name' =>'1.3.3','tree' => array())
      					))
      				)),
    				array('id' =>13,'name' =>'2','tree' => array()),
    				array('id' =>14,'name' =>'3','tree' => array(
      					array('id' =>15,'name' =>'3.1','tree' => array()),
      					array('id' =>16,'name' =>'3.2','tree' => array())
      				)),
    				array('id' =>17,'name' =>'4','tree' => array(
      					array('id' =>18,'name' =>'4.1','tree' => array())
      				)),
	  				array('id' =>19,'name' =>'5','tree' => array()),
  					array('id' =>20,'name' =>'6','tree' => array(
    					array('id' =>21,'name' =>'6.1','tree' => array())
    				))
    			))
    		);
  			
	 		$this->rightTrees["move"][0]=array(
  				array('id' =>1,'name' =>'0','tree' => array(
    				array('id' =>2,'name' =>'1','tree' => array(
	      				array('id' =>3,'name' =>'1.1','tree' => array()),
        				array('id' =>9,'name' =>'1.3','tree' => array(
        					array('id' =>10,'name' =>'1.3.1','tree' => array()),
        					array('id' =>11,'name' =>'1.3.2','tree' => array()),
        					array('id' =>12,'name' =>'1.3.3','tree' => array())
      					))
      				)),
      				array('id' =>13,'name' =>'2','tree' => array()),
    				array('id' =>14,'name' =>'3','tree' => array(
      					array('id' =>15,'name' =>'3.1','tree' => array()),
      					array('id' =>16,'name' =>'3.2','tree' => array())
      				)),
    				array('id' =>17,'name' =>'4','tree' => array(
      					array('id' =>18,'name' =>'4.1','tree' => array())
      				)),
	  				array('id' =>19,'name' =>'5','tree' => array()),
  					array('id' =>20,'name' =>'6','tree' => array(
    					array('id' =>21,'name' =>'6.1','tree' => array(
    	  					array('id' =>4,'name' =>'1.2','tree' => array(
        						array('id' =>5,'name' =>'1.2.1','tree' => array()),
        						array('id' =>6,'name' =>'1.2.2','tree' => array(
        							array('id' =>7,'name' =>'1.2.2.1','tree' => array())
        						)),
        						array('id' =>8,'name' =>'1.2.3','tree' => array())
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
      				array('id' =>13,'name' =>'2','tree' => array()),
    				array('id' =>14,'name' =>'3','tree' => array(
      					array('id' =>15,'name' =>'3.1','tree' => array()),
      					array('id' =>16,'name' =>'3.2','tree' => array())
      				)),
    				array('id' =>17,'name' =>'4','tree' => array(
      					array('id' =>18,'name' =>'4.1','tree' => array())
      				)),
	  				array('id' =>19,'name' =>'5','tree' => array()),
  					array('id' =>20,'name' =>'6','tree' => array(
    					array('id' =>21,'name' =>'6.1','tree' => array(
    	  					array('id' =>4,'name' =>'1.2','tree' => array(
        						array('id' =>5,'name' =>'1.2.1','tree' => array()),
        						array('id' =>6,'name' =>'1.2.2','tree' => array(
        							array('id' =>7,'name' =>'1.2.2.1','tree' => array(
        								array('id' =>9,'name' =>'1.3','tree' => array(
        									array('id' =>10,'name' =>'1.3.1','tree' => array()),
        									array('id' =>11,'name' =>'1.3.2','tree' => array()),
        									array('id' =>12,'name' =>'1.3.3','tree' => array())
        									
      									))
      								))
      							)),
        						array('id' =>8,'name' =>'1.2.3','tree' => array())
        					))
        				))
    				))
    			))
    		);

    		$this->rightTrees["move"][2]=array(
  				array('id' =>1,'name' =>'0','tree' => array(
    				array('id' =>2,'name' =>'1','tree' => array(
	      				array('id' =>3,'name' =>'1.1','tree' => array())
      				)),
      				array('id' =>13,'name' =>'2','tree' => array()),
    				array('id' =>14,'name' =>'3','tree' => array(
      					array('id' =>15,'name' =>'3.1','tree' => array()),
      					array('id' =>16,'name' =>'3.2','tree' => array())
      				)),
    				array('id' =>17,'name' =>'4','tree' => array(
      					array('id' =>18,'name' =>'4.1','tree' => array(
  							array('id' =>20,'name' =>'6','tree' => array(
    							array('id' =>21,'name' =>'6.1','tree' => array(
    	  							array('id' =>4,'name' =>'1.2','tree' => array(
        								array('id' =>5,'name' =>'1.2.1','tree' => array()),
        								array('id' =>6,'name' =>'1.2.2','tree' => array(
        									array('id' =>7,'name' =>'1.2.2.1','tree' => array(
        										array('id' =>9,'name' =>'1.3','tree' => array(
        											array('id' =>10,'name' =>'1.3.1','tree' => array()),
        											array('id' =>11,'name' =>'1.3.2','tree' => array()),
        											array('id' =>12,'name' =>'1.3.3','tree' => array())
		      									))
		      								))
      									)),
        								array('id' =>8,'name' =>'1.2.3','tree' => array())
        							))
        						))
        					))
      					))
      				)),
	  				array('id' =>19,'name' =>'5','tree' => array())
    			))
    		);

    		$this->rightTrees["move"][3]=array(
  				array('id' =>1,'name' =>'0','tree' => array(
    				array('id' =>2,'name' =>'1','tree' => array(
	      				array('id' =>3,'name' =>'1.1','tree' => array())
      				)),
      				array('id' =>13,'name' =>'2','tree' => array()),
    				array('id' =>17,'name' =>'4','tree' => array(
      					array('id' =>18,'name' =>'4.1','tree' => array(
  							array('id' =>20,'name' =>'6','tree' => array(
    							array('id' =>14,'name' =>'3','tree' => array(
      								array('id' =>15,'name' =>'3.1','tree' => array()),
      								array('id' =>16,'name' =>'3.2','tree' => array())
      							)),
  								array('id' =>21,'name' =>'6.1','tree' => array(
    	  							array('id' =>4,'name' =>'1.2','tree' => array(
        								array('id' =>5,'name' =>'1.2.1','tree' => array()),
        								array('id' =>6,'name' =>'1.2.2','tree' => array(
        									array('id' =>7,'name' =>'1.2.2.1','tree' => array(
        										array('id' =>9,'name' =>'1.3','tree' => array(
        											array('id' =>10,'name' =>'1.3.1','tree' => array()),
        											array('id' =>11,'name' =>'1.3.2','tree' => array()),
        											array('id' =>12,'name' =>'1.3.3','tree' => array())
		      									))
		      								))
      									)),
        								array('id' =>8,'name' =>'1.2.3','tree' => array())
        							))
        						))
        					))
      					))
      				)),
	  				array('id' =>19,'name' =>'5','tree' => array())
    			))
    		);

    		$this->rightTrees["move"][4]=array(
  				array('id' =>1,'name' =>'0','tree' => array(
    				array('id' =>2,'name' =>'1','tree' => array(
	      				array('id' =>3,'name' =>'1.1','tree' => array())
      				)),
      				array('id' =>13,'name' =>'2','tree' => array()),
    				array('id' =>17,'name' =>'4','tree' => array(
      					array('id' =>18,'name' =>'4.1','tree' => array(
  							array('id' =>20,'name' =>'6','tree' => array(
    							array('id' =>14,'name' =>'3','tree' => array(
      								array('id' =>15,'name' =>'3.1','tree' => array()),
      								array('id' =>16,'name' =>'3.2','tree' => array())
      							)),
        					))
      					))
      				)),
	  				array('id' =>19,'name' =>'5','tree' => array(
  						array('id' =>21,'name' =>'6.1','tree' => array(
    	  					array('id' =>4,'name' =>'1.2','tree' => array(
        						array('id' =>5,'name' =>'1.2.1','tree' => array()),
        						array('id' =>6,'name' =>'1.2.2','tree' => array(
        							array('id' =>7,'name' =>'1.2.2.1','tree' => array(
        								array('id' =>9,'name' =>'1.3','tree' => array(
        									array('id' =>10,'name' =>'1.3.1','tree' => array()),
        									array('id' =>11,'name' =>'1.3.2','tree' => array()),
        									array('id' =>12,'name' =>'1.3.3','tree' => array())
		      							))
		      						))
      							)),
        						array('id' =>8,'name' =>'1.2.3','tree' => array())
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
      				array('id' =>13,'name' =>'2','tree' => array()),
    				array('id' =>17,'name' =>'4','tree' => array(
      					array('id' =>18,'name' =>'4.1','tree' => array(
  							array('id' =>20,'name' =>'6','tree' => array(
        					))
      					))
      				)),
	  				array('id' =>19,'name' =>'5','tree' => array(
  						array('id' =>21,'name' =>'6.1','tree' => array(
    	  					array('id' =>4,'name' =>'1.2','tree' => array(
        						array('id' =>5,'name' =>'1.2.1','tree' => array()),
        						array('id' =>6,'name' =>'1.2.2','tree' => array(
        							array('id' =>7,'name' =>'1.2.2.1','tree' => array(
        								array('id' =>9,'name' =>'1.3','tree' => array(
        									array('id' =>10,'name' =>'1.3.1','tree' => array()),
        									array('id' =>11,'name' =>'1.3.2','tree' => array()),
        									array('id' =>12,'name' =>'1.3.3','tree' => array())
		      							))
		      						))
      							)),
        						array('id' =>8,'name' =>'1.2.3','tree' => array())
        					))
        				))
	  				))
    			))
    		);

    		$this->rightTrees["delete"][1]=array(
  				array('id' =>1,'name' =>'0','tree' => array(
    				array('id' =>2,'name' =>'1','tree' => array(
	      				array('id' =>3,'name' =>'1.1','tree' => array())
      				)),
      				array('id' =>13,'name' =>'2','tree' => array()),
    				array('id' =>17,'name' =>'4','tree' => array(
      					array('id' =>18,'name' =>'4.1','tree' => array(
  							array('id' =>20,'name' =>'6','tree' => array(
        					))
      					))
      				)),
	  				array('id' =>19,'name' =>'5','tree' => array(
  						array('id' =>21,'name' =>'6.1','tree' => array(
    	  					array('id' =>4,'name' =>'1.2','tree' => array(
        						array('id' =>5,'name' =>'1.2.1','tree' => array()),
        						array('id' =>6,'name' =>'1.2.2','tree' => array(
        							array('id' =>7,'name' =>'1.2.2.1','tree' => array(
        								array('id' =>9,'name' =>'1.3','tree' => array(
        									array('id' =>10,'name' =>'1.3.1','tree' => array()),
        									array('id' =>11,'name' =>'1.3.2','tree' => array())
		      							))
		      						))
      							)),
        						array('id' =>8,'name' =>'1.2.3','tree' => array())
        					))
        				))
	  				))
    			))
    		);
    		
    		$this->rightTrees["delete"][2]=array(
  				array('id' =>1,'name' =>'0','tree' => array(
    				array('id' =>2,'name' =>'1','tree' => array(
	      				array('id' =>3,'name' =>'1.1','tree' => array())
      				)),
      				array('id' =>13,'name' =>'2','tree' => array()),
    				array('id' =>17,'name' =>'4','tree' => array(
      					array('id' =>18,'name' =>'4.1','tree' => array(
  							array('id' =>20,'name' =>'6','tree' => array(
        					))
      					))
      				)),
    			))
    		);

    		$this->rightTrees["delete"][3]=array(
  				array('id' =>1,'name' =>'0','tree' => array(
      				array('id' =>13,'name' =>'2','tree' => array()),
    				array('id' =>17,'name' =>'4','tree' => array(
      					array('id' =>18,'name' =>'4.1','tree' => array(
  							array('id' =>20,'name' =>'6','tree' => array(
        					))
      					))
      				)),
    			))
    		);
    		
    		
	 		$this->rightTrees["sortadd"][0]=array(
  				array('id' =>1,'name' =>'0','tree' => array(
    				array('id' =>2,'name' =>'1','tree' => array(
    	  				array('id' =>4,'name' =>'1.2','tree' => array()),
    	  				array('id' =>3,'name' =>'1.1','tree' => array())
      				))
    			))
    		);

	 		$this->rightTrees["sortadd"][1]=array(
  				array('id' =>1,'name' =>'0','tree' => array(
    				array('id' =>2,'name' =>'1','tree' => array(
    	  				array('id' =>4,'name' =>'1.2','tree' => array()),
    	  				array('id' =>5,'name' =>'1.3','tree' => array()),
    	  				array('id' =>3,'name' =>'1.1','tree' => array())
      				))
    			))
    		);
    		
	 		$this->rightTrees["sortadd"][2]=array(
  				array('id' =>1,'name' =>'0','tree' => array(
    				array('id' =>2,'name' =>'1','tree' => array(
	      				array('id' =>4,'name' =>'1.2','tree' => array(
        					array('id' =>6,'name' =>'1.2.2','tree' => array(
        						array('id' =>7,'name' =>'1.2.2.1','tree' => array())
        					)),
        					array('id' =>8,'name' =>'1.2.3','tree' => array()),
        					array('id' =>9,'name' =>'1.2.4','tree' => array())
        				)),
	       				array('id' =>5,'name' =>'1.3','tree' => array(
        					array('id' =>10,'name' =>'1.3.1','tree' => array()),
        					array('id' =>11,'name' =>'1.3.2','tree' => array()),
        					array('id' =>12,'name' =>'1.3.3','tree' => array())
      					)),
    					array('id' =>3,'name' =>'1.1','tree' => array())
      				)),
    				array('id' =>17,'name' =>'4','tree' => array(
      					array('id' =>18,'name' =>'4.1','tree' => array())
      				)),
      				array('id' =>13,'name' =>'2','tree' => array()),
    				array('id' =>14,'name' =>'3','tree' => array(
      					array('id' =>15,'name' =>'3.1','tree' => array()),
      					array('id' =>16,'name' =>'3.2','tree' => array())
      				)),
	  				array('id' =>19,'name' =>'5','tree' => array()),
  					array('id' =>20,'name' =>'6','tree' => array(
    					array('id' =>21,'name' =>'6.1','tree' => array())
    				))
    			))
    		);

    		$this->rightTrees["sortadd"][3]=array(
  				array('id' =>1,'name' =>'0', 'sorder'=>0,'tree' => array(
    				array('id' =>2,'name' =>'1', 'sorder'=>1,'tree' => array(
	      				array('id' =>4,'name' =>'1.2', 'sorder'=>1,'tree' => array(
        					array('id' =>6,'name' =>'1.2.2', 'sorder'=>1,'tree' => array(
        						array('id' =>7,'name' =>'1.2.2.1', 'sorder'=>1,'tree' => array())
        					)),
        					array('id' =>8,'name' =>'1.2.3', 'sorder'=>2,'tree' => array()),
        					array('id' =>9,'name' =>'1.2.4', 'sorder'=>3,'tree' => array())
        				)),
	       				array('id' =>5,'name' =>'1.3', 'sorder'=>2,'tree' => array(
        					array('id' =>10,'name' =>'1.3.1', 'sorder'=>1,'tree' => array()),
        					array('id' =>11,'name' =>'1.3.2', 'sorder'=>2,'tree' => array()),
        					array('id' =>12,'name' =>'1.3.3', 'sorder'=>3,'tree' => array())
      					)),
    					array('id' =>3,'name' =>'1.1', 'sorder'=>3,'tree' => array())
      				)),
    				array('id' =>17,'name' =>'4', 'sorder'=>2,'tree' => array(
      					array('id' =>18,'name' =>'4.1', 'sorder'=>1,'tree' => array())
      				)),
      				array('id' =>13,'name' =>'2', 'sorder'=>3,'tree' => array()),
    				array('id' =>14,'name' =>'3', 'sorder'=>4,'tree' => array(
      					array('id' =>15,'name' =>'3.1', 'sorder'=>1,'tree' => array()),
      					array('id' =>16,'name' =>'3.2', 'sorder'=>2,'tree' => array())
      				)),
	  				array('id' =>19,'name' =>'5', 'sorder'=>5,'tree' => array()),
  					array('id' =>20,'name' =>'6', 'sorder'=>6,'tree' => array(
    					array('id' =>21,'name' =>'6.1', 'sorder'=>1,'tree' => array())
    				))
    			))
    		);
    		
    		
    		$this->rightTrees["reorder"][0]=array(
  				array('id' =>1,'name' =>'0','tree' => array(
    				array('id' =>2,'name' =>'1','tree' => array(
    					array('id' =>3,'name' =>'1.1','tree' => array()),
    					array('id' =>4,'name' =>'1.2','tree' => array(
        					array('id' =>6,'name' =>'1.2.2','tree' => array(
        						array('id' =>7,'name' =>'1.2.2.1','tree' => array())
        					)),
        					array('id' =>8,'name' =>'1.2.3','tree' => array()),
        					array('id' =>9,'name' =>'1.2.4','tree' => array())
        				)),
	       				array('id' =>5,'name' =>'1.3','tree' => array(
        					array('id' =>10,'name' =>'1.3.1','tree' => array()),
        					array('id' =>11,'name' =>'1.3.2','tree' => array()),
        					array('id' =>12,'name' =>'1.3.3','tree' => array())
      					))
      				)),
    				array('id' =>17,'name' =>'4','tree' => array(
      					array('id' =>18,'name' =>'4.1','tree' => array())
      				)),
      				array('id' =>13,'name' =>'2','tree' => array()),
    				array('id' =>14,'name' =>'3','tree' => array(
      					array('id' =>15,'name' =>'3.1','tree' => array()),
      					array('id' =>16,'name' =>'3.2','tree' => array())
      				)),
	  				array('id' =>19,'name' =>'5','tree' => array()),
  					array('id' =>20,'name' =>'6','tree' => array(
    					array('id' =>21,'name' =>'6.1','tree' => array())
    				))
    			))
    		);
    		
    		$this->rightTrees["reorder"][1]=array(
  				array('id' =>1,'name' =>'0','tree' => array(
    				array('id' =>17,'name' =>'4','tree' => array(
      					array('id' =>18,'name' =>'4.1','tree' => array())
      				)),
      				array('id' =>13,'name' =>'2','tree' => array()),
    				array('id' =>14,'name' =>'3','tree' => array(
      					array('id' =>15,'name' =>'3.1','tree' => array()),
      					array('id' =>16,'name' =>'3.2','tree' => array())
      				)),
	  				array('id' =>19,'name' =>'5','tree' => array()),
  					array('id' =>20,'name' =>'6','tree' => array(
    					array('id' =>21,'name' =>'6.1','tree' => array())
    				)),
    				array('id' =>2,'name' =>'1','tree' => array(
    					array('id' =>3,'name' =>'1.1','tree' => array()),
    					array('id' =>4,'name' =>'1.2','tree' => array(
        					array('id' =>6,'name' =>'1.2.2','tree' => array(
        						array('id' =>7,'name' =>'1.2.2.1','tree' => array())
        					)),
        					array('id' =>8,'name' =>'1.2.3','tree' => array()),
        					array('id' =>9,'name' =>'1.2.4','tree' => array())
        				)),
	       				array('id' =>5,'name' =>'1.3','tree' => array(
        					array('id' =>10,'name' =>'1.3.1','tree' => array()),
        					array('id' =>11,'name' =>'1.3.2','tree' => array()),
        					array('id' =>12,'name' =>'1.3.3','tree' => array())
      					))
      				))
    			))
    		);

    		$this->rightTrees["reorder"][2]=array(
  				array('id' =>1,'name' =>'0','tree' => array(
    				array('id' =>17,'name' =>'4','tree' => array(
      					array('id' =>18,'name' =>'4.1','tree' => array())
      				)),
      				array('id' =>13,'name' =>'2','tree' => array()),
  					array('id' =>20,'name' =>'6','tree' => array(
    					array('id' =>21,'name' =>'6.1','tree' => array())
    				)),
      				array('id' =>14,'name' =>'3','tree' => array(
      					array('id' =>15,'name' =>'3.1','tree' => array()),
      					array('id' =>16,'name' =>'3.2','tree' => array())
      				)),
	  				array('id' =>19,'name' =>'5','tree' => array()),
    				array('id' =>2,'name' =>'1','tree' => array(
    					array('id' =>3,'name' =>'1.1','tree' => array()),
    					array('id' =>4,'name' =>'1.2','tree' => array(
        					array('id' =>6,'name' =>'1.2.2','tree' => array(
        						array('id' =>7,'name' =>'1.2.2.1','tree' => array())
        					)),
        					array('id' =>8,'name' =>'1.2.3','tree' => array()),
        					array('id' =>9,'name' =>'1.2.4','tree' => array())
        				)),
	       				array('id' =>5,'name' =>'1.3','tree' => array(
        					array('id' =>10,'name' =>'1.3.1','tree' => array()),
        					array('id' =>11,'name' =>'1.3.2','tree' => array()),
        					array('id' =>12,'name' =>'1.3.3','tree' => array())
      					))
      				))
    			))
    		);
    		
    		$this->rightTrees["reorder"][3]=array(
  				array('id' =>1,'name' =>'0','tree' => array(
    				array('id' =>17,'name' =>'4','tree' => array(
      					array('id' =>18,'name' =>'4.1','tree' => array())
      				)),
      				array('id' =>13,'name' =>'2','tree' => array()),
  					array('id' =>20,'name' =>'6','tree' => array(
    					array('id' =>21,'name' =>'6.1','tree' => array())
    				)),
      				array('id' =>14,'name' =>'3','tree' => array(
      					array('id' =>15,'name' =>'3.1','tree' => array()),
      					array('id' =>16,'name' =>'3.2','tree' => array())
      				)),
    				array('id' =>2,'name' =>'1','tree' => array(
    					array('id' =>3,'name' =>'1.1','tree' => array()),
    					array('id' =>4,'name' =>'1.2','tree' => array(
        					array('id' =>6,'name' =>'1.2.2','tree' => array(
        						array('id' =>7,'name' =>'1.2.2.1','tree' => array())
        					)),
        					array('id' =>8,'name' =>'1.2.3','tree' => array()),
        					array('id' =>9,'name' =>'1.2.4','tree' => array())
        				)),
	       				array('id' =>5,'name' =>'1.3','tree' => array(
        					array('id' =>10,'name' =>'1.3.1','tree' => array()),
        					array('id' =>11,'name' =>'1.3.2','tree' => array()),
        					array('id' =>12,'name' =>'1.3.3','tree' => array())
      					))
      				)),
	  				array('id' =>19,'name' =>'5','tree' => array())
      			))
    		);

    		$this->rightTrees["reorder"][4]=array(
  				array('id' =>1,'name' =>'0','tree' => array(
    				array('id' =>17,'name' =>'4','tree' => array(
      					array('id' =>18,'name' =>'4.1','tree' => array())
      				)),
  					array('id' =>20,'name' =>'6','tree' => array(
    					array('id' =>21,'name' =>'6.1','tree' => array())
    				)),
      				array('id' =>13,'name' =>'2','tree' => array()),
      				array('id' =>14,'name' =>'3','tree' => array(
      					array('id' =>15,'name' =>'3.1','tree' => array()),
      					array('id' =>16,'name' =>'3.2','tree' => array())
      				)),
    				array('id' =>2,'name' =>'1','tree' => array(
    					array('id' =>3,'name' =>'1.1','tree' => array()),
    					array('id' =>4,'name' =>'1.2','tree' => array(
        					array('id' =>6,'name' =>'1.2.2','tree' => array(
        						array('id' =>7,'name' =>'1.2.2.1','tree' => array())
        					)),
        					array('id' =>8,'name' =>'1.2.3','tree' => array()),
        					array('id' =>9,'name' =>'1.2.4','tree' => array())
        				)),
	       				array('id' =>5,'name' =>'1.3','tree' => array(
        					array('id' =>10,'name' =>'1.3.1','tree' => array()),
        					array('id' =>11,'name' =>'1.3.2','tree' => array()),
        					array('id' =>12,'name' =>'1.3.3','tree' => array())
      					))
      				)),
	  				array('id' =>19,'name' =>'5','tree' => array())
      			))
    		);
    		
    		$this->rightTrees["reorder"][5]=array(
  				array('id' =>1,'name' =>'0','tree' => array(
    				array('id' =>17,'name' =>'4','tree' => array(
      					array('id' =>18,'name' =>'4.1','tree' => array())
      				)),
  					array('id' =>20,'name' =>'6','tree' => array(
    					array('id' =>21,'name' =>'6.1','tree' => array())
    				)),
	  				array('id' =>19,'name' =>'5','tree' => array()),
    				array('id' =>13,'name' =>'2','tree' => array()),
      				array('id' =>14,'name' =>'3','tree' => array(
      					array('id' =>15,'name' =>'3.1','tree' => array()),
      					array('id' =>16,'name' =>'3.2','tree' => array())
      				)),
    				array('id' =>2,'name' =>'1','tree' => array(
    					array('id' =>3,'name' =>'1.1','tree' => array()),
    					array('id' =>4,'name' =>'1.2','tree' => array(
        					array('id' =>6,'name' =>'1.2.2','tree' => array(
        						array('id' =>7,'name' =>'1.2.2.1','tree' => array())
        					)),
        					array('id' =>8,'name' =>'1.2.3','tree' => array()),
        					array('id' =>9,'name' =>'1.2.4','tree' => array())
        				)),
	       				array('id' =>5,'name' =>'1.3','tree' => array(
        					array('id' =>10,'name' =>'1.3.1','tree' => array()),
        					array('id' =>11,'name' =>'1.3.2','tree' => array()),
        					array('id' =>12,'name' =>'1.3.3','tree' => array())
      					))
      				))
      			))
    		);
    		
    		$this->rightTrees["reorder"][6]=array(
  				array('id' =>1,'name' =>'0','tree' => array(
    				array('id' =>17,'name' =>'4','tree' => array(
      					array('id' =>18,'name' =>'4.1','tree' => array())
      				)),
  					array('id' =>20,'name' =>'6','tree' => array(
    					array('id' =>21,'name' =>'6.1','tree' => array())
    				)),
    				array('id' =>13,'name' =>'2','tree' => array()),
      				array('id' =>14,'name' =>'3','tree' => array(
      					array('id' =>15,'name' =>'3.1','tree' => array()),
      					array('id' =>16,'name' =>'3.2','tree' => array())
      				)),
	  				array('id' =>19,'name' =>'5','tree' => array()),
      				array('id' =>2,'name' =>'1','tree' => array(
    					array('id' =>3,'name' =>'1.1','tree' => array()),
    					array('id' =>4,'name' =>'1.2','tree' => array(
        					array('id' =>6,'name' =>'1.2.2','tree' => array(
        						array('id' =>7,'name' =>'1.2.2.1','tree' => array())
        					)),
        					array('id' =>8,'name' =>'1.2.3','tree' => array()),
        					array('id' =>9,'name' =>'1.2.4','tree' => array())
        				)),
	       				array('id' =>5,'name' =>'1.3','tree' => array(
        					array('id' =>10,'name' =>'1.3.1','tree' => array()),
        					array('id' =>11,'name' =>'1.3.2','tree' => array()),
        					array('id' =>12,'name' =>'1.3.3','tree' => array())
      					))
      				))
      			))
    		);
    		
    		$this->rightTrees["reorder"][7]=array(
  				array('id' =>1,'name' =>'0','tree' => array(
	  				array('id' =>19,'name' =>'5','tree' => array()),
  					array('id' =>17,'name' =>'4','tree' => array(
      					array('id' =>18,'name' =>'4.1','tree' => array())
      				)),
  					array('id' =>20,'name' =>'6','tree' => array(
    					array('id' =>21,'name' =>'6.1','tree' => array())
    				)),
    				array('id' =>13,'name' =>'2','tree' => array()),
      				array('id' =>14,'name' =>'3','tree' => array(
      					array('id' =>15,'name' =>'3.1','tree' => array()),
      					array('id' =>16,'name' =>'3.2','tree' => array())
      				)),
      				array('id' =>2,'name' =>'1','tree' => array(
    					array('id' =>3,'name' =>'1.1','tree' => array()),
    					array('id' =>4,'name' =>'1.2','tree' => array(
        					array('id' =>6,'name' =>'1.2.2','tree' => array(
        						array('id' =>7,'name' =>'1.2.2.1','tree' => array())
        					)),
        					array('id' =>8,'name' =>'1.2.3','tree' => array()),
        					array('id' =>9,'name' =>'1.2.4','tree' => array())
        				)),
	       				array('id' =>5,'name' =>'1.3','tree' => array(
        					array('id' =>10,'name' =>'1.3.1','tree' => array()),
        					array('id' =>11,'name' =>'1.3.2','tree' => array()),
        					array('id' =>12,'name' =>'1.3.3','tree' => array())
      					))
      				))
      			))
    		);

    		$this->rightTrees["reorder"][8]=array(
  				array('id' =>1,'name' =>'0', 'sorder'=>0,'tree' => array(
	  				array('id' =>19,'name' =>'5', 'sorder'=>1,'tree' => array()),
  					array('id' =>17,'name' =>'4', 'sorder'=>2,'tree' => array(
      					array('id' =>18,'name' =>'4.1', 'sorder'=>1,'tree' => array())
      				)),
  					array('id' =>20,'name' =>'6', 'sorder'=>3,'tree' => array(
    					array('id' =>21,'name' =>'6.1', 'sorder'=>1,'tree' => array())
    				)),
    				array('id' =>13,'name' =>'2', 'sorder'=>4,'tree' => array()),
      				array('id' =>14,'name' =>'3', 'sorder'=>5,'tree' => array(
      					array('id' =>15,'name' =>'3.1', 'sorder'=>1,'tree' => array()),
      					array('id' =>16,'name' =>'3.2', 'sorder'=>2,'tree' => array())
      				)),
      				array('id' =>2,'name' =>'1', 'sorder'=>6,'tree' => array(
    					array('id' =>3,'name' =>'1.1', 'sorder'=>1,'tree' => array()),
    					array('id' =>4,'name' =>'1.2', 'sorder'=>2,'tree' => array(
        					array('id' =>6,'name' =>'1.2.2', 'sorder'=>1,'tree' => array(
        						array('id' =>7,'name' =>'1.2.2.1', 'sorder'=>1,'tree' => array())
        					)),
        					array('id' =>8,'name' =>'1.2.3', 'sorder'=>2,'tree' => array()),
        					array('id' =>9,'name' =>'1.2.4', 'sorder'=>3,'tree' => array())
        				)),
	       				array('id' =>5,'name' =>'1.3', 'sorder'=>3,'tree' => array(
        					array('id' =>10,'name' =>'1.3.1', 'sorder'=>1,'tree' => array()),
        					array('id' =>11,'name' =>'1.3.2', 'sorder'=>2,'tree' => array()),
        					array('id' =>12,'name' =>'1.3.3', 'sorder'=>3,'tree' => array())
      					))
      				))
      			))
    		);
    		
    		$this->rightTrees["sortmove"][0]=array(
  				array('id' =>1,'name' =>'0','tree' => array(
	  				array('id' =>19,'name' =>'5','tree' => array()),
  					array('id' =>17,'name' =>'4','tree' => array(
      					array('id' =>18,'name' =>'4.1','tree' => array())
      				)),
  					array('id' =>20,'name' =>'6','tree' => array(
    					array('id' =>21,'name' =>'6.1','tree' => array(
    						array('id' =>4,'name' =>'1.2','tree' => array(
        						array('id' =>6,'name' =>'1.2.2','tree' => array(
        							array('id' =>7,'name' =>'1.2.2.1','tree' => array())
        						)),
        						array('id' =>8,'name' =>'1.2.3','tree' => array()),
        						array('id' =>9,'name' =>'1.2.4','tree' => array())
        					))
    					))
    				)),
    				array('id' =>13,'name' =>'2','tree' => array()),
      				array('id' =>14,'name' =>'3','tree' => array(
      					array('id' =>15,'name' =>'3.1','tree' => array()),
      					array('id' =>16,'name' =>'3.2','tree' => array())
      				)),
      				array('id' =>2,'name' =>'1','tree' => array(
    					array('id' =>3,'name' =>'1.1','tree' => array()),
	       				array('id' =>5,'name' =>'1.3','tree' => array(
        					array('id' =>10,'name' =>'1.3.1','tree' => array()),
        					array('id' =>11,'name' =>'1.3.2','tree' => array()),
        					array('id' =>12,'name' =>'1.3.3','tree' => array())
      					))
      				))
      			))
    		);

    		$this->rightTrees["sortmove"][1]=array(
  				array('id' =>1,'name' =>'0','tree' => array(
	  				array('id' =>19,'name' =>'5','tree' => array()),
  					array('id' =>17,'name' =>'4','tree' => array(
      					array('id' =>18,'name' =>'4.1','tree' => array())
      				)),
  					array('id' =>20,'name' =>'6','tree' => array(
    					array('id' =>21,'name' =>'6.1','tree' => array(
      						array('id' =>14,'name' =>'3','tree' => array(
      							array('id' =>15,'name' =>'3.1','tree' => array()),
      							array('id' =>16,'name' =>'3.2','tree' => array())
      						)),
    						array('id' =>4,'name' =>'1.2','tree' => array(
        						array('id' =>6,'name' =>'1.2.2','tree' => array(
        							array('id' =>7,'name' =>'1.2.2.1','tree' => array())
        						)),
        						array('id' =>8,'name' =>'1.2.3','tree' => array()),
        						array('id' =>9,'name' =>'1.2.4','tree' => array())
        					))
    					))
    				)),
    				array('id' =>13,'name' =>'2','tree' => array()),
      				array('id' =>2,'name' =>'1','tree' => array(
    					array('id' =>3,'name' =>'1.1','tree' => array()),
	       				array('id' =>5,'name' =>'1.3','tree' => array(
        					array('id' =>10,'name' =>'1.3.1','tree' => array()),
        					array('id' =>11,'name' =>'1.3.2','tree' => array()),
        					array('id' =>12,'name' =>'1.3.3','tree' => array())
      					))
      				))
      			))
    		);

			$this->rightTrees["sortdelete"][0]=array(
  				array('id' =>1,'name' =>'0','tree' => array(
	  				array('id' =>19,'name' =>'5','tree' => array()),
  					array('id' =>17,'name' =>'4','tree' => array(
      					array('id' =>18,'name' =>'4.1','tree' => array())
      				)),
  					array('id' =>20,'name' =>'6','tree' => array(
    					array('id' =>21,'name' =>'6.1','tree' => array(
      						array('id' =>14,'name' =>'3','tree' => array(
      							array('id' =>15,'name' =>'3.1','tree' => array()),
      							array('id' =>16,'name' =>'3.2','tree' => array())
      						)),
    						array('id' =>4,'name' =>'1.2','tree' => array(
        						array('id' =>6,'name' =>'1.2.2','tree' => array(
        							array('id' =>7,'name' =>'1.2.2.1','tree' => array())
        						)),
        						array('id' =>8,'name' =>'1.2.3','tree' => array()),
        						array('id' =>9,'name' =>'1.2.4','tree' => array())
        					))
    					))
    				)),
    				array('id' =>13,'name' =>'2','tree' => array())
      			))
    		);

    		
    		$this->rightTrees["sortdelete"][1]=array(
  				array('id' =>1,'name' =>'0','tree' => array(
  					array('id' =>17,'name' =>'4','tree' => array(
      					array('id' =>18,'name' =>'4.1','tree' => array())
      				)),
  					array('id' =>20,'name' =>'6','tree' => array(
    					array('id' =>21,'name' =>'6.1','tree' => array(
      						array('id' =>14,'name' =>'3','tree' => array(
      							array('id' =>15,'name' =>'3.1','tree' => array()),
      							array('id' =>16,'name' =>'3.2','tree' => array())
      						)),
    						array('id' =>4,'name' =>'1.2','tree' => array(
        						array('id' =>6,'name' =>'1.2.2','tree' => array(
        							array('id' =>7,'name' =>'1.2.2.1','tree' => array())
        						)),
        						array('id' =>8,'name' =>'1.2.3','tree' => array()),
        						array('id' =>9,'name' =>'1.2.4','tree' => array())
        					))
    					))
    				)),
    				array('id' =>13,'name' =>'2','tree' => array())
      			))
    		);

    		$this->rightTrees["sortdelete"][2]=array(
  				array('id' =>1,'name' =>'0','tree' => array(
  					array('id' =>17,'name' =>'4','tree' => array(
      					array('id' =>18,'name' =>'4.1','tree' => array())
      				)),
    				array('id' =>13,'name' =>'2','tree' => array())
      			))
    		);
    		
    		$this->rightTrees["sortdelete"][3]=array(
  				array('id' =>1,'name' =>'0', 'sorder'=>0,'tree' => array(
  					array('id' =>17,'name' =>'4', 'sorder'=>1,'tree' => array(
      					array('id' =>18,'name' =>'4.1', 'sorder'=>1,'tree' => array())
      				)),
    				array('id' =>13,'name' =>'2', 'sorder'=>2,'tree' => array())
      			))
    		);
	 	}
	 	
		protected function check($rez, $right, $title, $makeArr=false, $sort=false){
 			$arr=$rez;
			if ($makeArr){
				if ($sort){
					$arr=$this->sortTree->getTree();
				}else{
					$arr=$this->tree->getTree();
 					$arr=DBTree::sortTree($arr);
				}
 			}
	 		parent::check($arr, $right, $title);
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
	 		$this->tree->add('1','0', DBTree::BOTH_NAME);
	 		$this->tree->add('1.1','1', DBTree::BOTH_NAME);
	 		$this->tree->add('1.2','1', DBTree::BOTH_NAME);
	 		$this->tree->add('1.2.1','1.2', DBTree::BOTH_NAME);
	 		$this->tree->add('1.2.2','1.2', DBTree::BOTH_NAME);
	 		$this->tree->add('1.2.2.1','1.2.2', DBTree::BOTH_NAME);
	 		$this->tree->add('1.2.3','1.2', DBTree::BOTH_NAME);
	 		$this->tree->add('1.3','1', DBTree::BOTH_NAME);
	 		$this->tree->add('1.3.1','1.3', DBTree::BOTH_NAME);
	 		$this->tree->add('1.3.2','1.3', DBTree::BOTH_NAME);
	 		$this->tree->add('1.3.3','1.3', DBTree::BOTH_NAME);
	 		$this->tree->add('2','0', DBTree::BOTH_NAME);
	 		$this->tree->add('3','0', DBTree::BOTH_NAME);
	 		$this->tree->add('3.1','3', DBTree::BOTH_NAME);
	 		$this->tree->add('3.2','3', DBTree::BOTH_NAME);
	 		$this->tree->add('4','0', DBTree::BOTH_NAME);
	 		$this->tree->add('4.1','4', DBTree::BOTH_NAME);
	 		$this->tree->add('5','0', DBTree::BOTH_NAME);
	 		$this->tree->add('6','0', DBTree::BOTH_NAME);
	 		$this->tree->add('6.1','6', DBTree::BOTH_NAME);
	 	}

	 	/**
	 	 * Тестирует выбор пустого дерева и поддерева.
	 	 */
	 	protected function testGetEmptyData(){
 			$this->printHeader("Тест пустого выбора", false);
 			$this->check(null, $this->rightTrees["empty"], "Выбор всего дерева", true);
 			$arr=$this->tree->getTree(null, 1);
 			$this->check($arr, $this->rightTrees["empty"], "Выбор всего дерева с передачей параметра");	 		
 			$arr=$this->tree->getTree(null,2);
 			$this->check($arr, array(), "Выбор пустого поддерева");
	 	}
	 	
	 	/**
	 	 * Тестирует добавление данных в дерево и выбор данных. 
	 	 */
	 	protected function testAddSortData(){
 			$this->printHeader("Тесты добавления и выбора с сортировкой", false);
			
	 		$this->sortTree->add('1','0',DBTree::BOTH_NAME);
	 		$this->sortTree->add('1.1','1',DBTree::BOTH_NAME);
	 		$this->sortTree->add('1.2','1',DBTree::BOTH_NAME,1);
	 		$this->check(null, $this->rightTrees["sortadd"][0], "Добавление в начало", true, true);

	 		$this->sortTree->add('1.3','1',DBTree::BOTH_NAME,2);
	 		$this->check(null, $this->rightTrees["sortadd"][1], "Добавление в центр", true,true);

	 		$this->sortTree->add('1.2.2','1.2', DBTree::BOTH_NAME);
	 		$this->sortTree->add('1.2.2.1','1.2.2', DBTree::BOTH_NAME);
	 		$this->sortTree->add('1.2.3','1.2', DBTree::BOTH_NAME);
	 		$this->sortTree->add('1.2.4','1.2', DBTree::BOTH_NAME);
	 		$this->sortTree->add('1.3.1','1.3', DBTree::BOTH_NAME);
	 		$this->sortTree->add('1.3.2','1.3', DBTree::BOTH_NAME);
	 		$this->sortTree->add('1.3.3','1.3', DBTree::BOTH_NAME);
	 		$this->sortTree->add('2','0', DBTree::BOTH_NAME);
	 		$this->sortTree->add('3','0', DBTree::BOTH_NAME);
	 		$this->sortTree->add('3.1','3', DBTree::BOTH_NAME);
	 		$this->sortTree->add('3.2','3', DBTree::BOTH_NAME);
	 		$this->sortTree->add('4','0', DBTree::BOTH_NAME,2);
	 		$this->sortTree->add('4.1','4', DBTree::BOTH_NAME);
	 		$this->sortTree->add('5','0', DBTree::BOTH_NAME);
	 		$this->sortTree->add('6','0', DBTree::BOTH_NAME);
	 		$this->sortTree->add('6.1','6', DBTree::BOTH_NAME);
	 		$this->check(null, $this->rightTrees["sortadd"][2], "Проверка всего дерева", true, true);
	 		
	 		$arr=$this->sortTree->getTree(array('sorder'=>"sorder"));
	 		$this->check($arr, $this->rightTrees["sortadd"][3], "Проверка выбора с доп. параметрами");
	 	}
	 	
	 	/**
	 	 * Тестирует изменение сортировки. 
	 	 */
	 	protected function testReOrderData(){
 			$this->printHeader("Тесты изменения порядка", false);
	 		$this->sortTree->setOrderNum('1.1',1, DBTree::BOTH_NAME);
	 		$this->check(null, $this->rightTrees["reorder"][0], "Перестановка в начало", true,true);
	 		$this->sortTree->setOrderNum('1',0, DBTree::BOTH_NAME);
	 		$this->check(null, $this->rightTrees["reorder"][1], "Перестановка в конец", true,true);
	 		$this->sortTree->setOrderNum('6',3, DBTree::BOTH_NAME);
	 		$this->check(null, $this->rightTrees["reorder"][2], "Перестановка в середину", true,true);

	 		$this->sortTree->moveNode(19);
	 		$this->check(null, $this->rightTrees["reorder"][3], "Перестановка на 1 вниз", true,true);
	 		$this->sortTree->moveNode('6',1,DBTree::MOVE_UP, DBTree::BOTH_NAME);
	 		$this->check(null, $this->rightTrees["reorder"][4], "Перестановка на 1 вверх", true,true);
	 		$this->sortTree->moveNode(19,3,DBTree::MOVE_UP);
	 		$this->check(null, $this->rightTrees["reorder"][5], "Перестановка на 3 вверх", true,true);
	 		$this->sortTree->moveNode(19,2);
	 		$this->check(null, $this->rightTrees["reorder"][6], "Перестановка на 2 вниз", true,true);
	 		$this->sortTree->moveNode(19,100,DBTree::MOVE_UP);
	 		$this->check(null, $this->rightTrees["reorder"][7], "Перестановка выше возможного", true,true);

	 		$arr=$this->sortTree->getTree(array('sorder'=>"sorder"));
	 		$this->check($arr, $this->rightTrees["reorder"][8], "Проверка правильной сортировки");
	 	}
	 	
	 	/**
	 	 * Тестирует изменение родителя с сортировкой
	 	 */
	 	protected function testMoveSortData(){
 			$this->printHeader("Тесты изменения родителя с сортировкой", false);
	 		
 			$this->sortTree->changePar('1.2','6.1', DBTree::BOTH_NAME);
	 		$this->check(null, $this->rightTrees["sortmove"][0], "Перемещение с сортировкой", true, true);
 			
	 		$this->sortTree->changePar('3','6.1', DBTree::BOTH_NAME,1);
	 		$this->check(null, $this->rightTrees["sortmove"][1], "Перемещение с сортировкой и указанием номера по-порядку", true, true);
	 	}

	 	/**
	 	 * Тестирует удаление сортированных данных.
	 	 */
	 	protected function testDeleteSortData(){
 			$this->printHeader("Тесты удаления с сортировкой", false);
 			
	 		$this->sortTree->deleteSubTree('1', DBTree::BOTH_NAME);
	 		$this->check(null, $this->rightTrees["sortdelete"][0], "Удаление c конца", true, true);
 			
	 		$this->sortTree->deleteSubTree('5', DBTree::BOTH_NAME);
	 		$this->check(null, $this->rightTrees["sortdelete"][1], "Удаление с начала", true, true);
	 		
	 		$this->sortTree->deleteSubTree('6', DBTree::BOTH_NAME);
	 		$this->check(null, $this->rightTrees["sortdelete"][2], "Удаление с середины", true, true);
	 		
	 		$arr=$this->sortTree->getTree(array('sorder'=>"sorder"));
	 		$this->check($arr, $this->rightTrees["sortdelete"][3], "Проверка правильной сортировки после удаления");
	 	}
	 	
	 	/**
	 	 * Тестирует определение родителя.
	 	 */
	 	protected function testGetParent(){
 			$this->printHeader("Тесты получения родителя", false);
 			$par=$this->tree->getParent(2);
	 		$this->check($par+0, 1, "Проверка получения предка");
 			$par=$this->tree->getParent(1);
	 		$this->check($par+0, 1, "Проверка получения предка корня");
	 		$par=$this->tree->getParent(5, DBTree::NO_NAME);
	 		$this->check($par+0, 4, "Проверка получения предка с NO_NAME");
 			$par=$this->tree->getParent('1', DBTree::CHILD_NAME);
	 		$this->check($par+0, 1, "Проверка получения предка с CHILD_NAME");
 			$par=$this->tree->getParent('1', DBTree::BOTH_NAME);
 			$this->check($par+0, 1, "Проверка получения предка с BOTH_NAME");
 			$par=$this->tree->getParent(2, DBTree::PARENT_NAME);
 			$this->check($par+0, 1, "Проверка получения предка с PARENT_NAME");

	 	 	try{
	 			$par=$this->tree->getParent(80);
	 	 		$this->check(false, true, "Попытка по id получить предка несуществующего узла");
	 		}catch(SqlException $e){
	 			$this->check(true, true, "Попытка по id получить предка несуществующего узла");
	 		}
	 	 	try{
	 			$par=$this->tree->getParent('sdf', DBTree::CHILD_NAME);
	 	 		$this->check(false, true, "Попытка по имени получить предка несуществующего узла");
	 		}catch(SqlException $e){
	 			$this->check(true, true, "Попытка по имени получить предка несуществующего узла");
	 		}
	 	}
	 	
	 	/**
	 	 * Тестирует смену родителей.
	 	 */
	 	protected function testMoveData(){
 			$this->printHeader("Тесты перемещения", false);
	 		$this->tree->changePar(4,21);
	 		$this->check(null, $this->rightTrees["move"][0], "Перемещение по id", true);
	 		$this->tree->changePar(9,7, DBTree::NO_NAME);
	 		$this->check(null, $this->rightTrees["move"][1], "Перемещение по id с NO_NAME", true);
	 		$this->tree->changePar('6',18, DBTree::CHILD_NAME);
	 		$this->check(null, $this->rightTrees["move"][2], "Перемещение по имени с указанием id родителя", true);
	 		$this->tree->changePar(14,'6', DBTree::PARENT_NAME);
	 		$this->check(null, $this->rightTrees["move"][3], "Перемещение по id с указанием имени родителя", true);
	 		$this->tree->changePar('6.1','5', DBTree::BOTH_NAME);
	 		$this->check(null, $this->rightTrees["move"][4], "Перемещение по именам", true);
	 		
	 		$this->tree->changePar(2,1);
	 		$this->check(null, $this->rightTrees["move"][4], "Перемещение на текущего родителя", true);
	 		$this->tree->changePar(20,18, DBTree::NO_NAME);
	 		$this->check(null, $this->rightTrees["move"][4], "Перемещение на текущего родителя по id с NO_NAME", true);
	 		$this->tree->changePar('1.3.3',9, DBTree::CHILD_NAME);
	 		$this->check(null, $this->rightTrees["move"][4], "Перемещение на текущего родителя по имени с указанием id родителя", true);
	 		$this->tree->changePar(19,'0', DBTree::PARENT_NAME);
	 		$this->check(null, $this->rightTrees["move"][4], "Перемещение на текущего родителя по id с указанием имени родителя", true);
	 		$this->tree->changePar('1.1','1', DBTree::BOTH_NAME);
	 		$this->check(null, $this->rightTrees["move"][4], "Перемещение на текущего родителя по именам", true);
	 		
	 		try{
	 			$this->tree->changePar(2,2);
	 			$this->check(false, true, "Перемещение на себя");
	 		}catch(FormatException $e){
	 			$this->check(true, true, "Перемещение на себя");
	 		}
	 		try{
	 			$this->tree->changePar(20,20, DBTree::NO_NAME);
	 			$this->check(false, true, "Перемещение на себя по id с NO_NAME");
	 		}catch(FormatException $e){
	 			$this->check(true, true, "Перемещение на себя по id с NO_NAME");
	 		}
	 		try{
	 			$this->tree->changePar('1.3',9, DBTree::CHILD_NAME);
	 			$this->check(false, true, "Перемещение на себя по имени с указанием id родителя");
	 		}catch(FormatException $e){
	 			$this->check(true, true, "Перемещение на себя по имени с указанием id родителя");
	 		}
	 		try{
	 			$this->tree->changePar(19,'5', DBTree::PARENT_NAME);
	 			$this->check(false, true, "Перемещение на себя по id с указанием имени родителя");
	 		}catch(FormatException $e){
	 			$this->check(true, true, "Перемещение на себя по id с указанием имени родителя");
	 		}
	 		try{
	 			$this->tree->changePar('1.1','1.1', DBTree::BOTH_NAME);
	 			$this->check(false, true, "Перемещение на себя по именам");
	 		}catch(FormatException $e){
	 			$this->check(true, true, "Перемещение на себя по именам");
	 		}
	 	}
	 	
	 	/**
	 	 * Тестирует удаление данных.
	 	 */
	 	protected function testDeleteData(){
 			$this->printHeader("Тесты удаления", false);
	 		$this->tree->deleteSubTree(14);
	 		$this->check(null, $this->rightTrees["delete"][0], "Удаление по id", true);
	 		$this->tree->deleteSubTree(12, DBTree::NO_NAME);
	 		$this->check(null, $this->rightTrees["delete"][1], "Удаление по id с NO_NAME", true);
	 		$this->tree->deleteSubTree('5', DBTree::CHILD_NAME);
	 		$this->check(null, $this->rightTrees["delete"][2], "Удаление по имени с CHILD_NAME", true);
	 		$this->tree->deleteSubTree('1', DBTree::BOTH_NAME);
	 		$this->check(null, $this->rightTrees["delete"][3], "Удаление по имени с BOTH_NAME", true);
	 		
	 		try{
	 			$this->tree->deleteSubTree(1);
	 			$this->check(false, true, "Попытка удалить корень");
	 		}catch(FormatException $e){
	 			$this->check(true, true, "Попытка удалить корень");
	 		}
	 		
	 	}
	 	
	 	 	/**
	 	 * Тестирует добавление данных в дерево и выбор данных. 
	 	 */
	 	protected function testAddData(){
 			$this->printHeader("Тесты добавления и выбора", false);
			$this->insertTestData();

			
	 		$this->tree->add(2,1);
	 		$this->check(null, $this->rightTrees["add"][0], "Добавление по id", true);
	 		
	 		try{
	 			$this->tree->add(3,80);
	 			$this->check(null, $this->rightTrees["add"][0], "Попытка создать связь c несуществующим узлом", true);
	 		}catch(SqlException $e){
	 			$this->check(true, true, "Попытка создать связь c несуществующим узлом");
	 		}
	 		
	 		
	 		$this->tree->add(3,2, DBTree::NO_NAME);
	 		$this->check(null, $this->rightTrees["add"][1], "Добавление по id с указанием NO_NAME", true);

	 		$this->tree->add(4,'1', DBTree::PARENT_NAME);
	 		$this->check(null, $this->rightTrees["add"][2], "Добавление по имени с указанием имени родителя", true);
	 		
	 		$this->tree->add('1.2.1',4,DBTree::CHILD_NAME);
	 		$this->check(null, $this->rightTrees["add"][3], "Добавление по id с указанием id родителя", true);
	 		
	 		$this->tree->add('1.2.2','1.2', DBTree::BOTH_NAME);
	 		$this->check(null, $this->rightTrees["add"][4], "Добавление по именам", true);
	 		
	 		$this->tree->add('1.2.2.1','1.2.2', DBTree::BOTH_NAME);
	 		$this->tree->add('1.2.3','1.2', DBTree::BOTH_NAME);
	 		$this->tree->add('1.3','1', DBTree::BOTH_NAME);
	 		$this->tree->add('1.3.1','1.3', DBTree::BOTH_NAME);
	 		$this->tree->add('1.3.2','1.3', DBTree::BOTH_NAME);
	 		$this->tree->add('1.3.3','1.3', DBTree::BOTH_NAME);
	 		$this->tree->add('2','0', DBTree::BOTH_NAME);
	 		$this->tree->add('3','0', DBTree::BOTH_NAME);
	 		$this->tree->add('3.1','3', DBTree::BOTH_NAME);
	 		$this->tree->add('3.2','3', DBTree::BOTH_NAME);
	 		$this->tree->add('4','0', DBTree::BOTH_NAME);
	 		$this->tree->add('4.1','4', DBTree::BOTH_NAME);
	 		$this->tree->add('5','0', DBTree::BOTH_NAME);
	 		$this->tree->add('6','0', DBTree::BOTH_NAME);
	 		$this->tree->add('6.1','6', DBTree::BOTH_NAME);
	 		$this->check(null, $this->rightTrees["add"][5], "Проверка всего дерева", true);
	 		
	 		try{
	 			$this->tree->add(80,1);
	 			$this->check(false, true, "Попытка создать связь для несуществующего узла");
	 		}catch(SqlException $e){
	 			$this->check(true, true, "Попытка создать связь для несуществующего узла");
	 		}
	 	}
	 	
		public function goTest(){
	 			$this->printHeader($this->header);
				$this->createTables();
	 			$this->setRightTree();
			try{
	 			$this->testGetEmptyData();
				$this->testAddData();
	 			$this->testGetParent();
	 			$this->testMoveData();
	 			$this->testDeleteData();

				$this->createTables();

				$this->testAddSortData();
	 			$this->testReOrderData();
				$this->testMoveSortData();
	 			$this->testDeleteSortData();
	 			
	 			$this->printEnd($this->header);
			}catch(TestException $e){
				$this->printEnd($this->header);
//	 		die();
			}
	 		$this->deleteTables();
	 	}
	 	
	 	
	 	
	 }
