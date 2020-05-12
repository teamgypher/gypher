<?php
	include "../megafunctions.php";
	
	session_name("connect.sid");
	session_start();
	
	if ($_SERVER['REQUEST_METHOD'] != 'POST') { // LOGIN
		returnResultJSON(404, "not-found", "Page not found");
	}
	
	if ($_SERVER["CONTENT_TYPE"] == "application/x-www-form-urlencoded") {
		if (!(isset($_POST['username']) && isset($_POST['password'])))
			returnResultJSON(400, "missing-arguments", "Username or password missing");
		$username = $_POST['username'];
		$password = $_POST['password'];
	} else if ($_SERVER["CONTENT_TYPE"] == "application/json") {
		if (!($data = json_decode(file_get_contents('php://input'), true)))
			returnResultJSON(400, "malformed-json", "Malformed JSON");
		
		if (!(isset($data["username"]) && isset($data["password"])))
			returnResultJSON(400, "missing-json-params", "JSON Missing mandatory parameters");
		
		$username = $data['username'];
		$password = $data['password'];
	}
	
	$dbcon = connectDB();
	
	$dbquery = "SELECT pwhash FROM `logins` WHERE username = ?";
	if (!$dbquery = $dbcon->prepare($dbquery)) returnResultJSON(500, "database-error", "Database error");
	if (!$dbquery->bind_param("s", $username)) returnResultJSON(500, "database-error", "Database error");
	if (!$result = $dbquery->execute()) returnResultJSON(500, "database-error", "Database error");
	
	if (!password_verify($password, $dbquery->get_result()->fetch_assoc()['pwhash'])) {
		returnResultJSON(400, "wrong-credentials", "Wrong credentials.");
	} else {
		$_SESSION['logged'] = true;
		$_SESSION['user'] = $username;
		returnResultJSON(200, "login-success", "You are now logged in.");
	}