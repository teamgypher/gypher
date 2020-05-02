<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Gypher</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<link href="https://fonts.googleapis.com/css2?family=Shadows+Into+Light&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
	<link href="style.css" rel="stylesheet"/>
</head>
<body>
<div id="videoDiv">
	<video autoplay muted loop class="video0">
		<source src="" type="video/mp4">
	</video>
	<video autoplay muted loop class="video1">
		<source src="" type="video/mp4">
	</video>
	<div id="blurVideoDiv" hidden>
		<video autoplay muted loop class="video0">
			<source src="" type="video/mp4">
		</video>
		<video autoplay muted loop class="video1">
			<source src="" type="video/mp4">
		</video>
	</div>
</div>
<div class="content">
	<p>Submitted by: <span id="author"></span></p>
</div>
<script> // Consts
	const instance = "<?php echo isset($_GET['inst']) ? strtolower($_GET['inst']) : "gifs"?>";
	
	searchParams = new URLSearchParams(window.location.search);
	const duration = searchParams.has('duration') ? searchParams.get('fade') : 10000;
	const fadeDuration = searchParams.has('fade') ? searchParams.get('fade') : 1000;
	const noText = searchParams.has('notext');
	const limit = searchParams.has('limit') ? searchParams.get('limit') : 10;
</script>
<script src="videoLoad.js"></script>
</body>
</html>