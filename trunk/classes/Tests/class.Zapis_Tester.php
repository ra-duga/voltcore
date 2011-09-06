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
	 * Класс для тестирования записи таблицы.
	 *  
	 * @category VoltCore
	 * @package VoltCoreClasses
	 * @subpackage Tests
	 */
	class Zapis_Tester extends Tester{
		
		public function goTest(){
			global $vf;
			$this->printHeader("Zapis тестер!");
			$this->createTables();
			
			$this->printText('<p>Проверка простой записи</p>');
			$zap=new SimpleZapisTest(null, $this->db);
			$this->check($zap->var,null,"Проверка на запрос несуществующего элемента");
			$this->check($zap->exists(),false,"Проверка проверки на существование");
			
			$zap->var=50;
			$this->check($zap->var,50,"Проверка на установку и запрос элемента");
			$zap->target=40;
			$this->check($zap->target,20,"Проверка перенаправления на метод возврата");
			$zap->permanent=50;
			$this->check($zap->permanent,'Перманент',"Проверка перенаправления на метод установки");
			$zap->reset();
			$this->check($zap->var,null,"Проверка сброса");
			$this->check($zap->permanent,null,"Проверка сброса 2");
			$zap->selectDefault();
			$this->check($zap->content,'default',"Проверка сброса на дефолтные поля");
			
			$zap->content='Это контент';
			$zap->insert();
			$this->check($zap->exists(),true,"Проверка на вставку");
			$this->check($zap->id,1,"Проверка на выборку значений после втавки");
			unset($zap);
			
			$cont=new SimpleZapisTest(1, $this->db);
			$this->check($cont->content,'Это контент',"Проверка на выборку значений по идентификатору в конструкторе");
			$cont->content='abc';
			$cont->update();
			unset($cont);
			
			$newZap=new SimpleZapisTest(1, $this->db);
			$this->check($newZap->content,'abc',"Проверка обновления значений");
			unset($newZap);
			
			$newZap=new SimpleZapisTest(array("content"=>'abc'), $this->db);
			$this->check($newZap->id,1,"Проверка на выборку по массиву в конструкторе");
			$newZap->delete();
			unset($newZap);
			
			$newZap=new SimpleZapisTest(1, $this->db);
			$this->check($newZap->exists,false,"Проверка удаления записи");
			unset($newZap);
			
			$zap=new SimpleZapisTest(null, $this->db);
			$zap->selectOrInsert(array("content"=>'cont'));
			$this->check($zap->id,2,"Проверка вставки через selectOrInsert");
			unset($zap);
			
			$zap=new SimpleZapisTest(null, $this->db);
			$zap->selectOrInsert(array("content"=>'cont'));
			$this->check($zap->id,2,"Проверка выборки через selectOrInsert");
			unset($zap);

			$newZap=new SimpleZapisTest(array('content'=>'brrr'), $this->db);
			$newZap->content='brrr';
			$newZap->insertOrUpdate();
			$this->check($newZap->id,3,"Проверка вставки через insertOrUpdate");
			unset($newZap);
			
			$newZap=new SimpleZapisTest(array('content'=>'brrr'), $this->db);
			$newZap->content='xbx';
			$newZap->insertOrUpdate();
			unset($newZap);
			
			$newZap=new SimpleZapisTest(array('content'=>'xbx'), $this->db);
			$this->check($newZap->id,3,"Проверка изменений через insertOrUpdate");
			
			
			$this->printText('<p>Проверка связи один ко многим</p>');
			$this->printText('<p>Связь с нижестоящей записью</p>');
			$s=new SlaveZapisTest(null, $this->db);
			$s->content='slave 1';
			$m=new MasterZapisTest(null, $this->db);
			$m->slave=$s;
			$m->content='Master 1';
			$m->insert();
			unset($s, $m);
			$s=new SlaveZapisTest(array('content'=>'slave 1'), $this->db);
			$this->check($s->content,'slave 1',"Проверка ввода нижестоящей записи при вводе вышестоящей");

			$idS=$s->id;
			
			$m=new MasterZapisTest(array('content'=>'Master 1'), $this->db);
			$this->check($m->slave->id,$idS,"Проверка установления идентификатора у записи с сылкой");
			
			$m->slave->content='Slave 1.5';			
			$m->update();
			$s2=new SlaveZapisTest($idS, $this->db);
			$this->check($s2->content,'Slave 1.5',"Проверка обновления нижестоящей записим при обновлении вышестоящей");
			unset($s, $s2, $m);
			
			$s2=new SlaveZapisTest($idS, $this->db);
			$m=new MasterZapisTest(null, $this->db);
			$m->slave=$s2;
			$m->content='Master 2';
			$m->slave->content='Slave 1 and 2';
			$m->insert();
			unset($s2,$m);
			$s=new SlaveZapisTest($idS, $this->db);
			$this->check($s->content,'Slave 1 and 2',"Проверка обновления нижестоящей записи при вводе вышестоящей");
			unset($s);
			
			$m=new MasterZapisTest(array('content'=>'Master 2'), $this->db);
			$s=new SlaveZapisTest(null, $this->db);
			$s->content='slave 2';
			$m->slave=$s;
			$m->update();
			unset($s, $m);
			$s=new SlaveZapisTest(array('content'=>'slave 2'), $this->db);
			$this->check($s->content,'slave 2',"Проверка ввода нижестоящей записи при обновлении вышестоящей");
			$m=new MasterZapisTest(array('content'=>'Master 2'), $this->db);
			$this->check($m->slave->id,$s->id,"Проверка изменения идентификатора у записи с сылкой");
			
			$this->printText('<p>Связь с вышестоящей записью</p>');
			$s=new SuperMasterZapisTest(null, $this->db);
			$s->content='Super 1';
			$m=new MasterZapisTest(null, $this->db);
			$m->super=$s;
			$m->content='Master 3';
			$m->insert();
			unset($s, $m);
			$s=new SuperMasterZapisTest(array('content'=>'Super 1'), $this->db);
			$this->check($s->content,'Super 1',"Проверка ввода вышестоящей записи при вводе нижестоящей");

			$idS=$s->id;
			
			$m=new MasterZapisTest(array('content'=>'Master 3'), $this->db);
			$this->check($m->super->id,$idS,"Проверка установления идентификатора у записи с сылкой");
			
			$m->super->content='Super 1.5';			
			$m->update();
			$s2=new SuperMasterZapisTest($idS, $this->db);
			$this->check($s2->content,'Super 1.5',"Проверка обновления вышестоящей записим при обновлении нижестоящей");
			unset($s, $s2, $m);
			
			$s2=new SuperMasterZapisTest($idS, $this->db);
			$m=new MasterZapisTest(null, $this->db);
			$m->super=$s2;
			$m->content='Master 4';
			$m->super->content='Super 1 and 2';
			$m->insert();
			unset($s2,$m);
			$s=new SuperMasterZapisTest($idS, $this->db);
			$this->check($s->content,'Super 1 and 2',"Проверка обновления вышестоящей записи при вводе нижестоящей");
			unset($s);
			
			$m=new MasterZapisTest(array('content'=>'Master 4'), $this->db);
			$s=new SuperMasterZapisTest(null, $this->db);
			$s->content='Super 2';
			$m->super=$s;
			$m->update();
			unset($s, $m);
			$s=new SuperMasterZapisTest(array('content'=>'Super 2'), $this->db);
			$this->check($s->content,'Super 2',"Проверка ввода вышестоящей записи при обновлении нижестоящей");
			$m=new MasterZapisTest(array('content'=>'Master 4'), $this->db);
			$this->check($m->super->id,$s->id,"Проверка изменения идентификатора у записи с сылкой");
			
			$this->printText('<p>Связь с нижестоящими записями</p>');
			$s=new SuperMasterZapisTest(null, $this->db);
			$s->content='Super main';
			$m1=new MasterZapisTest(null, $this->db);
			$m2=new MasterZapisTest(null, $this->db);
			$s->master=$m1;
			$s->master=$m2;
			$m1->content='MS1';
			$m2->content='MS2';
			$s->insert();
			unset($m1,$m2,$s);
			
			$s=new SuperMasterZapisTest(array('content'=>'Super main'), $this->db);
			$m1=new MasterZapisTest(array('content'=>'MS1'), $this->db);
			$m2=new MasterZapisTest(array('content'=>'MS2'), $this->db);
			$this->check($m1->exists(),true,"Проверка ввода нижестоящих записей при вводе вышестоящей");
			$this->check($m2->exists(),true,"Проверка второй записи");
			$this->check($m1->id_m,$s->id,"Проверка установки правильных идентификаторов");
			$this->check($m2->id_m,$s->id,"Проверка второй записи");
			
			$this->check($s->master[0],$m1,"Проверка на ленивую загрузку связанных записей");
			$this->check($s->master[1],$m2,"Проверка второй записи");
				
			$s->master[0]->content='MS12';
			$s->master[1]->content='MS22';
			$s->content='Super super';
			$s->update();
			unset($s, $m1, $m2);
			$s=new SuperMasterZapisTest(array('content'=>'Super super'), $this->db);
			$m1=new MasterZapisTest(array('content'=>'MS12'), $this->db);
			$m2=new MasterZapisTest(array('content'=>'MS22'), $this->db);
			$this->check($m1->exists(),true,"Проверка обновления нижестоящих записей при обновлении вышестоящей");
			$this->check($m2->exists(),true,"Проверка второй записи");
			$this->check($s->exists(),true,"Проверка обновления при этих связях");
			$this->check($m1->id_m,$s->id,"Проверка сохранения связей");
			$this->check($m2->id_m,$s->id,"Проверка сохранения связей");
			unset($s);
			
			$s=new SuperMasterZapisTest(null, $this->db);
			$s->content='Super predator';			
			$s->master=$m1;			
			$s->master=$m2;			
			$s->insert();
			unset($s, $m1, $m2);
			$s=new SuperMasterZapisTest(array('content'=>'Super super'), $this->db);
			$sp=new SuperMasterZapisTest(array('content'=>'Super predator'), $this->db);
			$m1=new MasterZapisTest(array('content'=>'MS12'), $this->db);
			$m2=new MasterZapisTest(array('content'=>'MS22'), $this->db);
			$this->check($m1->id_m,$sp->id,"Обновления нижестоящих записей при вводе выше стоящей и перенаправления связей");
			$this->check($m2->id_m,$sp->id,"Проверка второй записи");
			$this->check($s->master,null,"Проверка перенаправления записей и возвращения пустого массива связанных объектов");

			$m3=new MasterZapisTest(null, $this->db);
			$m3->content='ms3';
			$sp->master=$m3;
			$m4=new MasterZapisTest(null, $this->db);
			$m4->content='ms4';
			$sp->master=$m4;
			$sp->update();
			unset($s,$m1,$m2,$m3,$m4,$sp);
			$m1=new MasterZapisTest(array('content'=>'ms3'), $this->db);
			$m2=new MasterZapisTest(array('content'=>'ms4'), $this->db);
			$this->check($m1->exists(),true,"Проверка ввода нижестоящих записей при обновлении вышестоящей");
			$this->check($m2->exists(),true,"Проверка второй записи");
			$sp=new SuperMasterZapisTest(array('content'=>'Super predator'), $this->db);
			$this->check(count($sp->master),4,'Проверка проставления связей');
			unset($m1,$m2,$sp);
			
			
			$this->printText('<p>Связь многие ко многим</p>');
			$s1=new SuperMasterZapisTest(1, $this->db);
			$s2=new SuperMasterZapisTest(2, $this->db);
			$a1=new AnotherZapisTest(null, $this->db);
			$a2=new AnotherZapisTest(null, $this->db);
			$a1->content='Another 1';
			$a2->content='Another 2';
			$a1->super=$s1;			
			$a1->super=$s2;			
			$a2->super=$s1;			
			$a2->super=$s2;			
			$s1->content='NewSuper';
			$s2->content='VeryNewSuper';
			$a1->insert();
			$a2->insert();
			unset($s1,$s2,$a1,$a2);
			
			$s1=new SuperMasterZapisTest(array('content'=>'NewSuper'), $this->db);
			$s2=new SuperMasterZapisTest(array('content'=>'VeryNewSuper'), $this->db);
			$this->check($s1->exists(),true,"Проверка обновления записей при связях многие ко многим при вводе связанной запии");
			$this->check($s2->exists(),true,"Проверка второй записи");
			$this->check($s1->another[0]->id,1,"Проверка ввода и ленивой подгрузки связей многие ко многим. Первая запись");
			$this->check($s1->another[1]->id,2,"Проверка второй записи для первой записи");
			$this->check($s2->another[0]->id,1,"Проверка ввода и ленивой подгрузки связей многие ко многим. Вторая запись");
			$this->check($s2->another[1]->id,2,"Проверка второй записи для второй записи");
			unset($s1);
			
			$ref=$this->db->getVal('select id_a from Another_SuperMaster where id_a=1 and id_m=1');
			$this->check($ref,1,"Проверка наличия связи многие ко многим");
			
			$a1=new AnotherZapisTest(null, $this->db);
			$a2=new AnotherZapisTest(null, $this->db);
			$s1=new SuperMasterZapisTest(array('content'=>'NewSuper'), $this->db);
			$a1->content='Another 3';
			$a2->content='Another 4';
			$s1->another=$a1;
			$s1->another=$a2;
			$s1->content='Final Super';
			$s1->update();
			unset($s1,$s2,$a1,$a2);
			$a1=new AnotherZapisTest(array('content'=>'Another 3'), $this->db);
			$a2=new AnotherZapisTest(array('content'=>'Another 4'), $this->db);
			$this->check($a1->exists(),true,"Проверка ввода записей при связях многие ко многим при обновлении связанной запии");
			$this->check($a2->exists(),true,"Проверка второй записи");
			$s1=new SuperMasterZapisTest(array('content'=>'Final Super'), $this->db);
			$this->check($s1->another[0]->id,3,"Проверка отсутствия старых связей многие ко многим");
			
			$oldRef=$this->db->getVal('select id_a from Another_SuperMaster where id_a=1 and id_m=1');
			$this->check($oldRef,false,"Проверка затирания старых связей многие ко многим");
			unset($a1,$a2,$s1);
			
			
			$s=new SuperMasterZapisTest(1, $this->db);
			$s->master;
	 	 	try{
				$s->delete();
	 	 		$this->check(false, true, "Проверка выбрасывания исключения");
	 		}catch(SqlException $e){
	 			$this->check(true, true, "Проверка выбрасывания исключения");
	 		}
			$m=new MasterZapisTest(3,$this->db);
			$this->check($m->exists(),true,"Проверка отката транзакции");
			unset($s,$m);			
			
			$s=new SuperMasterZapisTest(1, $this->db);
			$s->master;
			$s->another;
			$s->delete();
			$m=new MasterZapisTest(3,$this->db);
			$this->check($m->exists(),false,"Проверка удаления нижестоящей записи");
			$oldRef=$this->db->getVal('select id_a from Another_SuperMaster where id_m=1');
			$this->check($oldRef,false,"Проверка удаления связей многие ко многим");
			unset($s,$m);			
						
			
			$this->printText('<p>Большой тест на все записи</p>');
			$s1=new SlaveZapisTest(null,$this->db);
			
			$m1=new MasterZapisTest(null,$this->db);
			$m2=new MasterZapisTest(null,$this->db);
			$m3=new MasterZapisTest(null,$this->db);

			$sm1=new SuperMasterZapisTest(null,$this->db);
			$sm2=new SuperMasterZapisTest(null,$this->db);
			
			$a1=new AnotherZapisTest(null,$this->db);
			$a2=new AnotherZapisTest(null,$this->db);
			$a3=new AnotherZapisTest(null,$this->db);
			$a4=new AnotherZapisTest(null,$this->db);
			
			
			$s1->content='FT Slave 1';
			$m1->content='FT Master 1';
			$m2->content='FT Master 2';
			$m3->content='FT Master 3';
			$sm1->content='FT SuperMaster 1';
			$sm2->content='FT SuperMaster 2';
			$a1->content='FT Another 1';
			$a2->content='FT Another 2';
			$a3->content='FT Another 3';
			$a4->content='FT Another 4';
			
			$a1->super=$sm1;
			$a2->super=$sm1;
			$a2->super=$sm2;
			$a3->super=$sm1;
			$a3->super=$sm2;
			$a4->super=$sm2;
			$sm1->master=$m1;
			$sm1->master=$m2;
			$sm2->master=$m3;
			$m1->slave=$s1;
			$m2->slave=$s1;
			$m3->slave=$s1;
			
			$a1->insert();
			$a2->insert();
			$a3->insert();
			$a4->insert();
			
			unset($s1);
			unset($a1,$a2,$a3,$a4);
			unset($sm1,$sm2);
			unset($m1,$m2,$m3);

			$s1=new SlaveZapisTest(array('content'=>'FT Slave 1'),$this->db);
			
			$m1=new MasterZapisTest(array('content'=>'FT Master 1'),$this->db);
			$m2=new MasterZapisTest(array('content'=>'FT Master 2'),$this->db);
			$m3=new MasterZapisTest(array('content'=>'FT Master 3'),$this->db);

			$sm1=new SuperMasterZapisTest(array('content'=>'FT SuperMaster 1'),$this->db);
			$sm2=new SuperMasterZapisTest(array('content'=>'FT SuperMaster 2'),$this->db);
			
			$a1=new AnotherZapisTest(array('content'=>'FT Another 1'),$this->db);
			$a2=new AnotherZapisTest(array('content'=>'FT Another 2'),$this->db);
			$a3=new AnotherZapisTest(array('content'=>'FT Another 3'),$this->db);
			$a4=new AnotherZapisTest(array('content'=>'FT Another 4'),$this->db);
			
			$this->check($s1->exists(),true,"Slave 1");
			$this->check($m1->exists(),true,"Master 1");
			$this->check($m2->exists(),true,"Master 2");
			$this->check($m3->exists(),true,"Master 3");
			$this->check($sm1->exists(),true,"SuperMaster 1");
			$this->check($sm2->exists(),true,"SuperMaster 2");
			$this->check($a1->exists(),true,"Another 1");
			$this->check($a2->exists(),true,"Another 2");
			$this->check($a3->exists(),true,"Another 3");
			$this->check($a4->exists(),true,"Another 4");
			unset($s1);
			unset($a1,$a2,$a3,$a4);
			unset($sm1,$sm2);
			unset($m1,$m2,$m3);
						
			$m=new MasterZapisTest(null,$this->db);
			$sm=new SuperMasterZapisTest(null,$this->db);
			
			$m->content='Steck master';
			$sm->content='Steck SuperMaster';
			$m->super=$sm;
			$sm->master=$m;
			$m->insert();
			$this->check(true,true,"Проверка отсутствия зацикаливания при вводе связанных записей");
			unset($m,$sm);
			
			$m=new MasterZapisTest(array('content'=>'Steck master'),$this->db);
			$sm=new SuperMasterZapisTest(array('content'=>'Steck SuperMaster'),$this->db);
			$this->check($m->exists(),true,"Проверка ввода связанных записей");
			$this->check($sm->exists(),true,"проверка второй записи");
			unset($m,$sm);
			
			$a=new AnotherZapisTest(null,$this->db);
			$sm=new SuperMasterZapisTest(null,$this->db);
			
			$a->content='Data another';
			$sm->content='Data SuperMaster';
			$a->super($sm,array('content'=>'dataCon'));
			$a->insert();
			unset($a,$sm);
			
			$dataTest=$this->db->getVal("select asm.content from Another_SuperMaster as asm join anotherZap as az on asm.id_a=az.id where az.content='Data another'");
			$this->check($dataTest,'dataCon',"Проверка ввода дополнительных данных в таблицу связей многие ко многим");
			
			
			$a1=new AnotherZapisTest(1,$this->db);
			$a2=new AnotherZapisTest(2,$this->db);
			$a3=new AnotherZapisTest(3,$this->db);
			$sm=new SuperMasterZapisTest(2,$this->db);
			
			$sm->another($a1, array('content'=>'data1'));
			$sm->another=$a2;
			$a2->super=$sm;
			$sm->another($a3, array('content'=>'data3'));
			$sm->update();
			unset($sm,$a1,$a2,$a3);
			
			$dataTest=$this->db->getVal("select id_a from Another_SuperMaster where content='data1'");
			$this->check($dataTest,1,"Проверка ввода верных дополнительных данных в таблицу связей многие ко многим");
			$dataTest=$this->db->getVal("select id_m from Another_SuperMaster where id_a=2");
			$this->check($dataTest,2,"Проверка ввода в таблицу связей многие ко многим при наличии доп. данных у других связанных записей");
			$dataTest=$this->db->getVal("select id_a from Another_SuperMaster where content='data3'");
			$this->check($dataTest,3,"Проверка ввода верных дополнительных данных в таблицу связей многие ко многим");
						
			
			
			$this->deleteTables();
			$this->printEnd("Zapis тестер!");
			
		}

		 protected function createTablesmssql(){
			global $vf;
			$dbName=$vf["test"]["db"];
		 	$db=$this->db;
	 		$db->exec("use $dbName");
	 		
			$this->deleteTablesmssql();
			
	 		$db->exec("create table SimpleZap (
		 		id int PRIMARY KEY identity(1,1),
		 		content nvarchar(20) unique
		 	)");

	 		$db->exec("create table SlaveZap (
		 		id int PRIMARY KEY identity(1,1),
		 		content nvarchar(20) unique
		 	)");
	 		
	 		$db->exec("create table SuperMasterZap (
		 		id int PRIMARY KEY identity(1,1),
		 		content nvarchar(20) unique
		 	)");
	 		
	 		$db->exec("create table MasterZap (
		 		id int PRIMARY KEY identity(1,1),
		 		id_s int null REFERENCES SlaveZap(id) on delete no action on update no action,
		 		id_m int null REFERENCES SuperMasterZap(id) on delete no action on update no action,
		 		content nvarchar(20) unique
		 	)");

	 		$db->exec("create table AnotherZap (
		 		id int PRIMARY KEY identity(1,1),
		 		content nvarchar(20) unique
		 	)");
	 		
	 		$db->exec("create table Another_SuperMaster (
		 		id_a int REFERENCES AnotherZap(id) on delete no action on update no action,
		 		id_m int REFERENCES SuperMasterZap(id) on delete no action on update no action,
		 		content nvarchar(20)
	 		)");
	 		
		 }
	 	
	 	protected function createTablesmysql(){
			global $vf;
			$dbName=$vf["test"]["db"];
			$db=$this->db;
	 		$db->exec("use $dbName");
			$this->deleteTablesmysql();
			
			$db->exec("create table SimpleZap (
		 		id int PRIMARY KEY AUTO_INCREMENT,
		 		content varchar(20) unique
		 	) ENGINE=InnoDB");
	 	
	 		$db->exec("create table SlaveZap (
		 		id int PRIMARY KEY AUTO_INCREMENT,
	 			content nvarchar(20) unique
		 	) ENGINE=InnoDB");
	 		
	 		$db->exec("create table SuperMasterZap (
		 		id int PRIMARY KEY AUTO_INCREMENT,
		 		content nvarchar(20) unique
		 	) ENGINE=InnoDB");

	 		$db->exec("create table MasterZap (
		 		id int PRIMARY KEY AUTO_INCREMENT,
		 		id_s int null,
		 		id_m int null,
		 		content nvarchar(20) unique,
		 		FOREIGN KEY (id_s) REFERENCES SlaveZap(id) on delete no action on update no action,
		 		FOREIGN KEY (id_m) REFERENCES SuperMasterZap(id) on delete no action on update no action
		 	) ENGINE=InnoDB");

	 		$db->exec("create table AnotherZap (
		 		id int PRIMARY KEY AUTO_INCREMENT,
		 		content nvarchar(20) unique
		 	) ENGINE=InnoDB");
	 		
	 		$db->exec("create table Another_SuperMaster (
		 		id_a int,
		 		id_m int,
		 		FOREIGN KEY (id_a) REFERENCES AnotherZap(id) on delete no action on update no action,
		 		FOREIGN KEY (id_m) REFERENCES SuperMasterZap(id) on delete no action on update no action
		 		content nvarchar(20)
		 	) ENGINE=InnoDB");
	 	}
	 	
	 	protected function deleteTablesmssql(){
			global $vf;
			$dbName=$vf["test"]["db"];
	 		$db=$this->db;
	 		$db->exec("use $dbName");
	 		
			$db->exec("IF EXISTS(SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'SimpleZap') DROP TABLE SimpleZap;");
			$db->exec("IF EXISTS(SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'MasterZap') DROP TABLE MasterZap;");
			$db->exec("IF EXISTS(SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'Another_SuperMaster') DROP TABLE Another_SuperMaster;");
	 		$db->exec("IF EXISTS(SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'SlaveZap') DROP TABLE SlaveZap;");
			$db->exec("IF EXISTS(SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'SuperMasterZap') DROP TABLE SuperMasterZap;");
			$db->exec("IF EXISTS(SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'AnotherZap') DROP TABLE AnotherZap;");
	 	}
	 	
	 	protected function deleteTablesmysql(){
			global $vf;
			$dbName=$vf["test"]["db"];
	 		$db=$this->db;
	 		$db->exec("use $dbName");
	 		
			$db->exec("DROP TABLE IF EXISTS SimpleZap");
			$db->exec("DROP TABLE IF EXISTS MasterZap");
			$db->exec("DROP TABLE IF EXISTS Another_SuperMaster");
			$db->exec("DROP TABLE IF EXISTS SlaveZap");
			$db->exec("DROP TABLE IF EXISTS SuperMasterZap");
			$db->exec("DROP TABLE IF EXISTS AnotherZap");
	 	}
	}

	/**
	 * Простая запись для тестов.
	 * 
	 * @category VoltCore
	 * @package VoltCoreClasses
	 * @subpackage Tests
	 */
	class SimpleZapisTest extends Zapis{

		public function __construct($id=null, $db=null){
			$this->emptyFields=array("id"=>-1, "content"=>"default");
			parent::__construct('simpleZap', $id, 'id', $db);
		}
		
		/**
		 * Возвращает значение свойства target
		 */
		protected function getTarget(){
			return 20;
		}

		/**
		 * Устанавливает значение свойства permanent
		 */
		protected function setPermanent($val){
			$this->fields['permanent']='Перманент';
		} 
		
	}
	
	/**
	 * Зависимая запись запись для тестов.
	 * 
	 * @category VoltCore
	 * @package VoltCoreClasses
	 * @subpackage Tests
	 */
	class SlaveZapisTest extends Zapis{

		public function __construct($id=null, $db=null){
			$this->emptyFields=array("id"=>-1, "content"=>"default");
			parent::__construct('slaveZap', $id, 'id', $db);
		}
	}
	
	/**
	 * Суперзапись для тестов.
	 * 
	 * @category VoltCore
	 * @package VoltCoreClasses
	 * @subpackage Tests
	 */
	class SuperMasterZapisTest extends Zapis{

		public function __construct($id=null, $db=null){
			$this->emptyFields=array("id"=>-1, "content"=>"default");
			$this->fks=array(
				'master'=>array(
					'class'=>'MasterZapisTest',
					'table'=>'MasterZap',
					'oId'=>'id',
					'otherKey'=> 'id_m', 
		  			'thisKey'=> 'id', 
		  			'direction'=>self::TOZAP,
		  			'val'=>array(),
		  		),
				'another'=>array(
					'class'=>'AnotherZapisTest',
		  			'table'=>'AnotherZap',
					'oId'=>'id',
		  			'otherKey'=> 'id', 
		  			'thisKey'=> 'id', 
		  			'direction'=>self::MMZAP,
		  			'val'=>array(),
		 			'mtm'=> array(
		 				'table'=>'Another_SuperMaster',
		 				'otherKey'=>'id_a', 
		 				'thisKey'=>'id_m',
		  				'data'=>array()
		 			) 
		  		)
			);
			parent::__construct('SuperMasterZap', $id, 'id', $db);
		}
	}
	
	/**
	 * Мастерзапись для тестов.
	 * 
	 * @category VoltCore
	 * @package VoltCoreClasses
	 * @subpackage Tests
	 */
	class MasterZapisTest extends Zapis{

		private $toDBCount=0;
		
		public function __construct($id=null, $db=null){
			$this->emptyFields=array("id"=>-1, "content"=>"default");
			parent::__construct('MasterZap', $id, 'id', $db);
			$this->fks=array(
				'slave'=>array(
					'class'=>'SlaveZapisTest',
					'table'=>'SlaveZap',
					'oId'=>'id',
					'otherKey'=> 'id', 
		  			'thisKey'=> 'id_s', 
		  			'direction'=>self::FROMZAP,
		  			'val'=>null,
		  		),
				'super'=>array(
					'class'=>'SuperMasterZapisTest',
		  			'table'=>'SuperMasterZap',
					'oId'=>'id',
		  			'otherKey'=> 'id', 
		  			'thisKey'=> 'id_m', 
		  			'direction'=>self::FROMZAP,
		  			'val'=>null,
		  		)
			);
		}
		
		public function insert(){
			$this->toDBCount++;
			$rez=parent::insert();
			if ($this->toDBCount>1 && $rez){
				throw new SqlException('Многократный ввод', "Перепонение стека", 'Ввод связанных записей');
			} 
			$this->toDBCount--;
		}

		public function update(){
			$this->toDBCount++;
			$rez=parent::update();
			if ($this->toDBCount>1 && $rez){
				throw new SqlException('Многократный ввод', "Перепонение стека", 'Обновление связанных записей');
			} 
			$this->toDBCount--;
		}
	}
	
	/**
	 * Другая запись для тестов.
	 * 
	 * @category VoltCore
	 * @package VoltCoreClasses
	 * @subpackage Tests
	 */
	class AnotherZapisTest extends Zapis{

		public function __construct($id=null, $db=null){
			$this->emptyFields=array("id"=>-1, "content"=>"default");
			parent::__construct('AnotherZap', $id, 'id', $db);
			$this->fks=array(
				'super'=>array(
					'class'=>'SuperMasterZapisTest',
					'table'=>'SuperMasterZap',
					'oId'=>'id',
					'otherKey'=> 'id', 
		  			'thisKey'=> 'id', 
		  			'direction'=>self::MMZAP,
		  			'val'=>array(),
		 			'mtm'=> array(
		 				'table'=>'Another_SuperMaster',
		 				'otherKey'=>'id_m', 
		 				'thisKey'=>'id_a',
		  				'data'=>array()
		 			) 
		  		)
			);
		}
	}
	
	
	