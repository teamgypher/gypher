<?php
	$IP_LIMIT = 10;
	
	$dbcon = mysqli_connect("localhost", "root", "", "socialdistancing");
	
	function isVideo($url){
		$url = get_headers($url,1);
		if(is_array($url['Content-Type'])){ //In some responses Content-type is an array
			$video = $url['Content-Type'][1] == 'video/mp4';
		}else{
			$video = $url['Content-Type'] == 'video/mp4';
		}
		
		return $video;
	}
	
	function isGiphy($url){
		return strpos($url, "giphy.com/media/") !== false;
	}
	
	function badRequest($str)
	{
		echo $str;
		exit();
	}
	
	function serverError($str)
	{
		echo $str;
		exit();
	}
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (!(isset($_POST) && isset($_POST['username']) && isset($_POST['url']))) {
			badRequest("Missing Arguments");
		}
		$ip = $_SERVER['REMOTE_ADDR'];
		if (!filter_var($ip, FILTER_VALIDATE_IP)) {
			badRequest("Too many requests");
		}
		$url = $_POST['url'];
		if (!filter_var($url, FILTER_VALIDATE_URL)) {
			badRequest("Invalid URL : $url");
		}
		if (!isGiphy($url)){
			badRequest("Not from GIPHY");
		}
		if (!isVideo($url)){
			badRequest("Not an MP4");
		}
		
		$dbquery = "SELECT ip FROM gifs ORDER BY i DESC LIMIT 10;";
		$result = $dbcon->query($dbquery);
		$count = 0;
		while ($row = $result->fetch_assoc()) {
			if ($row['ip'] == $ip) {
				$count++;
			}
		}
		if ($count > $IP_LIMIT) {
			badRequest("Too many requests");
		}
		
		$dbquery = "INSERT INTO gifs VALUES (null, ?, ?, ?)";
		$dbquery = $dbcon->prepare($dbquery);
		$dbquery->bind_param("sss", $_POST['url'], $_POST['username'], $ip);
		if (!$dbquery->execute()) {
			serverError("Already in database");
		}
		echo "OK";
	} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
		$obj = new stdClass();
		$limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
		if ($limit == 0) {
			$dbquery = "SELECT url, username FROM gifs ORDER BY i DESC";
		} else {
			$dbquery = "SELECT url, username FROM gifs ORDER BY i DESC LIMIT $limit";
		}
		if (!$result = $dbcon->query($dbquery)) {
			http_response_code(500);
			echo "500 Internal Server Error";
			exit();
		}
		
		$i = 0;
		while ($row = $result->fetch_assoc()) {
			$obj->results[$i] = $row;
			$i++;
		}
		
		header("Content-Type: application/json", true);
		http_response_code(200);
		echo json_encode($obj);
	}
	
	mysqli_close($dbcon);