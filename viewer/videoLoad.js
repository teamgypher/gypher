let searchParams = new URLSearchParams(window.location.search);
const duration = searchParams.has('duration') ? searchParams.get('fade') : 10000;
const fadeDuration = searchParams.has('fade') ? searchParams.get('fade') : 1000;
const noText = searchParams.has('notext');
const limit = searchParams.has('limit') ? searchParams.get('limit') : 10;
let background = false;
if (searchParams.has('viewport')){
	const viewport = searchParams.get('viewport');
	const viewportIndex = ["fill", "stretch", "center"].indexOf(viewport);
	
	if (viewportIndex > -1){
		$("#videoDiv > video").css("object-fit", ["cover", "fill", "contain"][viewportIndex]);
	}
	
	if (viewport === "center") {
		if (searchParams.has('background')){
			background = searchParams.get("background");
			if (background === "blur"){
				$("#blurVideoDiv").removeAttr("hidden")
			} else $("html, body, #videoDiv").css("background-color", background);
		}
	}
}


let i = 0;

if (noText) {
	$(".content").remove();
}

function swapVideos() {
	$.getJSON(`../api/api.php?limit=${limit}`, function (object) {
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
			content.animate({left: '-50px', bottom: '-130px'}, 500, "easeInBack", function () {
				if (object.results[i % object.results.length].username !== "") {
					$("#author")[0].innerText = object.results[i % object.results.length].username;
					content.animate({left: '0px', bottom: '0px'}, 500, "easeOutBack")
				}
			})
		}
	});
	i++;
}

window.setInterval(swapVideos, duration);
swapVideos();