<?php
	try {
		$dbcon = mysqli_connect("localhost", "root", "", "socialdistancing");
	} catch (Exception $e) {
		httpThrow(500);
	}
	
	function httpThrow($code, $response = "") {
		http_response_code($code);
		if ($response != "") echo $response;
		exit;
	}
	
	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		if (!isset($_POST['username']) || !isset($_POST['ip']) || !filter_var($_POST['ip'], FILTER_VALIDATE_IP)) {
			httpThrow(400);
		}
		$username = $_POST['username'];
		
		// Add user & ip to userlist table
		$dbquery = "INSERT INTO users VALUES (?, ?)";
		
		if (!($dbquery = $dbcon->prepare($dbquery))) httpThrow(500);
		if (!($dbquery->bind_param("ss", $username, $_POST['ip']))) httpThrow(500);
		if (!($dbquery->execute())) {
			if ($dbquery->errno == 1062)
				httpThrow(409, "User already exists");
			else httpThrow(500);
		}
		$dbquery->close();
		
		
		// Create user's namespace (table)
		$dbquery = "CREATE TABLE `$username` LIKE gifs";
		
		if (!($dbcon->query($dbquery))){
			if ($dbcon->errno == 1050)
				httpThrow(409, "User already exists");
			else httpThrow(500, "exec");
		}
		
		httpThrow(201, "Namespace created at $username");
	} else httpThrow(405, "Use POST");