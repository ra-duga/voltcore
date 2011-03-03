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
	 * Шаблон ExytJs колонок.
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @category VoltCore
	 * @package VoltCoreClasses
	 * @subpackage TemplatesConcrete
	 */

	class ExtJSColumnsTpl extends Template{

		/**
		 * Только колонки.
		 * @var int 
		 */
		const COLUMNS_ONLY=0;
		
		/**
		 * Создать запись.
		 * @var int
		 */
		const RECORD=1;

		/**
		 * Создать хранилище.
		 * @var int
		 */
		const STORE=2;

		/**
		 * Создать фильтры.
		 * @var int
		 */
		const FILTERS=3;
		
		/**
		 * Конструктор.
		 * 
		 * @param array $columns Массив колонок.
		 * @param int $tplType Включатьли запись или хранилище.
		 * 		Если указан флаг ExtJSColumnsTpl::RECORD, то будет создана запись с настройками по умолчанию.
		 * 		Если указан флаг ExtJSColumnsTpl::STORE, то будет создана запись и хранилище с настройками по умолчанию.
		 * 		Если указан флаг ExtJSColumnsTpl::COLUMNS_ONLY, то хранилище и запись созданы не будут. 
		 * 	Одноко можно определить переменные шаблона $recordName и $storeName чтобы все равно создать хранилище и запись.  
		 * @param boolean $cache Нужно ли кэширование шаблона.
		 * @param string $dir Дирректория для кэша шаблона.
		 */
		public function __construct($columns, $tplType=ExtJSColumnsTpl::COLUMNS_ONLY, $cache=null, $dir=null){
			parent::__construct(VCROOT."/Templates/ExtJS/extJsColumns.tpl", $cache, $dir);
			$this->fields=$columns;
			$this->columnsName='VC_Columns';
			if ($tplType>0){
				$this->recordName='VC_Record';
				if ($tplType>1){
					$this->storeName='VC_Store';
					$this->rootField='rows';
					$this->totalField='totalRows';
					$this->dataUrl='/admin/admin.php';
					$this->method='POST';
					$this->baseParams='{}';
					$this->storeId='VC_Store';
					if ($tplType>2){
						$this->filterName='VC_Filters';
						$this->localFilters='false';
					}
				}
			}
		}
	}
