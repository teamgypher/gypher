$(".form").on("submit", function (event) {
	event.preventDefault();
	$.post("../api/api.php", $(this).serialize(), function (data) {
		let submit = $(".submit");
		if (data === "OK") {
			submit.attr("value", "Sent!");
			submit.css("background-color", "rgba(3, 255, 62, 0.3)");
		} else {
			submit.attr("value", `Error: ${data}`);
			submit.css("background-color", "rgba(255,22,39,0.3)");
		}
	});
	return false;
})