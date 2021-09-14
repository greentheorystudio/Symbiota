function toggle(target){
	const ele = document.getElementById(target);
	if(ele){
		if(ele.style.display === "none"){
			if(ele.id.substring(0,5) === "minus" || ele.id.substring(0,4) === "plus"){
				ele.style.display = "inline";
	  		}
			else{
				ele.style.display = "block";
			}
  		}
	 	else {
	 		ele.style.display="none";
	 	}
	}
}

function toggleCat(catid){
	toggle("minus-"+catid);
	toggle("plus-"+catid);
	toggle("cat-"+catid);
}

function togglePid(pid){
	toggle("minus-pid-"+pid);
	toggle("plus-pid-"+pid);
	toggle("pid-"+pid);
}

function selectAll(cb){
	let boxesChecked = true;
	if(!cb.checked){
		boxesChecked = false;
	}
	const f = cb.form;
	for(let i=0; i<f.length; i++){
		if(f.elements[i].name === "db[]" || f.elements[i].name === "cat[]") {
			f.elements[i].checked = boxesChecked;
		}
	}
}

function uncheckAll(){
	if(document.getElementById('dballcb')){
		document.getElementById('dballcb').checked = false;
	}
	if(document.getElementById('dballspeccb')){
		document.getElementById('dballspeccb').checked = false;
	}
	if(document.getElementById('dballobscb')){
		document.getElementById('dballobscb').checked = false;
	}
}

function selectAllCat(cb,target){
	let boxesChecked = true;
	if(!cb.checked){
		boxesChecked = false;
		uncheckAll();
	}
	const inputObjs = document.getElementsByTagName("input");
	for (let i = 0; i < inputObjs.length; i++) {
		const inputObj = inputObjs[i];
		if(inputObj.getAttribute("class") == target || inputObj.getAttribute("className") == target){
  			inputObj.checked = boxesChecked;
  		}
  	}
}

function unselectCat(catTarget){
	const catObj = document.getElementById(catTarget);
	catObj.checked = false;
	uncheckAll();
}

function selectAllPid(cb){
	let boxesChecked = true;
	if(!cb.checked){
		boxesChecked = false;
	}
	const target = "pid-" + cb.value;
	const inputObjs = document.getElementsByTagName("input");
	for (let i = 0; i < inputObjs.length; i++) {
		const inputObj = inputObjs[i];
		if(inputObj.getAttribute("class") == target || inputObj.getAttribute("className") == target){
  			inputObj.checked = boxesChecked;
  		}
  	}
}

function openIndPU(occId,clid){
	let wWidth = 900;
	if(document.getElementById('innertext').offsetWidth){
		wWidth = document.getElementById('innertext').offsetWidth*1.05;
	}
	else if(document.body.offsetWidth){
		wWidth = document.body.offsetWidth*0.9;
	}
	if(wWidth > 1000) {
		wWidth = 1000;
	}
	let newWindow = window.open('../collections/individual/index.php?occid=' + occId, 'indspec' + occId, 'scrollbars=1,toolbar=1,resizable=1,width=' + (wWidth) + ',height=600,left=20,top=20');
	if (newWindow.opener == null) {
		newWindow.opener = self;
	}
	return false;
}

function openTaxonPopup(tid){
	let wWidth = 900;
	if(document.getElementById('innertext').offsetWidth){
		wWidth = document.getElementById('innertext').offsetWidth*1.05;
	}
	else if(document.body.offsetWidth){
		wWidth = document.body.offsetWidth*0.9;
	}
	if(wWidth > 1000) {
		wWidth = 1000;
	}
	let newWindow = window.open("../taxa/index.php?taxon=" + tid, 'taxon' + tid, 'scrollbars=1,toolbar=1,resizable=1,width=' + (wWidth) + ',height=700,left=20,top=20');
	if (newWindow.opener == null) {
		newWindow.opener = self;
	}
	return false;
}

function openImagePopup(imageId){
	let wWidth = 900;
	if(document.getElementById('innertext').offsetWidth){
		wWidth = document.getElementById('innertext').offsetWidth*1.05;
	}
	else if(document.body.offsetWidth){
		wWidth = document.body.offsetWidth*0.9;
	}
	if(wWidth > 1000) {
		wWidth = 1000;
	}
	let newWindow = window.open("imgdetails.php?imgid=" + imageId, 'image' + imageId, 'scrollbars=1,toolbar=1,resizable=1,width=' + (wWidth) + ',height=600,left=20,top=20');
	if (newWindow.opener == null) {
		newWindow.opener = self;
	}
	return false;
}

function changeFamily(taxon){
	selectedFamily = taxon;
}
