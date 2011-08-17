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
	 * Класс шаблон таблицы.
	 * 
	 * Клас реализует возможности работы с нативными шаблонами
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @category VoltCore
	 * @package VoltCoreClasses
	 * @subpackage TemplatesConcrete
	 */
	class TableTpl extends Template{
		
		/**
		 * Конструктор.
		 * 
		 * @param mixed $res Данные для таблицы. Результат запроса к БД или двумерный массив.
		 * @param boolean $cache Нужно ли кэширование шаблона.
		 * @param string $dir Дирректория для кэша шаблона.
		 */
		public function __construct($res, $cache=null, $dir=null){
			parent::__construct(VCROOT."/Templates/table.tpl", $cache, $dir);
			if (is_array($res) || is_object($res)){
				$this->data=$res;
			}
			else{
				$this->setData($res);
			}
			$this->title="";
			$this->class="";
			$this->tabId="";
			$this->footData=array();
		}
		
		/**
		 * Вытаскивание данных из результата запроса.
		 * 
		 * @param resource $res Результат запроса.
		 */
		public function setData($res){
			$db=SQLDBFactory::getDB();
			$this->data=$db->fetchTable($res);
		}
		
		/**
		 * Высчитывает сумму в столюцах заданных маской.
		 * 
		 * @param int $sumMask Маска суммирования. (1 - первый столбец, 2 - второй, 4 - третий, 8 - четвертый и т.д.)
		 */
		public function sumRows($sumMask){
			$masSum=array();
			$arr=$this->data;
			$firstEl=reset($arr);
			$numColumns=$firstEl ? count($firstEl) : 0;
			if ($numColumns>0){
				$masSum=array_fill(0,$numColumns, "");
			}
			
			
			foreach ($this->data as $row){
				$curColumn=0;
				$curMask=1;
				foreach ($row as $cell){
					if ($curMask & $sumMask){
						$masSum[$curColumn]+=$cell;
         			}
         			$curMask <<=1;
         			$curColumn++;
				}
			}
			$this->footData=$masSum;
		}
		
		public function __toString(){
			$arrData=$this->data;
			foreach($arrData as $key=>$arr){
				if (!$arr) {
					unset($arrData[$key]);
				}
			}
			$this->data=$arrData;
			return parent::__toString();
		}
	}



?>