<?php
	include "megafunctions.php";
	
	if ($_SERVER['REQUEST_METHOD'] != "GET")
		returnResultJSON(404, "not-found", "Not Found");
	
	if (!isset($_GET['inst']))
		returnResultJSON(400, "missing-argument", "Missing 'inst' argument");
	
	$dbcon = connectDB();
	$dbquery = "SELECT settings FROM `logins` WHERE username = ?";
	if (!$dbquery = $dbcon->prepare($dbquery)) returnDatabaseError();
	if (!$dbquery->bind_param("s", $_GET['inst'])) returnDatabaseError();
	if (!$dbquery->execute()) returnDatabaseError();
	if (!$result = $dbquery->get_result()) returnDatabaseError();
	if ($result->num_rows == 1)
		returnJSON(200, $result->fetch_assoc()['settings']);
	else returnResultJSON(400, "unknown-instance", "Unknown instance id");