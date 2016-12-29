<?php
date_default_timezone_set('Europe/Moscow');
define(REQUEST_PARAMETER_NAME,"_xg_u");
define(HTACCESS_REPLACEMENT,"#g_");
function __autoload($className){
	$sourceDir = "src";
	$vendorDir = "vendor";
	$classmap = [
		"Snoopy" => $vendorDir."/Snoopy/Snoopy.class.php"
	];
	if(isset($classmap[$className])){
		require_once $classmap[$className];
		return true;
	}
	$file = str_replace('\\','/',$className);
	require_once $sourceDir.'/'.$file.'.php';
	return true;
}
?>
