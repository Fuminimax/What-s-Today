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
	
	$insertDataSQL = "INSERT INTO todayData (day, cat_id, hash, dayTitle, dayDetail) 
			VALUES (:day, :cat_id, :hash, :dayTitle, :dayDetail)";
	$stmt = $pdo->prepare($insertDataSQL);
	
	// 日付設定
	$day = sprintf('%s-%s-%s', date('Y'), sprintf('%02d',$argv[1]), sprintf('%02d', $argv[2]));
	var_dump($day);
	$stmt->bindParam(':day', $day, PDO::PARAM_STR);
	
	// データを設定 (マスター使いたい、UPDATEも含めて要改修)
	// 出来事
	$events = $loadXML->events->item;
	$eventNum = count($events);
	if($eventNum > 0){
		for ($i = 0; $i < $eventNum; $i++){
			// カテゴリーIDを設定
			$stmt->bindValue(':cat_id', 2, PDO::PARAM_INT);
			
			// データを' - 'で分割
			$splitData = explode(' - ', $events[$i]);
			var_dump($splitData);
			
			$stmt->bindParam(':dayTitle', $splitData[0], PDO::PARAM_STR);
			$stmt->bindParam(':dayDetail', $splitData[1], PDO::PARAM_STR);
			
			$stmt->bindValue(':hash', hash('md5', $day . $splitData[1]), PDO::PARAM_STR);
			
			var_dump($stmt->execute());
			
			$stmt->debugDumpParams();
		}
	}
	
	// 誕生日
	$birthday = $loadXML->birthday->item;
	$birthdayNum = count($birthday);
	if($birthdayNum > 0){
		for ($i = 0; $i < $birthdayNum; $i++){
			// カテゴリーIDを設定
			$stmt->bindValue(':cat_id', 3, PDO::PARAM_INT);
				
			// データを' - 'で分割
			$splitData = explode(' - ', $birthday[$i]);
			var_dump($splitData);
				
			$stmt->bindParam(':dayTitle', $splitData[0], PDO::PARAM_STR);
			$stmt->bindParam(':dayDetail', $splitData[1], PDO::PARAM_STR);
				
			$stmt->bindValue(':hash', hash('md5', $day . $splitData[1]), PDO::PARAM_STR);
				
			var_dump($stmt->execute());
				
			$stmt->debugDumpParams();
		}
	}
	
	// 忌日
	$anniversary= $loadXML->anniversary->item;
	$anniversaryNum = count($anniversary);
	if($anniversaryNum > 0){
		for ($i = 0; $i < $anniversaryNum; $i++){
			// カテゴリーIDを設定
			$stmt->bindValue(':cat_id', 4, PDO::PARAM_INT);
	
			// データを' - 'で分割
			$splitData = explode(' - ', $anniversary[$i]);
			var_dump($splitData);
	
			$stmt->bindParam(':dayTitle', $splitData[0], PDO::PARAM_STR);
			$stmt->bindParam(':dayDetail', $splitData[1], PDO::PARAM_STR);
	
			$stmt->bindValue(':hash', hash('md5', $day . $splitData[1]), PDO::PARAM_STR);
	
			var_dump($stmt->execute());
	
			$stmt->debugDumpParams();
		}
	}
	
	// 記念日
	$topic= $loadXML->topic->item;
	$topicNum = count($topic);
	if($topicNum > 0){
		for ($i = 0; $i < $topicNum; $i++){
			// カテゴリーIDを設定
			$stmt->bindValue(':cat_id', 1, PDO::PARAM_INT);
	
			$stmt->bindParam(':dayTitle', $topic[$i], PDO::PARAM_STR);
			$stmt->bindValue(':dayDetail', null, PDO::PARAM_NULL);
	
			$stmt->bindValue(':hash', hash('md5', $day . $topic[$i]), PDO::PARAM_STR);
	
			var_dump($stmt->execute());
	
			$stmt->debugDumpParams();
		}
	}
	
	