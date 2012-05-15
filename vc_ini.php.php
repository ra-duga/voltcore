<?php
    /**
     * VoltCore - Файл настроек
     *
     * @author Костин Алексей Васильевич aka Volt(220)
     * @copyright Copyright (c) 2010, Костин Алексей Васильевич
     * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
     * @version 2.0
     * @category VoltCore
     * @package VoltCoreFiles
     */

    //Определение параметров работы с базой данных
    $vf["db"]["subd"]="mssql";                 // Тип sql-сервера. (mssql, mysql)
    $vf["db"]["host"]="localhost";             // Адрес сервера
    $vf["db"]["login"]="sa";                // Логин
    $vf["db"]["pass"]="";                    // Пароль
    $vf["db"]["base"]="";                    // Основная БД
    $vf["db"]["base2"]="test";                // Побочная БД
    $vf["db"]["needEnc"]=false;                // Нужно ли производить перекодирование между БД и сайтом
    $vf["db"]["encDB"]="windows-1251";        // Кодировка БД
    $vf["db"]["encFile"]="utf-8";            // Кодировка страниц сайта

    $vf["db"]["sqlShow"]=false;                // Выводить ли запросы на экран
    $vf["db"]["sqlLog"]=false;                // Логировать ли запросы

    $vf["db"]["checkUnion"]=true;            // Проверять ли на наличие union
    $vf["db"]["checkDoubleQuery"]=true;        // Проверять ли на наличие присоединенных запросов

    //Стандартные файлы логов
    $vf["log"]["sqlLog"]=LOGDIR.LOG_PREFIX."sql.log";            // Файл SQL логов
    $vf["log"]["log"]=LOGDIR.LOG_PREFIX."runtime.log";            // Файл сообщений(ошибок) возникших в ходе выполнения программы
    $vf["log"]["mailLog"]=LOGDIR.LOG_PREFIX."mail.log";            // Файл сообщений(ошибок) возникших при работе с почтой
    $vf["log"]["Exception"]=LOGDIR.LOG_PREFIX."exceptions.log";    // Файл сообщений(ошибок) о возникших исключениях
    $vf["log"]["VoltException"]=LOGDIR.LOG_PREFIX."exceptions.log";    // Файл сообщений(ошибок) о возникших VoltException исключениях
    $vf["log"]["FormatException"]=LOGDIR.LOG_PREFIX."exceptions.log";    // Файл сообщений(ошибок) о возникших FormatException исключениях
    $vf["log"]["TestException"]=LOGDIR.LOG_PREFIX."exceptions.log";    // Файл сообщений(ошибок) о возникших TestException исключениях
    $vf["log"]["MethodNotExistsException"]=LOGDIR.LOG_PREFIX."exceptions.log";    // Файл сообщений(ошибок) о возникших MethodNotExistsException исключениях
    $vf["log"]["SqlException"]=LOGDIR.LOG_PREFIX."sql.log";    // Файл сообщений(ошибок) о возникших SqlException исключениях
    $vf["log"]["debug"]=LOGDIR.LOG_PREFIX."debug.log";            // Файл отладочной информации
    $vf["log"]["var"]=LOGDIR.LOG_PREFIX."/var.log";                        // Файл с залогированными переменными 
    
    //Настройки исключений
    $vf["exc"]["excLog"]=true;                // Логировать ли исключения
    $vf["exc"]["SqlException"]=true;        // Логировать ли SqlException
    $vf["exc"]["FormatException"]=true;        // Логировать ли FormatException
    $vf["exc"]["TestException"]=false;        // Логировать ли TestException
    $vf["exc"]["MethodNotExistsException"]=true;        // Логировать ли MethodNotExistsException
    $vf["exc"]["VoltException"]=true;        // Логировать ли VoltException
    
    //Стандартные дирректории
    $vf["dir"]["js"]=DOCROOT."/js";          // Дирректория с javascript файлами
    $vf["dir"]["css"]=DOCROOT."/css";        // Дирректория с файлами стилей
    $vf["dir"]["php"]=DOCROOT."/modules";    // Дирректория с php файлами
    $vf["dir"]["tpls"]=DOCROOT."/templates"; // Дирректория с файлами шаблонов
    $vf["dir"]["cache"]=DOCROOT."/cache";    // Дирректория для кэша
    
    $vf['dir']['classes'][]=VCROOT."/classes";    // Массив дирректорий из которых автолоад должен составить список классов.
    
    $vf["tpl"]["needCache"]=false;           // Кшировать ли шаблоны

    //Настройки безопасности.
    $vf['security']['userRights']='1';       //Стратегия прав пользователей по умолчанию (0 - запрещать все, 1 - все разрешать)
    
    $vf['gettext']=false;                    //Использовать ли gettext.
    
    $vf["cache"]["defType"]="file";          // Куда кэшировать по умолчанию. (file)
    
    $vf["test"]["db"]="VoltCore_Test";       //Имя базы данных для самотестирования.