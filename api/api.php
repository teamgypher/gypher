<?php
	$LIMIT_SEARCH = 20;
	$IP_LIMIT = 10;
	
	$dbcon = mysqli_connect("localhost", "root", "", "socialdistancing");
	
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
	
	function httpThrow($code = 200, $response = "") {
		http_response_code($code);
		if ($response != "") echo $response;
		exit;
	}
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (!(isset($_POST)
			&& isset($_POST['username'])
			&& isset($_POST['url'])
			&& isset($_POST['inst']))) {
			httpThrow(400, "Missing Arguments");
		}
		
		$ip = $_SERVER['REMOTE_ADDR'];
		if (!filter_var($ip, FILTER_VALIDATE_IP)) httpThrow(429, "Too many requests");
		
		$url = $_POST['url'];
		if (!filter_var($url, FILTER_VALIDATE_URL)) httpThrow(400, "Invalid URL : $url");
		if (!isGiphy($url)) httpThrow(400, "Not from GIPHY");
		if (!isVideo($url)) httpThrow(400, "Not an MP4");
		
		$inst = $dbcon->escape_string($_POST['inst']);
		$dbquery = "SELECT ip, url FROM `$inst` ORDER BY i DESC LIMIT $LIMIT_SEARCH;";
		$result = $dbcon->query($dbquery);
		
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
		
		if ($count > $IP_LIMIT) httpThrow(429, "Too many requests");
		if ($present) httpThrow(429, "Already in database");
		
		$dbquery = "INSERT INTO `$inst` VALUES (null, ?, ?, ?)";
		$dbquery = $dbcon->prepare($dbquery);
		$dbquery->bind_param("sss", $_POST['url'], $_POST['username'], $ip);
		if (!$dbquery->execute()) httpThrow(500, "Database error");
		
		httpThrow(); // OK
		
	} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
		if (!isset($_GET['inst'])) httpThrow(400, "Missing instance name");
		
		$obj = new stdClass();
		
		$inst = $dbcon->escape_string($_GET['inst']);
		$limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
		if ($limit == 0) {
			$dbquery = "SELECT url, username FROM `$inst` ORDER BY i DESC"; // Get all
		} else {
			$dbquery = "SELECT url, username FROM `$inst` ORDER BY i DESC LIMIT $limit"; // Get last $limit
		}
		if (!$result = $dbcon->query($dbquery)) {
			httpThrow(500);
		}
		
		$i = 0;
		while ($row = $result->fetch_assoc()) {
			$obj->results[$i] = $row;
			$i++;
		}
		
		header("Content-Type: application/json", true);
		httpThrow(200, json_encode($obj));
	}
	
	mysqli_close($dbcon);