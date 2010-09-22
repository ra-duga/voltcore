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
	 * Класс для тестирования деревьев AL.
	 *  
	 * @package tests
	 * @subpackage trees
	 */
	 class ALTree_Tester extends DBTree_Tester{
	 	
	 	public function __construct($print=false, $DBCon=null){
	 		parent::__construct($print, $DBCon);
	 		$this->header="ALTree тестер!";
	 		
	 		$arrFields=array("table"=>"ALTree", "idField"=>"cid", "idParField"=>"pid",
	 			"nameTable"=>"ALTreeData","idNameField"=>"id", "nameField"=>"content");
	 		$this->tree= new ALTree($arrFields, $DBCon);

	 		$arrSortFields=array("table"=>"ALSortTree", "idField"=>"cid", "idParField"=>"pid",
	 			"nameTable"=>"ALSortTreeData","idNameField"=>"id", "nameField"=>"content", "orderField"=>"sorder");
	 		$this->sortTree= new ALTree($arrSortFields, $DBCon);
	 	}
	 	
	 	protected function createTablesmssql(){
			global $vf;
			$dbName=$vf["test"]["db"];
		 	$db=$this->db;
	 		$db->exec("use $dbName");
	 		
			$this->deleteTablesmssql();
			
	 		$db->exec("create table ALSortTreeData (
		 		id int PRIMARY KEY identity(1,1),
		 		content nvarchar(20) unique,
		 		sorder int not null
		 	)");
	 		$db->exec("create table ALTreeData (
	 			id int PRIMARY KEY identity(1,1),
		 		content nvarchar(20) unique
	 		)");
	 		$db->exec("create table ALTree (
		 		cid int REFERENCES ALTreeData(id) on delete no action on update no action,
		 		pid int REFERENCES ALTreeData(id) on delete no action on update no action
	 		)");
	 		$db->exec("create table ALSortTree (
		 		cid int REFERENCES ALSortTreeData(id) on delete no action on update no action,
		 		pid int REFERENCES ALSortTreeData(id) on delete no action on update no action
	 		)");
	 		
	 		$db->insert("insert into ALTreeData(content) values('0')");
	 		$db->insert("insert into ALTree(cid, pid) values(1,1)");
	 		$db->insert("insert into ALSortTreeData(content, sorder) values('0', 0)");
	 		$db->insert("insert into ALSortTree(cid, pid) values(1,1)");
	 		
	 	}
	 	
	 	protected function createTablesmysql(){
			global $vf;
			$dbName=$vf["test"]["db"];
			$db=$this->db;
	 		$db->exec("use $dbName");
			$this->deleteTablesmysql();
			
			$db->exec("create table ALSortTreeData (
		 		id int PRIMARY KEY AUTO_INCREMENT,
		 		content varchar(20) unique,
		 		sorder int not null
		 	) ENGINE=InnoDB");
	 		$db->exec("create table ALTreeData (
	 			id int PRIMARY KEY AUTO_INCREMENT,
		 		content varchar(20) unique
	 		) ENGINE=InnoDB");
	 		$db->exec("create table ALTree (
		 		cid int,
		 		pid int,
		 		FOREIGN KEY (cid) REFERENCES ALTreeData(id) on delete no action on update no action,
		 		FOREIGN KEY (pid) REFERENCES ALTreeData(id) on delete no action on update no action
		 		) ENGINE=InnoDB");
	 		$db->exec("create table ALSortTree (
		 		cid int,
		 		pid int,
		 		FOREIGN KEY (cid) REFERENCES ALSortTreeData(id) on delete no action on update no action,
		 		FOREIGN KEY (pid) REFERENCES ALSortTreeData(id) on delete no action on update no action
		 		) ENGINE=InnoDB");
	 		
	 		$db->insert("insert into ALTreeData(content) values('0')");
	 		$db->insert("insert into ALTree(cid, pid) values(1,1)");
	 		$db->insert("insert into ALSortTreeData(content, sorder) values('0', 0)");
	 		$db->insert("insert into ALSortTree(cid, pid) values(1,1)");
	 		
	 	}
	 	
	 	protected function insertTestData(){
	 		$db=$this->db;
	 		$db->insert("insert into ALTreeData(content) values('1')");
	 		$db->insert("insert into ALTreeData(content) values('1.1')");
	 		$db->insert("insert into ALTreeData(content) values('1.2')");
	 	}
	 	
	 	protected function deleteTablesmssql(){
			global $vf;
			$dbName=$vf["test"]["db"];
	 		$db=$this->db;
	 		$db->exec("use $dbName");
	 		
			$db->exec("IF EXISTS(SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'ALTree') DROP TABLE ALTree;");
			$db->exec("IF EXISTS(SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'ALSortTree') DROP TABLE ALSortTree;");
			$db->exec("IF EXISTS(SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'ALTreeData') DROP TABLE ALTreeData;");
			$db->exec("IF EXISTS(SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'ALSortTreeData') DROP TABLE ALSortTreeData;");
	 	}
	 	
	 	protected function deleteTablesmysql(){
			global $vf;
			$dbName=$vf["test"]["db"];
	 		$db=$this->db;
	 		$db->exec("use $dbName");
	 		
			$db->exec("DROP TABLE IF EXISTS ALTree");
			$db->exec("DROP TABLE IF EXISTS ALSortTree");
			$db->exec("DROP TABLE IF EXISTS ALTreeData");
			$db->exec("DROP TABLE IF EXISTS ALSortTreeData");
	 	}
	 	
	 	
	 }
