<?php
	include "../megafunctions.php";
	
	session_name("connect.sid");
	session_start();
	
	if ($_SERVER['REQUEST_METHOD'] != 'GET') { // LOGIN
		returnResultJSON(404, "not-found", "Page not found");
		exit;
	}
	
	$_SESSION['logged'] = false;
	$_SESSION['username'] = null;
	session_destroy();
	
	returnResultJSON(200, "logged-out", "Logged out");