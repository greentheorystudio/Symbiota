$('html').hide();
$(document).ready(function() {
    $("#tabs").tabs();
    $('html').show();
});

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
		if(inputObj.getAttribute("class") === target || inputObj.getAttribute("className") === target){
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
		if(inputObj.getAttribute("class") === target || inputObj.getAttribute("className") === target){
  			inputObj.checked = boxesChecked;
  		}
  	}
}

function verifyCollForm(f){
	let formVerified = false;
	for(let h=0; h<f.length; h++){
		if(f.elements[h].name === "db[]" && f.elements[h].checked){
			formVerified = true;
			break;
		}
		if(f.elements[h].name === "cat[]" && f.elements[h].checked){
			formVerified = true;
			break;
		}
	}
	if(!formVerified){
		alert("Please choose at least one collection!");
		return false;
	}
	else{
		for(let i=0; i<f.length; i++){
			if(f.elements[i].name === "cat[]" && f.elements[i].checked){
				const childrenEle = document.getElementById('cat-' + f.elements[i].value).children;
				for(let j=0; j<childrenEle.length; j++){
					if(childrenEle[j].tagName === "DIV"){
						const divChildren = childrenEle[j].children;
						for(let k=0; k<divChildren.length; k++){
							const divChildren2 = divChildren[k].children;
							for(let l=0; l<divChildren2.length; l++){
								if(divChildren2[l].tagName === "INPUT"){
									divChildren2[l].checked = false;
								}
							}
						}
					}
				}
			}
		}
	}
  	return formVerified;
}

function verifyOtherCatForm(){
	const pidElems = document.getElementsByName("pid[]");
	for(let i = 0; i < pidElems.length; i++){
		const pidElem = pidElems[i];
		if(pidElem.checked) return true;
	}
	const clidElems = document.getElementsByName("clid[]");
	for(let i = 0; i < clidElems.length; i++){
		const clidElem = clidElems[i];
		if(clidElem.checked) return true;
	}
   	alert("Please choose at least one search region!");
	return false;
}
