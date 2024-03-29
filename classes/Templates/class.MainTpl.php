<?php
	/**
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 	 * @version 1.1
	 * @category VoltCore
	 * @package VoltCoreFiles
	 * @subpackage Classes
	 */

	/**
	 * Класс основной шаблон страницы.
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @category VoltCore
	 * @package VoltCoreClasses
	 * @subpackage TemplatesConcrete
	 */
	class MainTpl extends Template{
		
		/**
		 * Конструктор.
		 * 
		 * @param boolean $cache Нужно ли кэширование шаблона.
		 * @param string $dir Дирректория для кэша шаблона.
		 */
		public function __construct($cache=null, $dir=null){
			parent::__construct(VCROOT."/Templates/main.tpl",$cache, $dir);
			$this->css=new CssTpl();
			$this->js=new JsTpl();
			$this->css->setDefault();
			$this->js->setDefault();
			$this->baseUrl = defined("URLROOT") ? "<base href='".URLROOT."' />" : "";
			$this->pageTitle="Главная страница";
			$this->body="Привет, кто-бы-ты-ни-был!";
		}
		
		/**
		 * Устанавливает базовый адрес модуля.
		 * 
		 * @param string $base Базовый адрес модуля.
		 */
		public function setBase($base){
			$this->baseUrl="<base href='$base' />";
		}
	}