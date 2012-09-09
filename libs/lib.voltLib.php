<?php
	/**
	 * Библиотека разнообразных функций.
	 * 
	 * Данная библиотека содержит функции которые не удалось с чем-либо логически объединить 
	 * 
	 * @author Костин Алексей Васильевич aka Volt(220)
	 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
	 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
	 * @version 1.0
	 * @category VoltCore
	 * @package VoltCoreFiles
	 * @subpackage Libs
	 */

	/**
	 * Функция перекодирует строки из кодировки $from в кодировку $to.
	 * 
	 * Функция аналогична стандартной функции iconv, но позволяет перекодировать не только строки, но и массивы строк.
	 * 
	 * @param string $from Из какой кодировки 
	 * @param string $to В какую кодировку
	 * @param mixed $sbj Что перекодируем 
	 * @return mixed Результат перекодирования
	 */
	function deepIconv($from, $to, $sbj){
		if (is_array($sbj) || is_object($sbj)){
			if (is_array($sbj)){
				$rez=array();
			}else{
				$rez=new get_class($sbj);
			}
			foreach ($sbj as $k=>$val){ 
				$rezK=$k;
				if (is_string($k)){
					$rezK=iconv($from, $to, $k);
				}
				if (is_array($sbj)){
					$rez[$rezK]= deepIconv($from, $to, $val);
				}else{
					$rez->$rezK= deepIconv($from, $to, $val);
				}
			}
			return $rez;
		}else{
			return iconv($from, $to, $sbj);
		}
	}
	
	/**
	 * Выполняет ksort для подмассивов.
	 * 
	 * @param array $arr Массив для сортировки.
	 */
	function deepKsort(&$arr){
		ksort($arr);
		foreach($arr as $key=>$val){
			if (is_array($val)){
				deepKsort($arr[$key]);
			}
		}
	}
	
	/**
	 * Разбирает bb коды и заменяет их на html.
	 * 
	 * @param string $string Строка с кодами.
	 * @return string Требуемый HTML 
	 */
	function parseBBCode($string){
		$string=nl2br($string);
		while (preg_match_all('#\[(.+?)=?(.*?)\](.+?)\[/\1\]#um', $string, $matches)){
			foreach ($matches[0] as $key => $match) { 
				list($tag, $param, $innertext) = array($matches[1][$key], $matches[2][$key], $matches[3][$key]); 
            	switch ($tag) { 
                	case 'b': $replacement = "<strong>$innertext</strong>"; break; 
                	case 'i': $replacement = "<em>$innertext</em>"; break; 
                	case 's': $replacement = "<span class=\"strike\">$innertext</span>"; break; 
                	case 'u': $replacement = "<span class=\"underline\">$innertext</span>"; break; 
            	} 
            	$string = str_replace($match, $replacement, $string); 
        	} 
        }
        return $string; 
	}
	
	/**
	 * Заменяет URL адреса на ссылки.
	 *   
	 * @param string $text Текст в котором надо произвести замену.
	 * @return string Текст с сылками.
	 */
	function hrefToA($text){
		return preg_replace('#\b(aaa|aaas|acap|cap|cid|crid|data|dav|dict|dns|fax|file|ftp|go|gopher|h323|http|https|im|imap|ldap|mailto|mid|news|nfs|nntp|pop|pres|rtsp|sip|sips|snmp|tel|telnet|urn|wais|xmpp|about|aim|bolo|btc|bzr|callto|chrome|cvs|daap|ed2k|ed2kftp|feed|fish|git|gizmoproject|iax2|irc|ircs|lastfm|ldaps|magnet|mms|msnim|psyc|rsync|secondlife|skype|ssh|svn|sftp|smb|sms|soldat|steam|unreal|ut2004|view-source|vzochat|webcal|xfire|ymsgr)://[^\s\'"><]+#i', '<a href="$0">$0</a>', $text); 
	}
	
	/**
	 * Генерирует пароль.
	 * 
	 * @param int $num Количество символов в пароле. (Не более 248)
	 * @return string Новый пароль 
	 */
	function newPass($num){
		$s="qwertyuiopasdfghjklzxcvbnm";
		$s .=strtoupper($s);
		$s .="0123456789";
		$s .=$s.$s.$s;
		$s=str_shuffle($s);
		return substr($s,0,$num);
	}
	
	/**
	 * Удаляет файл, если тот существует.
	 * @param string $file Путь к файлу.
	 */
	function smartUnlink($file){
		if(file_exists($file)){
			unlink($file);
		}
	}
	
	/**
	 * Создает timestamp из даты формата d.m.Y H:i:s
	 * 
	 * @param string $date Дата формата d.m.Y H:i:s
	 * @return int timestamp соответствующий входной дате.
	 */
	function timestampFromDate($date){
		if(strlen($date)<9) throw new FormatException('Неправильный формат даты','Некорректные данные');
		$fullDate=$date;
		if (strlen($date)<12){
			$fullDate=$date." 00:00:00";
		}
		preg_match("#(\d{1,2}).(\d{1,2}).(\d{4})\s(\d{1,2}).(\d{1,2}).(\d{1,2})#", $fullDate, $matches);
		return mktime($matches[6],$matches[5],$matches[4], $matches[2], $matches[1], $matches[3]);
	}
	
	/**
	 * Подготовливает $data к печати в js шаблон.
	 * 
	 * @param mixed $data Данные для вывода.
	 * @return string Строка для вывода.
	 */
	function toJS($data){
		if (is_string($data)){
			return '"'.$data.'"';
		}
		if (is_bool($data)){
			return $data ? 'true' : 'false';
		}
		return $data;
		
	}
	
	/**
	 * Заменяет $str_pattern на $str_replacement в $string один раз.
	 *
	 * @author Oleg Butuzov
	 * @link http://ru.php.net/manual/en/function.str-replace.php#102186
	 * @param string $str_pattern Что заменять
	 * @param string $str_replacement На что заменять
	 * @param string $string Где заменять
	 * @return string Результат замены
	 */
	function str_replace_once($str_pattern, $str_replacement, $string){ 
        
        if (strpos($string, $str_pattern) !== false){ 
            $occurrence = strpos($string, $str_pattern); 
            return substr_replace($string, $str_replacement, strpos($string, $str_pattern), strlen($str_pattern)); 
        } 
        
        return $string; 
    } 
    
	/**
	 * Функция возвращает массив с именами файлов, которые соответствуют шаблону 
	 * 
	 * @param string $pattern шаблон, которому должны соответствовать имена файлов
	 * @return array массив с именами файлов 
	 */
	function getFiles($pattern){
		$rez=glob($pattern);
		return  $rez ? $rez : array();
	}
	
	/**
	 * Создает путь до файла $file. 
	 * 
	 * @param string $file Адрес файла для которого создаются дирректории. 
	 */
	function makeDirs($file){
		if (!file_exists(dirname($file))){
			if (!mkdir(dirname($file), 0777, true)){
				throw new FormatException("Не удалось создать дирректории","Некуда записывать");
			}
		}
	}    
    
    /**
	 * Очищает библиографическое описание.
	 *  
	 * @param string $bo БО для очичистки.
	 * @return string Чистое БО.
	 */
	function clearBo($bo){
		$rezBo=html_entity_decode($bo,ENT_COMPAT, 'UTF-8');
		$rezBo=strip_tags($rezBo);
		
		$rezBo=str_replace("- ","— ",$rezBo);
		$rezBo=str_replace("/ "," / ",$rezBo);
		$rezBo=str_replace(" /"," / ",$rezBo);

		$rezBo=preg_replace("(([^A-Za-zА-Яа-я0-9])-)", "$1 — ", $rezBo); 
		$rezBo=preg_replace("(\.([A-Za-zА-Яа-я]))", ". $1", $rezBo); 
		$rezBo=preg_replace("(/([^A-Za-zА-Яа-я0-9]))", " / $1", $rezBo); 
		$rezBo=preg_replace("(([^A-Za-zА-Яа-я0-9])/)", "$1 / ", $rezBo);
		$rezBo=preg_replace("(([^0-9])—([^0-9]))", "$1 — $2", $rezBo);
		$rezBo=preg_replace("(([0-9])\s?-\s?([0-9]))", "$1—$2", $rezBo);
		$rezBo=preg_replace("(([0-9])\s?—\s?([0-9]))", "$1—$2", $rezBo);
		$rezBo=preg_replace("( +)", " ", $rezBo);
		$rezBo=str_replace("/ /","//",$rezBo);
		$rezBo=trim($rezBo);
		
		return $rezBo;
			
	}
    