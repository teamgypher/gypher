$(".form").on("submit", function (event) {
	event.preventDefault();
	let data = `username=${$('#username').val()}`;
	
	let submit = $(".submit");
	$.ajax("new_instance.php", {
		type: "POST",
		data: data
	}).then(function (data, statusText, xhr) {
		submit.attr("value", "Sent!");
		submit.animate({backgroundColor: "rgba(3, 255, 62, 0.3)"}, 500, function () {
			$(this).delay(3000).animate({backgroundColor: "rgba(0, 0, 0, 0.3)"}, 500, function () {
				$(this).attr("value", "Submit");
			})
		})
	}, function (xhr, statusText, data) {
		submit.attr("value", `Error: ${xhr.responseText}`);
		submit.animate({backgroundColor: "rgba(255,22,39,0.3)"}, 500, function () {
			$(this).delay(3000).animate({backgroundColor: "rgba(0, 0, 0, 0.3)"}, 500, function () {
				$(this).attr("value", "Submit");
			})
		})
	});
	return false;
});