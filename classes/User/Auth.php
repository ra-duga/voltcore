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
 * Класс данных о текущем пользователе.
 * 
 * @author Костин Алексей Васильевич
 * @copyright Copyright (c) 2010, Костин Алексей Васильевич
 * @version 2.0
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @category VoltCore
 * @package VoltCoreClasses
 * @subpackage User
 */
class Auth {

	/**
	 * Данные о текущем пользователе.
	 * @var array 
	 */
	protected $user = array();
	
	/**
	 * Проверяет имеет ли текущий пользователь право на name
	 * 
	 * @param string $name Имя права
	 * @return bool Разрешено ли
	 */
	public static function may($name){
		$a = Registry::getAuth();
		return $a->checkPermission($name);
	}
	
	/**
	 * Возвращает идентификатор пользователя.
	 * 
	 * @return int Идентификатор пользователя.
	 */
	public static function getUserId(){
		$a = Registry::getAuth();
		if (isset($a->user['id'])){
			return (int)$a->user['id'];
		}
		return null;
	}
	
	/**
	 * Проверяет, авторизован ли пользователь.
	 * 
	 * @return bool Авторизован ли пользователь 
	 */
	public static function logged(){
		$id = self::getUserId();
		if ($id){
			return true;
		}
		return false;
	}
	
	/**
	 * Инициализирует данные о пользователе. 
	 */
	public function init(){
		if (isset($_SESSION['user']) && $_SESSION['user']){
			$this->user = $_SESSION['user'];
		}else{
			$this->user = array();
		}
	}
	
	/**
	 * Вход пользователя.
	 * 
	 * @param array $user Данные о пользователе.
	 */
	public function login($user){
		$_SESSION['user'] = $user;
		$this->user = $user;
	}
	
	/**
	 * Выход пользователя. 
	 */
	public function logout(){
		unset($_SESSION['user']);
		$this->user = array();
	}
	
	/**
	 * Обновляет данные о пользователе.
	 * 
	 * @param array $user Новые данные о пользователе.
	 */
	public function refreshUser($user){
		$_SESSION['user'] = $user;
		$this->user = $user;
	}
	
	/**
	 * Устанавливает права пользователя.
	 * 
	 * @param array $permissions Права пользователя
	 */
	public function setPermissions($permissions){
		$this->user['permissions'] = $permissions;
		$this->saveUser();
	}
	
	/**
	 * Добавляет право пользователю.
	 * 
	 * @param string $name Имя права
	 * @param bool $may Разрешено ли оно пользователю
	 */
	public function addPermission($name, $may = true){
		if(isset($this->user['permissions'])){
			$this->user['permissions'] = array_merge($this->user['permissions'], array($name => $may));
		}else{
			$this->user['permissions'] = array($name => $may);
		}
		$this->saveUser();
	}
	
	/**
	 * Проверяет тварь ли пользователь дрожащая или право имеет.
	 * 
	 * @param string $name Имя права
	 */
	public function checkPermission($name){
		if(isset($this->user['permissions'][$name])){
			return $this->user['permissions'][$name];
		}
		return false;
	}
	
	/**
	 * Возвращает данные о текущем пользователе.
	 * 
	 * @return array Данные о текущем пользователе.
	 */
	public function getUser(){
		return $this->user;
	}
	
	/**
	 * Сохраняет пользователя в сессию 
	 */
	private function saveUser(){
		$_SESSION['user'] = $this->user;
	}
}