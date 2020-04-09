$(".form").on("submit", function (event) {
	event.preventDefault();
	if (selectedGif == null) return false;
	let data = `username=${$('#username').val()}&url=${selectedGif.images.original_mp4.mp4}`;
	
	let submit = $(".submit");
	$.ajax("../api/api.php", {
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
		submit.attr("value", `Error: ${data}`);
		submit.animate({backgroundColor: "rgba(255,22,39,0.3)"}, 500, function(){
			$(this).delay(3000).animate({backgroundColor: "rgba(0, 0, 0, 0.3)"}, 500, function () {
				$(this).attr("value", "Submit");
			})
		})
	});
	return false;
});