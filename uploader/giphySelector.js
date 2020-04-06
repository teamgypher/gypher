var selectedGif = null;

let typingTimer;
const doneTypingInterval = 500;

let gifResults = $('#gif-results');

let field = $('#gif-search').on("keyup", function () {
	clearTimeout(typingTimer);
	selectedGif = null;
	redField(true);
	if ($(this).val()) {
		typingTimer = setTimeout(doneTyping, doneTypingInterval);
		gifResults.empty();
		for (i = 0; i < 10; i++) gifResults.append("<div class='gif-result' style='width: 150px;'></div>");
		gifResults.removeAttr("hidden");
	} else {
		gifResults.attr("hidden", "");
	}
});

let currentSubmitState = true;

function redField(red, duration = 500) {
	const submitButton = $(".submit");
	if (red === true && currentSubmitState === true) {
		submitButton.animate({
			backgroundColor: "rgba(255, 22, 39, 0.3)",
			color: "rgba(255, 255, 255, 0.0)"
		}, duration, function () {
			submitButton.attr("value", "Select a GIF...").animate({color: "rgba(255, 255, 255, 1)"}, duration);
		});
		currentSubmitState = false;
	} else if (red === false && currentSubmitState === false) {
		submitButton.animate({
			backgroundColor: "rgba(0, 0, 0, 0.3)",
			color: "rgba(255, 255, 255, 0.0)"
		}, duration, function () {
			submitButton.attr("value", "Submit").animate({color: "rgba(255, 255, 255, 1)"}, duration);
		});
		currentSubmitState = true;
	}
}

redField(true, 0);

function doneTyping() {
	let request;
	if (field.val().startsWith("https://giphy.com/gifs/"))
		request = $.get(`https://api.giphy.com/v1/gifs/${field.val().substring(field.val().lastIndexOf('-') + 1)}?api_key=z6T7uPAQUd96dUuoEEAV3B8etfOZOc5n`, null, "json");
	else if (field.val() === "random")
		request = $.get(`https://api.giphy.com/v1/gifs/random?api_key=z6T7uPAQUd96dUuoEEAV3B8etfOZOc5n`, null, "json");
	else
		request = $.get(`https://api.giphy.com/v1/gifs/search?api_key=z6T7uPAQUd96dUuoEEAV3B8etfOZOc5n&q=${field.val()}&limit=10&offset=0&rating=G&lang=en`, null, "json");
	request.done(function (obj) {
		if (obj.data.length === 0) {
			return;
		}
		gifResults.empty();
		const ps = new PerfectScrollbar('#gif-results');
		
		let data;
		if (!Array.isArray(obj.data)) data = [obj.data];
		else data = obj.data;
		
		data.forEach(function (gif) {
			let str = `<div class='gif-result' style='width: ${gif.images.fixed_height_small.width}px'><video autoplay muted loop><source src='${gif.images.fixed_height_small.mp4}' type=video/mp4></video></div>`;
			gifResults.append(str);
			ps.update();
		});
		let allResults = gifResults.children();
		allResults.each(function () {
			$(this).children("video").on("click", function () {
				$("#tick-icon").remove();
				$(".shadowed").css("filter", "").removeClass("shadowed");
				$(this).css("filter", "contrast(30%)").addClass("shadowed").parent().append("<img id='tick-icon' src=\"check.svg\" alt=\"selected\" height='64px' width='64px'>");
				selectedGif = data[$(this).parent().index(".gif-result")];
				redField(false);
				return false;
			})
		})
	});
}