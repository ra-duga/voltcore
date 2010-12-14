<?php
	/**
	 * Библиотека функций для работы с extJS.
	 * 
	 * Данная библиотека содержит функции для облегчения работы с extJS.  
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @version 1.0
	 * @category VoltCore
	 * @package VoltCoreFiles
	 * @subpackage Libs
	 */

	/**
	 * Создает из содержания файлов основу для вкладок TabPanel 
	 * 
	 * Создает из содержания файлов div'ы, которые могут автоматически преобразоваться в закладки TabPanel.
	 * 
	 * @param string $dir директория из которой брать файлы  
	 */
	function tabsFromFiles($dir=false){
		tabsFromArrFiles(getPHPFiles($dir));
	}
	
	/**
	 * Создает из содержания файлов основу для вкладок TabPanel 
	 * 
	 * Создает из содержания файлов div'ы, которые могут автоматически преобразоваться в закладки TabPanel.
	 * @param array $arr Массив с путями к файлам
	 */
	function tabsFromArrFiles($arr){
		foreach ($arr as $file){
			$name=ucfirst(substr(basename($file),0,-4)); // обрезаем имя папки, расширение и делаем первую букву заглавной
			echo "<div class='x-tab' title='$name'>";
			include($file);
			echo "</div>";
		}
	}
	
	/**
	 * Определяет метаданные поля.
	 *  
	 * @param array $row Имя, длина и имя типа данных поля.
	 * @param bool $sortable Можно ли сортировать данные.
	 * @param bool $editor Нужно ли создавать едитор.
	 * @return array Мета данные для поля.
	 */
	function determineExtJsFieldMetaData($row, $sortable=true, $editor=true){
		$rez=array();
		$rez['sortable']=$sortable ? 'true' : 'false';
		switch ($row["tname"]){
			case 'nchar':
			case 'nvarchar':
				$row['flen']=$row['flen']/2;
			case 'char':
			case 'varchar':
				$rez['width']=min(300,$row['flen']*15);
				$rez['record']['type']='string';
				if($editor){
					$rez['editor']['xtype']='"textfield"';
					$rez['editor']['maxLength']=$row['flen'];
				}
			break;
			case 'int':
			case 'smallint':
				if (stripos($row['fname'], 'date')!==false){
					$rez['width']=100;
					$rez['xtype']="'datecolumn'";
					$rez['format']="'U'";
					$rez['record']['type']='date';
					$rez['record']['dateFormat']='U';
					if($editor){
						$rez['editor']['xtype']='"datefield"';
						$rez['editor']['format']='"U"';
					}
				}elseif ($row['fname']=='id'){
					$rez['width']=40;
					$rez['record']['type']='int';
				}else{
					$rez['width']=40;
					$rez['record']['type']='int';
					if($editor){
						$rez['editor']['xtype']='"textfield"';
						$rez['editor']['vtype']='"num"';
					}
				}
			break;
			case 'ntext':
			case 'text':
				$rez['width']=400;
				$rez['record']['type']='string';
				if($editor){
					$rez['editor']['xtype']='"textarea"';
					$rez['editor']['cls']='"bigArea"';
					$rez['editor']['listeners']='{focus:function(ta){activeArea=ta;}}';
				}
			break;
			default:
				$rez['width']=50;
				$rez['record']['type']='"string"';
				if($editor){
					$rez['editor']['xtype']='"textfield"';
				}
		}
			
		$rez['tooltip']=iconv('windows-1251','utf-8',$row['descr']);
		$rez['tooltip']="'".str_replace("'","\'",$rez['tooltip'])."'";
		if (isset($rez['editor'])){
			$rez['editor']['allowBlank']=$row['allowBlank']=='1' ? 'true' : 'false';
		}
		return $rez;
	}
	
	/**
	 * Возвращает массив с метаданными полей таблицы.
	 * 
	 * @param string $table Таблица.
	 * @param SQLDB $db Соединение с БД.
	 * @return array Массив с метаданными полей таблицы.
	 */
	function getExtJsTabFieldsMetaData($table, $db=null){
		$db=$db ? $db : SQLDBFactory::getDB();
		$db->getColumnsInfo($table);
			
		$fields=array();
		while($row=$db->fetchAssoc()){
			$fields[$row['fname']]=determineExtJsFieldMetaData($row);
		}
		return $fields;
	} 
?>