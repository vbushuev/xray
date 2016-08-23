<?php
function __autoload($className){
	$sourceDir = "/src";
	$vendorDir = "/vendor";
	$classmap = [
	    "Snooper" => "src/Snooper.php",
	    "Snoopy" => "src/Snoopy/Snoopy.class.php",
	];
	if(isset($classmap[$className])){
		require_once $classmap[$className];
		return true;
	}
	$file = str_replace('\\','/',$className);
	require_once 'src/'.$file.'.php';
	return true;
}
?>
