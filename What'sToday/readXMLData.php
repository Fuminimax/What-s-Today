<?php
	date_default_timezone_set("Asia/Tokyo");
	
	global $argc;
	global $argv;
	
	$fileDateName = $argv[1] . "月" . $argv[2] . "日.xml";
	
	$loadXML = simplexml_load_file($fileDateName);
	
	print($loadXML);
	
	
	
	