<?php
    /**
     * Cтандартный файл настроек 
     *
     * @author Костин Алексей Васильевич aka Volt(220)
     * @copyright Copyright (c) 2010, Костин Алексей Васильевич
     * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
     * @version 1.0
     * @category VoltCoreTest
     * @package VoltCoreTestFiles
     */
    
    require(dirname(__FILE__)."/ini.php");
    
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
    
    /**
     * Подключение VoltCore
     */
    require_once(SITEROOT."/../VoltCore.php");
    $vf['testMode'] = true;
    $vf['error']['errorToException'] = false;    

    $siteConf = array();
    
    
    
	

    