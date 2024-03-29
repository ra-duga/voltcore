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
	 * Класс шаблон меню перехода на другой модуль.
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @category VoltCore
	 * @package VoltCoreClasses
	 * @subpackage TemplatesConcrete
	 */
	class ModulsTpl extends Template{
		
		/**
		 * Конструктор.
		 * 
		 * @param boolean $cache Нужно ли кэширование шаблона.
		 * @param string $dir Дирректория для кэша шаблона.
		 */
		public function __construct($cache=null, $dir=null){
			parent::__construct(VCROOT."/Templates/moduls.tpl", $cache, $dir);
			$arrFiles=getPHPFiles();
			$rightArr=array();
			foreach($arrFiles as $file){
				$rightArr[$this->getFileTitle($file)]=str_replace(SITEROOT,"",$file);
			}
			$this->arrModuls=$rightArr;
		}
		
		/**
		 * Возвращает заголовок файла.
		 * 
		 * @param string $file Путь к файлу.
		 * @return string Заголовок файла.
		 */
		private function getFileTitle($file){
			$strFile=file_get_contents($file);
			$titlePos=strpos($strFile, '$pageT=');
			if ($titlePos<0){
				return basename($file);
			}
			$endStr=substr($strFile,$titlePos+8);
			return substr($endStr,0,strpos($endStr, '"'));
		}
	}
?>