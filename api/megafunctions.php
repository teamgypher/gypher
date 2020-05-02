<?php
	function returnErrorJSON($code, $error, $message) {
		http_response_code($code);
		header("Content-Type: application/json");
		$obj = new stdClass();
		$obj->error = $error;
		$obj->message = $message;
		echo json_encode($obj);
		exit;
	}
	
	function returnJSON($code, $obj) {
		header("Content-Type: application/json");
		http_response_code($code);
		echo json_encode($obj);
		exit;
	}
