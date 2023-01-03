const imageArr = [];
const imgCnt = 0;

document.addEventListener("DOMContentLoaded", function() {
	$('#desctabs').tabs();
	$("#desctabs").show();

});

function toggleLinks(target){
	const ele = document.getElementById(target);
	if(ele){
		if(ele.style.display === "none"){
			ele.style.display="block";
		}
		else {
			ele.style.display="none";
		}
	}
	$('html,body').animate({scrollTop:$("#"+target).offset().top}, 500);
}

function toggleMap(mapObj){
	const roi = mapObj.value;
	const mapObjs = getElementByTagName("div");
	for(let x = 0;x<mapObjs.length;x++){
		const mObj = mapObjs[x];
		if(mObj.classname === "mapdiv"){
			if(mObj == mapObj){
				mObj.style.display = "block";
			}
			else{
				mObj.style.display = "none";
			}
		}
	}
}

function toggleImgInfo(target, anchorObj){
	const divs = document.getElementsByTagName("div");
	for(let x = 0;x<divs.length;x++){
		const d = divs[x];
		if(d.getAttribute("class") === "imgpopup" || d.getAttribute("className") === "imgpopup"){
			d.style.display = "none";
		}
	}

	const obj = document.getElementById(target);
	const pos = findPos(anchorObj);
	let posLeft = pos[0];
	if(posLeft > 550){
		posLeft = 550;
	}
	obj.style.left = posLeft;
	obj.style.top = pos[1];
	if(obj.style.display === "block"){
		obj.style.display="none";
	}
	else {
		obj.style.display="block";
	}
	const targetStr = "document.getElementById('" + target + "').style.display='none'";
	setTimeout(targetStr, 10000);
}

function findPos(obj){
	let curleft = 0;
	let curtop = 0;
	curleft = obj.offsetLeft;
	curtop = obj.offsetTop;
	return [curleft,curtop];
}

function expandExtraImages(){
	const display = document.getElementById("img-div").style.display;
	if(!display || display === 'none'){
		document.getElementById("img-div").style.display = "block";
	}
	if(display === 'block'){
		document.getElementById("img-div").style.display = "none";
	}
}

function openMapPopup(taxonVar,clustering){
	const url = '../spatial/index.php?starr={"usethes":true,"taxontype":"1","taxa":"' + taxonVar.replaceAll("'",'%squot;') + '"}&clusterpoints=' + (clustering ? 'true' : 'false');
	window.open(url, '_blank');
}

