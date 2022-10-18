let activeImgIndex = 1;

$(document).ready(function() {
	const imgTd = getCookie("symbimgtd");
	if(imgTd !== "close") {
		toggleImageTdOn();
	}
	initImgRes();
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

function initImgRes(){
	const imgObj = document.getElementById("activeimg-" + activeImgIndex);
	if(imgObj){
		if(imgLgArr[activeImgIndex]){
			const imgRes = getCookie("symbimgres");
			if(imgRes === 'lg'){
				changeImgRes('lg');
			}
		}
		else{
			imgObj.src = imgArr[activeImgIndex];
			document.getElementById("imgresmed").checked = true;
			const imgResLgRadio = document.getElementById("imgreslg");
			imgResLgRadio.disabled = true;
			imgResLgRadio.title = "Large resolution image not available";
		}
		if(!imgArr[activeImgIndex]){
			if(imgLgArr[activeImgIndex]){
				imgObj.src = imgLgArr[activeImgIndex];
				document.getElementById("imgreslg").checked = true;
				const imgResMedRadio = document.getElementById("imgresmed");
				imgResMedRadio.disabled = true;
				imgResMedRadio.title = "Medium resolution image not available";
			}
		}
	}
}

function changeImgRes(resType){
	const imgObj = document.getElementById("activeimg-" + activeImgIndex);
	let oldSrc = imgObj.src;
	if(resType === 'lg'){
        document.cookie = "symbimgres=lg";
    	if(imgLgArr[activeImgIndex]){
    		imgObj.src = imgLgArr[activeImgIndex];
    		document.getElementById("imgreslg").checked = true;
    	}
	}
	else{
        document.cookie = "symbimgres=med";
    	if(imgArr[activeImgIndex]){
    		imgObj.src = imgArr[activeImgIndex];
    		document.getElementById("imgresmed").checked = true;
    	}
	}
	if(oldSrc.indexOf("rotate=") > -1){
		oldSrc = oldSrc.substring(0,oldSrc.indexOf('&format='));
		oldSrc = oldSrc.substring(oldSrc.indexOf('rotate=')+7);
		let currentSrc = imgObj.src;
		currentSrc = currentSrc.substring(0,currentSrc.indexOf('&format='));
		imgObj.src = currentSrc + '&rotate=' + oldSrc + '&format=jpeg';
	}
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
