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

     $vf['viewType'] = 'html'; // тип представления
     $vf['testMode'] = false;  // Установлен ли режим тестирования

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
    $vf["log"]["sqlLog"]                   = LOGDIR.LOG_PREFIX."sql.log"; // Файл SQL логов
    $vf["log"]["log"]                      = LOGDIR.LOG_PREFIX."runtime.log";            // Файл сообщений(ошибок) возникших в ходе выполнения программы
    $vf["log"]["mailLog"]                  = LOGDIR.LOG_PREFIX."mail.log";            // Файл сообщений(ошибок) возникших при работе с почтой
    $vf["log"]["Exception"]                = LOGDIR.LOG_PREFIX."exceptions.log";    // Файл сообщений(ошибок) о возникших исключениях
    $vf["log"]["VoltException"]            = LOGDIR.LOG_PREFIX."exceptions.log";    // Файл сообщений(ошибок) о возникших VoltException исключениях
    $vf["log"]["SqlException"]             = LOGDIR.LOG_PREFIX."sql.log";    // Файл сообщений(ошибок) о возникших SqlException исключениях
    $vf["log"]["debug"]                    = LOGDIR.LOG_PREFIX."debug.log";            // Файл отладочной информации
    $vf["log"]["var"]                      = LOGDIR.LOG_PREFIX."var.log";                        // Файл с залогированными переменными 
    
    $vf['log']['toOutput'] = false; //Выдавать ли логи в ответ
    $vf['log']['type']     = 3; // Куда логировать
    $vf['log']['logUnknownAction'] = true; // Логировать ли запрос несуществующего дейсвия
    

    $vf['error']['toOutput'] = false; //Выдавать ли ошибки в ответ
    $vf['error']['errorToException'] = true; //Повышать ошибки до исключений
    
    //Настройки исключений
    $vf["exc"]["excLog"]=true;                // Логировать ли исключения
    $vf["exc"]["TestException"]=false;        // Логировать ли TestException
    
    //Стандартные дирректории
    $vf["dir"]["js"]    = DOCROOT."/js";          // Дирректория с javascript файлами
    $vf["dir"]["css"]   = DOCROOT."/css";         // Дирректория с файлами стилей
    $vf["dir"]["php"]   = DOCROOT."/modules";     // Дирректория с php файлами
    $vf["dir"]["tpls"]  = DOCROOT."/templates";   // Дирректория с файлами шаблонов
    $vf["dir"]["cache"] = DOCROOT."/cache";       // Дирректория для кэша
    
    $vf['dir']['classes'][]=VCROOT."/classes";    // Массив дирректорий из которых автолоад должен составить список классов.
    $vf['dir']['classes'][]=OBJDIR;
    
    $vf["tpl"]["needCache"]=false;           // Кэшировать ли шаблоны
    $vf['tpl']['global'] = VCROOT."/Templates/global.tpl"; //Шаблон обвязки
    $vf['tpl']['defaultContent'] = VCROOT."/Templates/content.tpl"; //Шаблон контента по умолчанию
    $vf['tpl']['404'] = VCROOT."/Templates/404.tpl"; // Шаблон 404 страницы
    $vf['tpl']['403'] = VCROOT."/Templates/403.tpl"; // Шаблон 403 страницы
    $vf['tpl']['error'] = VCROOT."/Templates/error.tpl"; // Шаблон страницы с ошибкой
    
    
    //Настройки безопасности.
    $vf['security']['userRights']='1';       //Стратегия прав пользователей по умолчанию (0 - запрещать все, 1 - все разрешать)
    
    $vf['gettext']=false;                    //Использовать ли gettext.
    
    $vf["cache"]["defType"]="file";          // Куда кэшировать по умолчанию. (file)
    
    $vf["test"]["db"]="VoltCore_Test";       //Имя базы данных для самотестирования.
    
    $vf['defaultController'] = 'context';