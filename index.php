<?php
ob_start();
include("config.php");
$f = new \g\Fetcher();
$content =  $f->get();
Log::debug("FLUSH DATA: ".ob_get_flush());
$f->pull($content);
?>
