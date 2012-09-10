<?php
/**
 * @author Костин Алексей Васильевич
 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
 * @version 2.0
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @category VoltCore
 * @package VoltCoreFiles
 * @subpackage Classes
 */

/**
 * Класс отвечающий за представление.
 * 
 * @author Костин Алексей Васильевич
 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
 * @version 2.0
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @category VoltCore
 * @package VoltCoreClasses
 * @subpackage OtherClasses
 */
class View {
	
	/**
	 * Возможные типы представления
	 * @var array 
	 */
	public static $types = array('json', 'html', 'test');
	
	/**
	 * Данные для представления.
	 * @var array 
	 */
	protected $data;
	
	/**
	 * Тип представления
	 * @var string 
	 */
	protected $type;
	
    /**
     * Шаблон для вывода
     * @var string
     */
    protected $tpl;
    
	/**
	 * Дополнительный js
	 * @var string 
	 */
	protected $dopJs = '';

	/**
	 * Массив js для шаблона
	 * @var array 
	 */
	protected $js = array();

	/**
	 * Массив css для шаблона
	 * @var array 
	 */
	protected $css = array();
	
    /**
     * Конструктор. Устанавливает тип представления по умолчанию 
     */
    public function __construct(){
        $this->type = Registry::getConfig()->getVC('viewType');
    }
    
    /**
	 * Добавляет js в шаблон.
	 * 
	 * @param string $js Js файл для добавления
	 */
	public function addJs($js){
		if (is_array($js)){
			$this->js = array_merge($this->js, $js);
		}else{
			$this->js[] = $js;
		}
	}

	/**
	 * Добавляет css в шаблон.
	 * 
	 * @param string $css Css файл для добавления
	 */
	public function addCss($css){
		if (is_array($css)){
			$this->css = array_merge($this->css, $css);
		}else{
			$this->css[] = $css;
		}
	}
	
	/**
	 * Добавляет js код в шаблон
	 * 
	 * @param string $str Дополнительный js.
	 */
	public function addDopJs($str){
		$this->dopJs .= $str;
	}
	    
    /**
     * Устанавливает шаблон
     * 
     * @param string $tpl Путь к файлу шаблона
     */
    public function setTpl($tpl){
        $this->tpl = $tpl;
    }
    
    /**
     * Устанавливает тип представления
     * 
     * @param string $type Тип представления
     */
    public function setViewType($type){
        if (in_array($type, self::$types)){
            $this->type = $type;
        }
    }
    
	/**
	 * Выдает данные в нужном виде. 
	 */
	public function show(){
		$data = Registry::getResponse()->getResponseData();
        switch($this->type){
			case 'json':
				echo json_encode($data);
			break;
			case 'html':
				if ($this->tpl){
					$tpl = $this->tpl;
				}else{
					$tpl = Registry::getConfig()->defaultTpl;
				}
				$template = new Template($tpl);
                $template->js    = $this->js;
                $template->css   = $this->css;
                $template->dopJs = $this->dopJs;
                $template->arraySet($data);
                echo $template;
			break;
			case 'test':
                if (Registry::getConfig()->testMode){
                    var_dump($data);
                }
			break;
		}
	}
}