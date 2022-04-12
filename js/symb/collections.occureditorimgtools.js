let activeImgIndex = 1;
let ocrFragIndex = 1;

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

function rotateiPlantImage(rotationAngle){
	const imgObj = document.getElementById("activeimg-" + activeImgIndex);
	let imgSrc = imgObj.src;
	if(imgSrc.indexOf("bisque.cyverse") > -1){
		let angle = 0;
		imgSrc = imgSrc.substring(0,imgSrc.indexOf('&format='));
		if(imgSrc.indexOf("rotate=") > -1){
			const last3 = imgSrc.substr(-3);
			if(last3 === "=90"){
				angle = 90;
			}
			else if(last3 === "-90"){
				angle = -90;
			}
			else if(last3 === "180"){
				angle = 180;
			}
			imgSrc = imgSrc.substring(0,imgSrc.indexOf('&rotate='));
		}
		angle = angle + rotationAngle;
		if(angle == -180){
			angle = 180;
		}
		else if(angle == 270){
			angle = -90;
		}
		if(angle == 0){
			imgObj.src = imgSrc + "&format=jpeg";
		}
		else{
			imgObj.src = imgSrc + "&rotate="+angle+"&format=jpeg";
		}

		const img = document.getElementById("activeimg-" + activeImgIndex);
		$(img).imagetool("option","src",imgObj.src);
		$(img).imagetool("reset");
	}
}

function ocrImage(ocrButton,imgidVar,imgCnt){
	ocrButton.disabled = true;
	document.getElementById("workingcircle-"+imgCnt).style.display = "inline";

	const imgObj = document.getElementById("activeimg-" + imgCnt);

	let xVar = 0;
	let yVar = 0;
	let wVar = 1;
	let hVar = 1;
	let ocrBestVar = 0;

	if(document.getElementById("ocrfull").checked === false){
		xVar = $(imgObj).imagetool('properties').x;
		yVar = $(imgObj).imagetool('properties').y;
		wVar = $(imgObj).imagetool('properties').w;
		hVar = $(imgObj).imagetool('properties').h;
	}
	if(document.getElementById("ocrbest").checked === true){
		ocrBestVar = 1;
	}

	$.ajax({
		type: "POST",
		url: "rpc/ocrimage.php",
		data: { imgid: imgidVar, ocrbest: ocrBestVar, x: xVar, y: yVar, w: wVar, h: hVar }
	}).done(function( msg ) {
		const rawStr = msg;
		document.getElementById("tfeditdiv-"+imgCnt).style.display = "none";
		document.getElementById("tfadddiv-"+imgCnt).style.display = "block";
		const addform = document.getElementById("ocraddform-" + imgCnt);
		addform.rawtext.innerText = rawStr;
		addform.rawtext.textContent = rawStr;
		const today = new Date();
		let dd = today.getDate();
		let mm = today.getMonth() + 1; //January is 0!
		const yyyy = today.getFullYear();
		if(dd<10) {
			dd='0'+dd;
		}
		if(mm<10) {
			mm='0'+mm;
		}
		addform.rawsource.value = "Tesseract: "+yyyy+"-"+mm+"-"+dd;
		
		document.getElementById("workingcircle-"+imgCnt).style.display = "none";
		ocrButton.disabled = false;
	});
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

function nextRawText(imgCnt,fragCnt){
	document.getElementById("tfdiv-"+imgCnt+"-"+(fragCnt-1)).style.display = "none";
	let fragObj = document.getElementById("tfdiv-" + imgCnt + "-" + fragCnt);
	if(!fragObj) {
		fragObj = document.getElementById("tfdiv-"+imgCnt+"-1");
	}
	fragObj.style.display = "block";
	ocrFragIndex = fragCnt;
	return false;
}
