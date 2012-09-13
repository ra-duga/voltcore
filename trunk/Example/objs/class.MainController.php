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
    protected function afterCompile() {
        $r = Registry::getResponse();
        $r->set('header', '');
        $r->set('footer', '');
        $r->set('title', 'Пример использования VoltCore');
    }
}