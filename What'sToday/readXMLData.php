<?php
	date_default_timezone_set("Asia/Tokyo");
	
	require_once 'inc/defineData.php';
	
	global $argc;
	global $argv;
	
	$fileDateName = $argv[1] . "月" . $argv[2] . "日.xml";
	
	$loadXML = simplexml_load_file('sampleData/' . $fileDateName);
	
	var_dump($loadXML);
	
	// データベース接続
	$pdo = new PDO( PDO_DSN, DATABASE_USER, DATABASE_PASSWORD );
	
	$insertDataSQL = "INSERT INTO todayData SET (day, cat_id, hash, dayTitle, dayDetail) 
			VALUES (:day, :cat_id, :hash, :dayTitle, :dayDetail)";
	$stmt = $pdo->prepare($insertDataSQL);
	