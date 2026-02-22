function toggleCat(catid){
	toggle("minus-"+catid);
	toggle("plus-"+catid);
	toggle("cat-"+catid);
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
