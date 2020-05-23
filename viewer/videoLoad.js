let settings = { // DEFAULT SETTINGS
	"gif": {
		"fade": 1000,
		"duration": 10000,
		"viewport": "fill",
		"background": "transparent"
	},
	"limit": 10,
	"text": {
		"size": "2em",
		"font": null
	}
};

let blur = false;
let noText = false;

function applySettings() {
	let viewportIndex = Math.min(["fill", "stretch", "center"].indexOf(settings.gif.viewport), 0);
	$("#videoDiv > video").css("object-fit", ["cover", "fill", "contain"][viewportIndex]);
	if (viewportIndex === 2) {
		if (settings.gif.background === "blur") {
			$("#blurVideoDiv").removeAttr("hidden")
			blur = true;
		} else $("html, body, #videoDiv").css("background-color", settings.gif.background);
	}
	
	noText = settings.text.size == 0;
	let textContainer = $(".content");
	if (noText) textContainer.remove();
	else {
		if (settings.text.font != null) textContainer.css("font-family", settings.text.font)
		textContainer.css("font-size", settings.text.size);
	}
}

applySettings()

$.ajax("api/viewer_settings.php", {
	success: (data, textStatus, jqXHR) => {
		settings = jqXHR.responseJSON
		applySettings()
	}
})

let i = 0;

function swapVideos() {
	$.getJSON(`api/gifs.php?limit=${settings.limit}&inst=${instance}`, function (object) {
		let currentSource = $(`.video${i % 2} source`);
		let nextSource = $(`.video${(i + 1) % 2} source`);
		
		let currentVideo = $(`.video${i % 2}`);
		let nextVideo = $(`.video${(i + 1) % 2}`);
		
		nextSource.attr("src", object.results[i % object.results.length].url);
		nextVideo[0].load();
		
		if (blur) nextVideo[1].load();
		
		currentVideo.fadeOut(settings.gif.fade);
		nextVideo.fadeIn(settings.gif.fade);
		
		if (!noText) {
			let content = $(".content");
			content.animate({left: '-150px', bottom: '-300px'}, 500, "easeInBack", function () {
				if (object.results[i % object.results.length].username !== "") {
					$("#author")[0].innerText = object.results[i % object.results.length].username;
					content.delay(100).animate({left: '0px', bottom: '0px'}, 500, "easeOutBack")
				}
			})
		}
	});
	i++;
	setTimeout(swapVideos, Math.max(settings.gif.duration, 1000));
}

swapVideos();