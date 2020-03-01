function toggle(target){
	const obj = document.getElementById(target);
	if(obj){
		if(obj.style.display === "none"){
			obj.style.display="block";
		}
		else {
			obj.style.display="none";
		}
	}
	else{
		const spanObjs = document.getElementsByTagName("span");
		for (let i = 0; i < spanObjs.length; i++) {
			const spanObj = spanObjs[i];
			if(spanObj.getAttribute("class") === target || spanObj.getAttribute("className") === target){
				if(spanObj.style.display === "none"){
					spanObj.style.display="inline";
				}
				else {
					spanObj.style.display="none";
				}
			}
		}

		const divObjs = document.getElementsByTagName("div");
		for (let i = 0; i < divObjs.length; i++) {
			const divObj = divObjs[i];
			if(divObj.getAttribute("class") === target || divObj.getAttribute("className") === target){
				if(divObj.style.display === "none"){
					divObj.style.display="block";
				}
				else {
					divObj.style.display="none";
				}
			}
		}
	}
}

function expandImages(){
	let divCnt = 0;
	const divObjs = document.getElementsByTagName("div");
	for (let i = 0; i < divObjs.length; i++) {
		const obj = divObjs[i];
		if(obj.getAttribute("class") === "extraimg" || obj.getAttribute("className") === "extraimg"){
			if(obj.style.display === "none"){
				obj.style.display="inline";
				divCnt++;
				if(divCnt >= 5) break;
			}
		}
	}
}

function submitAddForm(f){
	let imgUploadPath = f.elements["userfile"].value.replace(/\s/g, "");
	if(imgUploadPath === "" ){
		imgUploadPath = f.elements["filepath"].value.replace(/\s/g, "");
        if(imgUploadPath === ""){
			alert("File path must be entered");
			return false;
        }
    }
	if((imgUploadPath.indexOf(".jpg") === -1) && (imgUploadPath.indexOf(".JPG") === -1) && (imgUploadPath.indexOf(".jpeg") === -1) && (imgUploadPath.indexOf(".JPEG") === -1)){
		alert("Image file upload must be a JPG file (with a .jpg extension)");
		return false;
	}
    if(f.elements["photographeruid"].value.replace(/\s/g, "") === "" ){
        if(f.elements["photographer"].value.replace(/\s/g, "") === ""){
			alert("Please select the photographer from the pulldown or enter an override value");
			return false;
        }
    }
    if(isNumeric(f.sortsequence.value) === false){
		alert("Sort value must be a number");
		return false;
    }
    return true;
}

function isNumeric(sText){
	const ValidChars = "0123456789-.";
	let IsNumber = true;
	let Char;

	for (let i = 0; i < sText.length && IsNumber === true; i++){
	   Char = sText.charAt(i); 
		if (ValidChars.indexOf(Char) === -1){
			IsNumber = false;
			break;
      	}
   	}
	return IsNumber;
}
		
