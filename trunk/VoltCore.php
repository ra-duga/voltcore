<?php
    /**
     * VoltCore - Создание инфраструктуры.
     *
     * @author Костин Алексей Васильевич aka Volt(220)
     * @copyright Copyright (c) 2010, Костин Алексей Васильевич
     * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
     * @version 2.0
     * @category VoltCore
     * @package VoltCoreFiles
     */

    date_default_timezone_set('Europe/Moscow');
    // Настройки вывода ошибок
/*    error_reporting(E_ALL);
    ini_set("display_errors", "off");*/
    
    /**
     * Корень фреймворка.
     * 
     * @var string 
     */
    define("VCROOT",str_replace('\\','/',dirname(__FILE__)));
    
    /**
     * Подключение библиотеки кэширования
     */
    require_once(VCROOT."/libs/lib.cache.php"); 
    
    /**
     * Подключение полезных функций, которые логически не объединяются в библиотеку с говорящим названием.
     */
    require_once(VCROOT."/libs/lib.voltLib.php"); 

    /**
     * Подключение автозагрузчика
     */
    require_once(VCROOT."/autoload.php");

    if (!defined("DOCROOT")){
        /**
         * Корень модуля.
         * @var string 
         */
        define("DOCROOT",SITEROOT);
    }
    
    if (!defined("LOGDIR")){
        /**
         * Директория логов.
         * @var string 
         */
        define("LOGDIR",DOCROOT);
    }

    if (!defined("EVENTDIR")){
        /**
         * Директория логов событий.
         * @var string 
         */
        define("EVENTDIR",LOGDIR."/events");
    }
    
    if (!defined("LOG_PREFIX")){
        /**
         * Префикс файлов логов.
         * @var string 
         */
        define("LOG_PREFIX","/");
    }
    if (!defined("ERR_LOG_LEVEL")){
        /**
         * Уровень ошибок для логирования.
         * @var int
         */
        define("ERR_LOG_LEVEL",0);
    }
    
    require_once (VCROOT."/vc_ini.php");
    if (!isset($siteConf) || !is_array($siteConf)){
        $siteConf = array();
    }
    
    set_error_handler(array('Error', 'errorHandler'));
    set_exception_handler (array('Error', 'exceptionHandler')); 
    Logger::init();
