<?php
	function returnResultJSON($code, $error, $message) {
		http_response_code($code);
		header("Content-Type: application/json");
		$obj = new stdClass();
		$obj->result = $error;
		$obj->message = $message;
		echo json_encode($obj);
		exit;
	}
	
	function returnOBJ($code, $obj) {
		header("Content-Type: application/json");
		http_response_code($code);
		echo json_encode($obj);
		exit;
	}
	
	function returnJSON($code, $JSONstr) {
		header("Content-Type: application/json");
		http_response_code($code);
		echo $JSONstr;
		exit;
	}
	
	function connectDB() {
		if (!$dbcon = mysqli_connect("localhost", "root", "", "socialdistancing"))
			returnResultJSON(500, "database-error", "Database error");
		return $dbcon;
	}
	
	function returnDatabaseError() {
		returnResultJSON(500, "database-error", "Database error");
	}
