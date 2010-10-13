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
	 * Класс для тестирования деревьев NS.
	 *  
	 * @package tests
	 * @subpackage trees
	 */
	 class NSTree_Tester extends DBTree_Tester{
	 	
	 	public function __construct($print=false, $DBCon=null){
	 		parent::__construct($print, $DBCon);
	 		$this->header="NSTree тестер!";
	 		
	 		$arrFields=array("table"=>"NSTree", "idField"=>"id", "leftField"=>"leftKey", "rightField"=>"rightKey", "levelField"=>"level",
	 			"nameTable"=>"NSTreeData","idNameField"=>"id", "nameField"=>"content");
	 		$this->tree= new NSTree($arrFields, $DBCon);
	 		
	 		$arrSortFields=array("table"=>"NSSortTree", "idField"=>"id", "leftField"=>"leftKey", "rightField"=>"rightKey", "levelField"=>"level",
	 			"nameTable"=>"NSSortTreeData","idNameField"=>"id", "nameField"=>"content", "orderField"=>"sorder");
	 		$this->sortTree= new NSTree($arrSortFields, $DBCon);
	 	}
	 	
	 	protected function createTablesmssql(){
			global $vf;
			$dbName=$vf["test"]["db"];
		 	$db=$this->db;
	 		$db->exec("use $dbName");
	 		
			$this->deleteTablesmssql();
			
	 		$db->exec("create table NSTreeData (
	 			id int PRIMARY KEY identity(1,1),
		 		content nvarchar(20) unique
	 		)");
	 		$db->exec("create table NSTree (
		 		id int REFERENCES NSTreeData(id) on delete no action on update no action,
		 		leftKey int not null,
		 		rightKey int not null,
		 		level int not null
	 		)");
	 		
	 		$db->exec("create table NSSortTreeData (
		 		id int PRIMARY KEY identity(1,1),
		 		content nvarchar(20) unique,
		 		sorder int not null
		 	)");
	 		
	 		$db->exec("create table NSSortTree (
		 		id int REFERENCES NSSortTreeData(id) on delete no action on update no action,
		 		leftKey int not null,
		 		rightKey int not null,
		 		level int not null
	 		)");
	 		
	 		

	 		$db->insert("insert into NSTreeData(content) values('0')");
	 		$db->insert("insert into NSTree(id, leftKey, rightKey, level) values(1,1,2,1)");
	 		$db->insert("insert into NSSortTreeData(content,sorder) values('0', 0)");
	 		$db->insert("insert into NSSortTree(id, leftKey, rightKey, level) values(1,1,2,1)");
	 	}
	 	
	 	protected function createTablesmysql(){
			global $vf;
			$dbName=$vf["test"]["db"];
			$db=$this->db;
	 		$db->exec("use $dbName");
			$this->deleteTablesmysql();
			
	 		$db->exec("create table NSTreeData (
	 			id int PRIMARY KEY AUTO_INCREMENT,
		 		content varchar(20) unique
	 		) ENGINE=InnoDB");
	 		$db->exec("create table NSTree (
		 		id int,
		 		leftKey int not null,
		 		rightKey int not null,
		 		level int not null,
		 		FOREIGN KEY (id) REFERENCES NSTreeData(id) on delete no action on update no action
		 		) ENGINE=InnoDB");

	 		$db->exec("create table NSSortTreeData (
	 			id int PRIMARY KEY AUTO_INCREMENT,
		 		content varchar(20) unique,
		 		sorder int not null
	 		) ENGINE=InnoDB");
	 		$db->exec("create table NSSortTree (
		 		id int,
		 		leftKey int not null,
		 		rightKey int not null,
		 		level int not null,
		 		FOREIGN KEY (id) REFERENCES NSSortTreeData(id) on delete no action on update no action
		 		) ENGINE=InnoDB");
	 		
	 		$db->insert("insert into NSTreeData(content) values('0')");
	 		$db->insert("insert into NSTree(id, leftKey, rightKey, level) values(1,1,2,1)");
	 		$db->insert("insert into NSSortTreeData(content, sorder) values('0', 0)");
	 		$db->insert("insert into NSSortTree(id, leftKey, rightKey, level) values(1,1,2,1)");
	 	}
	 	
	 	protected function insertTestData(){
	 		$db=$this->db;
	 		$db->insert("insert into NSTreeData(content) values('1')");
	 		$db->insert("insert into NSTreeData(content) values('1.1')");
	 		$db->insert("insert into NSTreeData(content) values('1.2')");
	 	}
	 	
	 	protected function deleteTablesmssql(){
			global $vf;
			$dbName=$vf["test"]["db"];
	 		$db=$this->db;
	 		$db->exec("use $dbName");
	 		
			$db->exec("IF EXISTS(SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'NSTree') DROP TABLE NSTree;");
			$db->exec("IF EXISTS(SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'NSTreeData') DROP TABLE NSTreeData;");
			$db->exec("IF EXISTS(SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'NSSortTree') DROP TABLE NSSortTree;");
			$db->exec("IF EXISTS(SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'NSSortTreeData') DROP TABLE NSSortTreeData;");
	 	}
	 	
	 	protected function deleteTablesmysql(){
			global $vf;
			$dbName=$vf["test"]["db"];
	 		$db=$this->db;
	 		$db->exec("use $dbName");
	 		
			$db->exec("DROP TABLE IF EXISTS NSTree");
			$db->exec("DROP TABLE IF EXISTS NSTreeData");
			$db->exec("DROP TABLE IF EXISTS NSSortTree");
			$db->exec("DROP TABLE IF EXISTS NSSortTreeData");
	 	}
	 	
	 	
	 }
