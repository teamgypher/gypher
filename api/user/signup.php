<?php
	include "../megafunctions.php";
	
	session_name("connect.sid");
	session_start();
	
	if ($_SERVER['REQUEST_METHOD'] != 'POST') { // LOGIN
		returnResultJSON(404, "not-found", "Not found");
	}
	
	if (!($data = json_decode(file_get_contents('php://input'), true))) {
		returnResultJSON(400, "malformed-json", "Malformed JSON");
	}
	
	if (!isset($data["username"]) || !isset($data["password"]) || !isset($data["email"])) {
		returnResultJSON(400, "missing-json-params",
			"JSON Missing mandatory parameters");
	}
	
	if (strlen($data["username"]) > 25) {
		returnResultJSON(400, "username-too-long",
			"Username provided exceeds 25 characters limit");
	}
	
	if (strlen($data["password"]) < 8) {
		returnResultJSON(400, "password-too-short",
			"Password provided must be 8 characters or longer");
	}
	
	if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
		returnResultJSON(400, "email-malformed", "Email is malformed or incorrect");
	}
	
	$data["username"] = strtolower($data["username"]);
	$data["email"] = strtolower($data["email"]);
	
	$pwhash = password_hash($data["password"], PASSWORD_BCRYPT);
	
	// Create entry in logins database
	$dbcon = connectDB();
	$dbquery = "INSERT INTO logins VALUES (?, ?, ?, DEFAULT)";
	if (!($dbquery = $dbcon->prepare($dbquery)))
		returnDatabaseError();
	if (!($dbquery->bind_param("sss", $data["username"], $pwhash, $data["email"])))
		returnDatabaseError();
	if (!($dbquery->execute())) {
		if ($dbquery->errno == 1062)
			returnResultJSON(400, "already-exists", "User already exists");
		else returnDatabaseError();
	}
	$dbquery->close();
	
	// Create user's namespace (table)
	$escaped = $dbcon->escape_string($data["username"]);
	$dbquery = "CREATE TABLE `$escaped` LIKE gifs";
	
	if (!($dbcon->query($dbquery))) {
		if ($dbcon->errno == 1050)
			returnResultJSON(409, "duplicate-user", "User already exists");
		else returnDatabaseError();
	}
	
	returnResultJSON(201, "user-created", "Registration complete.");