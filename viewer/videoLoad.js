let background;
let viewport;
let viewportIndex

function updateSettings() {
	background = false;
	if (searchParams.has('viewport')) {
		viewport = searchParams.get('viewport');
		viewportIndex = ["fill", "stretch", "center"].indexOf(viewport);
		
		if (viewportIndex > -1) {
			$("#videoDiv > video").css("object-fit", ["cover", "fill", "contain"][viewportIndex]);
		}
		
		if (viewport === "center") {
			if (searchParams.has('background')) {
				background = searchParams.get("background");
				if (background === "blur") {
					$("#blurVideoDiv").removeAttr("hidden")
				} else $("html, body, #videoDiv").css("background-color", background);
			}
		}
	}
	
	if (noText) {
		$(".content").remove();
	}
}

updateSettings();

let i = 0;

function swapVideos() {
	$.getJSON(`../api/api.php?limit=${limit}&inst=${instance}`, function (object) {
		let currentSource = $(`.video${i % 2} source`);
		let nextSource = $(`.video${(i + 1) % 2} source`);
		
		let currentVideo = $(`.video${i % 2}`);
		let nextVideo = $(`.video${(i + 1) % 2}`);
		
		nextSource.attr("src", object.results[i % object.results.length].url);
		nextVideo[0].load();
		
		if (background) nextVideo[1].load();
		
		currentVideo.fadeOut(fadeDuration);
		nextVideo.fadeIn(fadeDuration);
		
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
}

window.setInterval(swapVideos, duration);
swapVideos();