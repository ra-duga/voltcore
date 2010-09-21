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
	 * Класс для тестирования деревьев RAL(FH).
	 *  
	 * @package tests
	 * @subpackage trees
	 */
	 class FHTree_Tester extends DBTree_Tester{
	 	
	 	public function __construct($print=false, $DBCon=null){
	 		parent::__construct($print, $DBCon);
	 		$this->header="FHTree тестер!";
	 		$this->tree= new FHTree("FHTree", "cid", "pid", "level", "FHTreeData",  'id', "content", null, $DBCon);
			$this->sortTree= new FHTree("FHSortTree", "cid", "pid", "level", "FHSortTreeData",  'id', "content", "sorder", $DBCon);
	 	}
	 	
	 	protected function createTablesmssql(){
			global $vf;
			$dbName=$vf["test"]["db"];
		 	$db=$this->db;
	 		$db->exec("use $dbName");
	 		
			$this->deleteTablesmssql();
			
	 		$db->exec("create table FHSortTreeData (
		 		id int PRIMARY KEY identity(1,1),
		 		content nvarchar(20) unique,
		 		sorder int not null
		 	)");
	 		$db->exec("create table FHTreeData (
	 			id int PRIMARY KEY identity(1,1),
		 		content nvarchar(20) unique
	 		)");
	 		$db->exec("create table FHTree (
		 		cid int REFERENCES FHTreeData(id) on delete no action on update no action,
		 		pid int REFERENCES FHTreeData(id) on delete no action on update no action,
		 		level int not null
	 		)");
	 		$db->exec("create table FHSortTree (
		 		cid int REFERENCES FHSortTreeData(id) on delete no action on update no action,
		 		pid int REFERENCES FHSortTreeData(id) on delete no action on update no action,
		 		level int not null
	 		)");
	 		
	 		$db->insert("insert into FHTreeData(content) values('0')");
	 		$db->insert("insert into FHTree(cid, pid, level) values(1,1,0)");
	 		$db->insert("insert into FHSortTreeData(content, sorder) values('0', 0)");
	 		$db->insert("insert into FHSortTree(cid, pid, level) values(1,1,0)");
	 		
	 	}
	 	
	 	protected function createTablesmysql(){
			global $vf;
			$dbName=$vf["test"]["db"];
			$db=$this->db;
			$this->deleteTablesmysql();
			
			$db->exec("create table FHSortTreeData (
		 		id int Primory key AUTO_INCREMENT,
		 		content varchar(20) unique,
		 		sorder int not null
		 	) ENGINE=InnoDB");
	 		$db->exec("create table FHTreeData (
	 			id int Primory key AUTO_INCREMENT,
		 		content varchar(20) unique
	 		) ENGINE=InnoDB");
	 		$db->exec("create table FHTree (
		 		cid int REFERENCES FHTreeData(id) on delete no action on update no action,
		 		pid int REFERENCES FHTreeData(id) on delete no action on update no action,
		 		level int not null
	 		) ENGINE=InnoDB");
	 		$db->exec("create table FHSortTree (
		 		cid int REFERENCES FHSortTreeData(id) on delete no action on update no action,
		 		pid int REFERENCES FHSortTreeData(id) on delete no action on update no action,
		 		level int not null
	 		) ENGINE=InnoDB");
	 		
	 		$db->insert("insert into FHTreeData(content) values('0')");
	 		$db->insert("insert into FHTree(cid, pid, level) values(1,1,0)");
	 		$db->insert("insert into FHSortTreeData(content, sorder) values('0', 0)");
	 		$db->insert("insert into FHSortTree(cid, pid, level) values(1,1,0)");
	 		
	 	}
	 	
	 	protected function insertTestData(){
	 		$db=$this->db;
	 		$db->insert("insert into FHTreeData(content) values('1')");
	 		$db->insert("insert into FHTreeData(content) values('1.1')");
	 		$db->insert("insert into FHTreeData(content) values('1.2')");
	 	}
	 	
	 	protected function deleteTablesmssql(){
			global $vf;
			$dbName=$vf["test"]["db"];
	 		$db=$this->db;
	 		$db->exec("use $dbName");
	 		
			$db->exec("IF EXISTS(SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'FHTree') DROP TABLE FHTree;");
			$db->exec("IF EXISTS(SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'FHSortTree') DROP TABLE FHSortTree;");
			$db->exec("IF EXISTS(SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'FHTreeData') DROP TABLE FHTreeData;");
			$db->exec("IF EXISTS(SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'FHSortTreeData') DROP TABLE FHSortTreeData;");
	 	}
	 	
	 	protected function deleteTablesmysql(){
			global $vf;
			$dbName=$vf["test"]["db"];
	 		$db=$this->db;
	 		
			$db->exec("DROP TABLE IF EXISTS FHTree");
			$db->exec("DROP TABLE IF EXISTS FHSortTree");
			$db->exec("DROP TABLE IF EXISTS FHTreeData");
			$db->exec("DROP TABLE IF EXISTS FHSortTreeData");
	 	}
	 	
	 	
	 }
