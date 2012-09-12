<?
define('PUBROOT', dirname(__FILE__)."/");

require(PUBROOT."../platform.php");
require(PUBROOT."../ini_".PLATFORM.".php");

$c = new MainController();
$c->compileResponse();
Registry::getView()->show();
