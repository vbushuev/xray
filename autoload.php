<?php
date_default_timezone_set('Europe/Moscow');
function __autoload($className){
	$sourceDir = "src";
	$vendorDir = "vendor";
	$classmap = [];
	if(isset($classmap[$className])){
		require_once $classmap[$className];
		return true;
	}
	$file = str_replace('\\','/',$className);
	require_once $sourceDir.'/'.$file.'.php';
	return true;
}
?>
