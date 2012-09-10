<?php
/**
 * @author Костин Алексей Васильевич
 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
 * @version 2.0
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @category VoltCoreTest
 * @package VoltCoreTestFiles
 * @subpackage Classes
 */

/**
 * Класс главного контроллера проекта.
 * 
 * @author Костин Алексей Васильевич
 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
 * @version 2.0
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @category VoltCoreTest
 * @package VoltCoreTestClasses
 * @subpackage Example
 */
class MainController extends GlobalController {
	
	/**
	 * Возвращает итог работы.
	 * 
	 * @return string итог работы 
	 */
	public function getOutput($data = null){
		Registry::getResponse()->text = 'Работает';
        Registry::getView()->setViewType('dumpData');
	}
}