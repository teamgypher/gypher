<?php
	include "../megafunctions.php";
	
	if ($_SERVER['REQUEST_METHOD'] != 'POST') { // LOGIN
		returnErrorJSON(404, "not-found", "Not found");
	}
	
	if (!($data = json_decode(file_get_contents('php://input'), true))) {
		returnErrorJSON(400, "malformed-json", "Malformed JSON");
	}
	
	if (!isset($data["username"]) || !isset($data["password"]) || !isset($data["email"])) {
		returnErrorJSON(400, "missing-json-params",
			"JSON Missing mandatory parameters");
	}
	
	if (strlen($data["username"]) > 25) {
		returnErrorJSON(400, "username-too-long",
			"Username provided exceeds 25 characters limit");
	}
	
	if (strlen($data["password"]) < 8) {
		returnErrorJSON(400, "password-too-short",
			"Password provided must be 8 characters or longer");
	}
	
	if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
		returnErrorJSON(400, "email-malformed", "Email is malformed or incorrect");
	}
	
	$data["username"] = strtolower($data["username"]);
	$data["email"] = strtolower($data["email"]);
	
	$pwhash = password_hash($data["password"], PASSWORD_BCRYPT);
	
	// Database and query prep
	if (!$dbcon = mysqli_connect("localhost", "root", "", "socialdistancing"))
		returnErrorJSON(500, "database-error", "Database error");
	$dbquery = "INSERT INTO logins VALUES (?, ?, ?, NULL)";
	if (!($dbquery = $dbcon->prepare($dbquery)))
		returnErrorJSON(500, "database-error", "Database error");
	if (!($dbquery->bind_param("sss", $data["username"], $pwhash, $data["email"])))
		returnErrorJSON(500, "database-error", "Database error");
	if (!($dbquery->execute())) {
		if ($dbquery->errno == 1062)
			returnErrorJSON(400, "already-exists", "User already exists");
		else returnErrorJSON(500, "database-error", "Database error");
	}
	$dbquery->close();
	
	returnErrorJSON(201, "user-created", "Registration complete.");