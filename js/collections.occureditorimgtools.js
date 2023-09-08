let activeImgIndex = 1;

document.addEventListener("DOMContentLoaded", function() {
	const imgTd = getCookie("symbimgtd");
	if(imgTd !== "close") {
		toggleImageTdOn();
	}
});

function toggleImageTdOn(){
	const imgSpan = document.getElementById("imgProcOnSpan");
	if(imgSpan){
		imgSpan.style.display = "none";
		document.getElementById("imgProcOffSpan").style.display = "block";
		const imgTdObj = document.getElementById("imgtd");
		if(imgTdObj){
			document.getElementById("imgtd").style.display = "block";
			initImageTool("activeimg-1");
			document.cookie = "symbimgtd=open";
		}
	}
}

function toggleImageTdOff(){
	const imgSpan = document.getElementById("imgProcOnSpan");
	if(imgSpan){
		imgSpan.style.display = "block";
		document.getElementById("imgProcOffSpan").style.display = "none";
		const imgTdObj = document.getElementById("imgtd");
		if(imgTdObj){
			document.getElementById("imgtd").style.display = "none";
			document.cookie = "symbimgtd=close";
		}
	}
}

function initImageTool(imgId){
	const img = document.getElementById(imgId);
	if (!img.complete) {
		setTimeout(function () {
			initImageTool(imgId)
		}, 500);
	} else {
		let portWidth = 400;
		let portHeight = 400;
		const portXyCookie = getCookie("symbimgport");
		if (portXyCookie) {
			portWidth = parseInt(portXyCookie.substr(0, portXyCookie.indexOf(":")));
			portHeight = parseInt(portXyCookie.substr(portXyCookie.indexOf(":") + 1));
		}
		$(function () {
			$(img).imagetool({
				maxWidth: 6000
				, viewportWidth: portWidth
				, viewportHeight: portHeight
			});
		});
	}
}

function setPortXY(portWidth,portHeight){
	document.cookie = "symbimgport=" + portWidth + ":" + portHeight;
}

function nextLabelProcessingImage(imgCnt){
	document.getElementById("labeldiv-"+(imgCnt-1)).style.display = "none";
	let imgObj = document.getElementById("labeldiv-" + imgCnt);
	if(!imgObj){
		imgObj = document.getElementById("labeldiv-1");
		imgCnt = "1";
	}
	imgObj.style.display = "block";

	initImageTool("activeimg-"+imgCnt);
	activeImgIndex = imgCnt;

	return false;
}
