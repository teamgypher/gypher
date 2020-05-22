<?php
	include "megafunctions.php";
	
	$LIMIT_SEARCH = 20;
	$IP_LIMIT = 10;
	
	$dbcon = connectDB();
	
	function isVideo($url) {
		$url = get_headers($url, 1);
		if (is_array($url['Content-Type'])) { //In some responses Content-type is an array
			$video = $url['Content-Type'][1] == 'video/mp4';
		} else {
			$video = $url['Content-Type'] == 'video/mp4';
		}
		
		return $video;
	}
	
	function isGiphy($url) {
		return strpos($url, "giphy.com/media/") !== false;
	}
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (!(isset($_POST)
			&& isset($_POST['username'])
			&& isset($_POST['url'])
			&& isset($_POST['inst']))) {
			returnResultJSON(400, "missing-arguments", "Missing Arguments");
		}
		
		$ip = $_SERVER['REMOTE_ADDR'];
		if (!filter_var($ip, FILTER_VALIDATE_IP)) returnResultJSON(429, "too-many-requests", "Too many requests");
		
		$url = $_POST['url'];
		if (!filter_var($url, FILTER_VALIDATE_URL)) returnResultJSON(400, "invalid-url", "Invalid URL");
		if (!isGiphy($url)) returnResultJSON(400, "not-giphy", "Not from GIPHY");
		if (!isVideo($url)) returnResultJSON(400, "not-mp4", "Not an MP4");
		
		$inst = $_POST['inst'];
		$dbquery = "SELECT ip, url FROM gifs WHERE `inst` = ? ORDER BY i DESC LIMIT $LIMIT_SEARCH";
		
		if (!$dbquery = $dbcon->prepare($dbquery)) returnDatabaseError();
		if (!$dbquery->bind_param("s", $inst)) returnDatabaseError();
		if (!$dbquery->execute()) returnDatabaseError();
		if (!$result = $dbquery->get_result()) returnDatabaseError();
		
		$count = 0;
		$present = false;
		while ($row = $result->fetch_assoc()) {
			if ($row['ip'] == $ip) {
				$count++;
			}
			if ($row['url'] == $url) {
				$present = true;
			}
		}
		
		if ($count > $IP_LIMIT) returnResultJSON(429, "too-many-requests", "Too many requests");
		if ($present) returnResultJSON(429, "already-exists", "Already in database");
		
		$dbquery = "INSERT INTO gifs VALUES (NULL, ?, ?, ?, ?)";
		if (!$dbquery = $dbcon->prepare($dbquery)) returnDatabaseError();
		if (!$dbquery->bind_param("ssss", $_POST['url'], $_POST['username'], $ip, $inst)) returnDatabaseError();
		if (!$dbquery->execute()) returnDatabaseError();
		
		returnResultJSON(200, "ok", "Gif submitted"); // OK
		
	} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
		if (!isset($_GET['inst'])) returnResultJSON(400, "missing-inst", "Missing instance name");
		
		$obj = new stdClass();
		
		$inst = $_GET['inst'];
		$limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
		if ($limit == 0) $limit = 10;
		$dbquery = "SELECT url, username FROM gifs WHERE `inst` = ? ORDER BY i DESC LIMIT ?";
		
		if (!$dbquery = $dbcon->prepare($dbquery)) returnDatabaseError();
		if (!$dbquery->bind_param("si", $inst, $limit)) returnDatabaseError();
		if (!$dbquery->execute()) returnDatabaseError();
		if (!$result = $dbquery->get_result()) returnDatabaseError();
		
		$i = 0;
		while ($row = $result->fetch_assoc()) {
			$obj->results[$i] = $row;
			$i++;
		}
		
		header("Content-Type: application/json", true);
		returnOBJ(200, $obj);
	}
	
	$dbcon->close();