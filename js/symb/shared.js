function toggle(target){
	const ele = document.getElementById(target);
	if(ele){
		if(ele.style.display === "none"){
			ele.style.display = "block";
		}
		else {
			ele.style.display = "none";
		}
	}
	else{
		const divObjs = document.getElementsByTagName("div");
		for (let i = 0; i < divObjs.length; i++) {
			const divObj = divObjs[i];
			if(divObj.getAttribute("class") === target || divObj.getAttribute("className") === target){
				if(divObj.style.display === "none"){
					divObj.style.display = "block";
				}
				else {
					divObj.style.display = "none";
				}
			}
		}
	}
}

function openIndividualPopup(clientRoot, occid,clid){
	let wWidth = 900;
	if(document.getElementById('innertext')){
        wWidth = document.getElementById('innertext').offsetWidth*1.05;
    }
    else if(document.body.offsetWidth){
        wWidth = document.body.offsetWidth*0.9;
    }
    if(wWidth > 1000) {
    	wWidth = 1000;
    }
	let newWindow = window.open(clientRoot + '/collections/individual/index.php?occid=' + occid + '&clid=' + clid, 'indspec' + occid, 'scrollbars=1,toolbar=0,resizable=1,width=' + (wWidth) + ',height=700,left=20,top=20');
    if(newWindow.opener == null) {
    	newWindow.opener = self;
    }
    return false;
}

function openPopup(url){
	let wWidth = 900;
	if(document.getElementById('innertext')){
        wWidth = document.getElementById('innertext').offsetWidth*1.05;
    }
    else if(document.body.offsetWidth){
        wWidth = document.body.offsetWidth*0.9;
    }
    if(wWidth > 1000) {
    	wWidth = 1000;
    }
	let newWindow = window.open(url, 'genericPopup', 'scrollbars=1,toolbar=0,resizable=1,width=' + (wWidth) + ',height=700,left=20,top=20');
    if(newWindow.opener == null) {
    	newWindow.opener = self;
    }
    return false;
}

function parseDate(dateStr){
	const dateObj = new Date(dateStr);
	let dateTokens;
	let y = 0;
	let m = 0;
	let d = 0;
	try{
		const validformat1 = /^\d{4}-\d{1,2}-\d{1,2}$/; //Format: yyyy-mm-dd
		const validformat2 = /^\d{1,2}\/\d{1,2}\/\d{2,4}$/; //Format: mm/dd/yyyy
		const validformat3 = /^\d{1,2} \D+ \d{2,4}$/; //Format: dd mmm yyyy
		if(validformat1.test(dateStr)){
			dateTokens = dateStr.split("-");
			y = dateTokens[0];
			m = dateTokens[1];
			d = dateTokens[2];
		}
		else if(validformat2.test(dateStr)){
			dateTokens = dateStr.split("/");
			m = dateTokens[0];
			d = dateTokens[1];
			y = dateTokens[2];
			if(y.length === 2){
				y = 0;
			}
		}
		else if(validformat3.test(dateStr)){
			dateTokens = dateStr.split(" ");
			d = dateTokens[0];
			mText = dateTokens[1];
			y = dateTokens[2];
			if(y.length === 2){
				y = 0;
			}
			mText = mText.substring(0,3);
			mText = mText.toLowerCase();
			const mNames = ["jan", "feb", "mar", "apr", "may", "jun", "jul", "aug", "sep", "oct", "nov", "dec"];
			m = mNames.indexOf(mText)+1;
		}
		else if(dateObj instanceof Date){
			y = dateObj.getFullYear();
			m = dateObj.getMonth() + 1;
			d = dateObj.getDate();
		}
	}
	catch(ex){
	}
	const retArr = [];
	retArr["y"] = y.toString();
	retArr["m"] = m.toString();
	retArr["d"] = d.toString();
	return retArr;
}

function showWorking(){
	document.body.classList.add("processing");
}

function hideWorking(){
	document.body.classList.remove("processing");
}

function arrayIndexSort(obj){
	const keys = [];
	for(const key in obj){
		if(obj.hasOwnProperty(key)){
			keys.push(key);
		}
	}
	return keys;
}

function extractLast(term){
	return split( term ).pop();
}

function formatCheckDate(dateStr){
	if(dateStr !== ""){
		const dateArr = parseDate(dateStr);
		if(dateArr['y'] === 0){
			alert("Please use the following date formats: yyyy-mm-dd, mm/dd/yyyy, or dd mmm yyyy");
			return false;
		}
		else{
			if(dateArr['m'] > 12){
				alert("Month cannot be greater than 12. Note that the format should be YYYY-MM-DD");
				return false;
			}

			if(dateArr['d'] > 28){
				if(dateArr['d'] > 31
					|| (dateArr['d'] === 30 && dateArr['m'] === 2)
					|| (dateArr['d'] === 31 && (dateArr['m'] === 4 || dateArr['m'] === 6 || dateArr['m'] === 9 || dateArr['m'] === 11))){
					alert("The Day (" + dateArr['d'] + ") is invalid for that month");
					return false;
				}
			}

			let mStr = dateArr['m'];
			if(mStr.length === 1){
				mStr = "0" + mStr;
			}
			let dStr = dateArr['d'];
			if(dStr.length === 1){
				dStr = "0" + dStr;
			}
			return dateArr['y'] + "-" + mStr + "-" + dStr;
		}
	}
}

function generateRandColor(){
	let hexColor = '';
	const x = Math.round(0xffffff * Math.random()).toString(16);
	const y = (6 - x.length);
	const z = '000000';
	const z1 = z.substring(0, y);
	hexColor = z1 + x;
	return hexColor;
}

function getISOStrFromDateObj(dObj){
	const dYear = dObj.getFullYear();
	const dMonth = ((dObj.getMonth() + 1) > 9 ? (dObj.getMonth() + 1) : '0' + (dObj.getMonth() + 1));
	const dDay = (dObj.getDate() > 9 ? dObj.getDate() : '0' + dObj.getDate());

	return dYear+'-'+dMonth+'-'+dDay;
}

function hexToRgb(hex) {
	const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
	return result ? {
		r: parseInt(result[1],16),
		g: parseInt(result[2],16),
		b: parseInt(result[3],16)
	} : null;
}

function getRgbaStrFromHexOpacity(hex,opacity) {
	const rgbArr = hexToRgb(hex);
	let retStr = '';
	if(rgbArr){
		retStr = 'rgba('+rgbArr['r']+','+rgbArr['g']+','+rgbArr['b']+','+opacity+')';
	}
	return retStr;
}

function imagePostFunction(image, src) {
	const img = image.getImage();
	if(typeof window.btoa === 'function'){
		const xhr = new XMLHttpRequest();
		const dataEntries = src.split("&");
		let url;
		let params = "";
		for (let i = 0 ; i< dataEntries.length ; i++){
			if (i===0){
				url = dataEntries[i];
			}
			else{
				params = params + "&"+dataEntries[i];
			}
		}
		xhr.open('POST', url, true);
		xhr.responseType = 'arraybuffer';
		xhr.onload = function(e) {
			if (this.status === 200) {
				const uInt8Array = new Uint8Array(this.response);
				let i = uInt8Array.length;
				const binaryString = new Array(i);
				while (i--) {
					binaryString[i] = String.fromCharCode(uInt8Array[i]);
				}
				const data = binaryString.join('');
				const type = xhr.getResponseHeader('content-type');
				if (type.indexOf('image') === 0) {
					img.src = 'data:' + type + ';base64,' + window.btoa(data);
				}
			}
		};
		xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhr.send(params);
	}
	else {
		img.src = src;
	}
}

function split(val) {
	return val.split( /,\s*/ );
}
