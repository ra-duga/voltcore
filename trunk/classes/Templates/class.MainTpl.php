<?php
	/**
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 	 * @version 1.0
	 * @package classes
	 * @subpackage Templates
	 */

	/**
	 * Класс основной шаблон страницы.
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @package classes
	 * @subpackage templates
	 */
	class MainTpl extends Template{
		
		/**
		 * Конструктор.
		 */
		public function __construct($cache=null){
			parent::__construct(VCROOT."/Templates/main.tpl",$cache);
			$this->css=new CssTpl();
			$this->js=new JsTpl();
			$this->pageTitle="Главная страница";
			$this->body="Привет, кто-бы-ты-ни-был!";
		}
	}