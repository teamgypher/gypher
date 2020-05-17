<?php
	session_name("connectsid");
	session_start();
	
	include "../megafunctions.php";
	
	
	$response = (object)[];
	
	if ($_SERVER['REQUEST_METHOD'] == "GET") {
		if (!isset($_SESSION['username']) || !$_SESSION['logged'] || $_SESSION['username'] == null) {
			$response->logged = false;
		} else {
			$dbcon = connectDB();
			$dbquery = "SELECT settings FROM `logins` WHERE username = ?";
			$dbquery = $dbcon->prepare($dbquery);
			$dbquery->bind_param("s", $_SESSION['username']);
			$dbquery->execute();
			$settings = json_decode($dbquery->get_result()->fetch_assoc()['settings']);
			
			$response = (object)array(
				"session" => array(
					"username" => $_SESSION['username']
				),
				"logged" => true,
				"links" => array(
					"submit-form" => "https://gif.defvs.dev/{$_SESSION['username']}/",
					"viewer-default" => "https://gif.defvs.dev/{$_SESSION['username']}/dviewer/",
					"viewer-custom" => "https://gif.defvs.dev/{$_SESSION['username']}/viewer/"
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
			$dbquery = $dbcon->prepare($dbquery);
			$dbquery->bind_param("s", $data->session->username);
			$dbquery->execute();
		}
		if (isset($data->{"viewer-settings"})) {
			$dbquery = "UPDATE `logins` SET settings = ? WHERE username = {$_SESSION['username']}";
			$dbquery = $dbcon->prepare($dbquery);
			$dbquery->bind_param("s", $data->{"viewer-settings"});
			$dbquery->execute();
		}
		
	} else if ($_SERVER['REQUEST_METHOD'] == "POST")
		returnResultJSON(404, "not-found", "Please use the PATCH method");
	else returnResultJSON(404, "not-found", "Not Found");