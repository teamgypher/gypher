<?php
	include "megafunctions.php";
	
	if ($_SERVER['REQUEST_METHOD'] != "GET")
		returnResultJSON(404, "not-found", "Not Found");
	
	if (!isset($_GET['inst']))
		returnResultJSON(400, "missing-argument", "Missing 'inst' argument");
	
	$dbcon = connectDB();
	$dbquery = "SELECT settings FROM `logins` WHERE username = ?";
	$dbquery = $dbcon->prepare($dbquery);
	$dbquery->bind_param("s", $_GET['inst']);
	$dbquery->execute();
	$result = $dbquery->get_result();
	echo $result->num_rows;
	if ($result->num_rows == 1)
		returnJSON(200, $result->fetch_assoc()['settings']);
	else returnResultJSON(400, "unknown-instance", "Unknown instance id");