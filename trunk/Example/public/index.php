<?
define('PATHPUB', dirname(__FILE__)."/");

require(PATHPUB."../platform.php");
require(PATHPUB."../ini_".PLATFORM.".php");

$c = new MainController();
$c->compileResponse();
Registry::getView()->show();
