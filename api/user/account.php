<?php
	include "../megafunctions.php";
	
	session_name("connectsid");
	session_start();
	
	if ($_SERVER['REQUEST_METHOD'] == "GET") {
		$response = (object)[];
		if (!isset($_SESSION['username']) || !$_SESSION['logged'] || $_SESSION['username'] == null) {
			$response->logged = false;
		} else {
			$dbcon = connectDB();
			$dbquery = "SELECT settings FROM `logins` WHERE username = ?";
			if (!$dbquery = $dbcon->prepare($dbquery)) returnDatabaseError();
			if (!$dbquery->bind_param("s", $_SESSION['username'])) returnDatabaseError();
			if (!$dbquery->execute()) returnDatabaseError();
			$settings = json_decode($dbquery->get_result()->fetch_assoc()['settings']);
			if ($settings == null) returnDatabaseError();
			
			$response = (object)array(
				"session" => array(
					"username" => $_SESSION['username']
				),
				"logged" => true,
				"links" => array(
					"submit-form" => "https://gif.defvs.dev/{$_SESSION['username']}/",
					"viewer-url" => "https://gif.defvs.dev/{$_SESSION['username']}/viewer/"
				),
				"viewer-settings" => $settings
			);
		}
		returnOBJ(200, $response);
	} else if ($_SERVER['REQUEST_METHOD'] == "PATCH") {
		if ($_SERVER["CONTENT_TYPE"] != "application/json")
			returnResultJSON(400, "invalid-data", "Data should be a JSON.");
		if (!($data = json_decode(file_get_contents('php://input'), false)))
			returnResultJSON(400, "malformed-json", "Malformed JSON");
		if (!(isset($data->session->username) || isset($data->{"viewer-settings"})))
			returnResultJSON(400, "missing-editable-parameters", "JSON missing parameters or the ones provided are not editable.");
		
		$dbcon = connectDB();
		if (isset($data->session->username)) {
			$dbquery = "UPDATE `logins` SET username = ? WHERE username = {$_SESSION['username']}";
			if(!$dbquery = $dbcon->prepare($dbquery)) returnDatabaseError();
			if(!$dbquery->bind_param("s", $data->session->username)) returnDatabaseError();
			if(!$dbquery->execute()) returnDatabaseError();
		}
		if (isset($data->{"viewer-settings"})) {
			$dbquery = "UPDATE `logins` SET settings = ? WHERE username = {$_SESSION['username']}";
			if(!$dbquery = $dbcon->prepare($dbquery)) returnDatabaseError();
			if(!$dbquery->bind_param("s", $data->{"viewer-settings"})) returnDatabaseError();
			if(!$dbquery->execute()) returnDatabaseError();
		}
		
	} else if ($_SERVER['REQUEST_METHOD'] == "POST")
		returnResultJSON(404, "not-found", "Please use the PATCH method");
	else returnResultJSON(404, "not-found", "Not Found");