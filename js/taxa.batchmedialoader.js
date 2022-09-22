function resetUploader(){
	fileData = [];
	taxaNameArr = [];
	taxaDataArr = {};
	document.getElementById('csvDataMessage').style.display = 'none';
}

function parseScinameFromFilename(fileName){
	let adjustedFileName = fileName.replace(/_/g, ' ');
	adjustedFileName = adjustedFileName.replace(/\s+/g, ' ').trim();
	const lastDotIndex = adjustedFileName.lastIndexOf('.');
	adjustedFileName = adjustedFileName.substr(0, lastDotIndex);
	const lastSpaceIndex = adjustedFileName.lastIndexOf(' ');
	if(lastSpaceIndex){
		const lastPartAfterSpace = adjustedFileName.substr(lastSpaceIndex);
		if(!isNaN(lastPartAfterSpace)){
			adjustedFileName = adjustedFileName.substr(0, lastSpaceIndex);
		}
	}
	taxaNameArr.push(adjustedFileName);
	getNewTaxaDataArr(adjustedFileName,fileName,false);
}

function getNewTaxaDataArr(value,fileName,validate){
	const fileNodeArr = document.getElementById('uploadList').childNodes;
	const http = new XMLHttpRequest();
	const url = "../rpc/getbatchimageuploadtaxadata.php";
	const params = 'taxa='+encodeURIComponent(JSON.stringify(taxaNameArr));
	//console.log(url+'?'+params);
	http.open("POST", url, true);
	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http.onreadystatechange = function() {
		if(http.readyState === 4 && http.status === 200) {
			if(http.responseText) {
				taxaDataArr = {
					...taxaDataArr,
					...JSON.parse(http.responseText)
				};
				taxaNameArr = [];
				if(taxaDataArr.hasOwnProperty(value)){
					for(let n in fileNodeArr){
						if(fileNodeArr.hasOwnProperty(n)){
							const fileNode = fileNodeArr[n];
							const nodeFileName = fileNode.getElementsByClassName('name')[0].innerHTML;
							if(nodeFileName === fileName){
								if(!validate){
									fileNode.querySelectorAll('input[name="scientificname"]')[0].value = value;
								}
								fileNode.querySelectorAll('input[name="tid"]')[0].value = taxaDataArr[value];
								fileNode.getElementsByClassName('errorUploadMessage')[0].innerHTML = '';
								fileNode.getElementsByClassName('errorUploadMessage')[0].style.display = 'none';
								fileNode.getElementsByClassName('goodUploadMessage')[0].style.display = 'block';
								fileNode.querySelectorAll('button[name="startButton"]')[0].classList.remove('disabled');
								fileNode.querySelectorAll('button[name="startButton"]')[0].disabled = false;
							}
						}
					}
					updateFileDataArrTid();
					updateDisplay();
				}
				else if(validate){
					for(let n in fileNodeArr){
						if(fileNodeArr.hasOwnProperty(n)){
							const fileNode = fileNodeArr[n];
							const nodeFileName = fileNode.getElementsByClassName('name')[0].innerHTML;
							if(nodeFileName === fileName){
								fileNode.getElementsByClassName('errorUploadMessage')[0].innerHTML = 'Scientific name not found in taxonomic thesaurus';
								fileNode.getElementsByClassName('errorUploadMessage')[0].style.display = 'block';
								fileNode.getElementsByClassName('goodUploadMessage')[0].style.display = 'none';
								fileNode.querySelectorAll('button[name="startButton"]')[0].classList.add('disabled');
							}
						}
					}
				}
			}
		}
	};
	http.send(params);
}

function updateFileDataArrTid(){
	for(let i in fileData){
		if(fileData.hasOwnProperty(i)){
			const sciname = fileData[i].scientificname;
			if(!fileData[i].hasOwnProperty('tid') || fileData[i].tid === ''){
				if(sciname){
					if(taxaDataArr.hasOwnProperty(sciname)){
						fileData[i].tid = taxaDataArr[sciname];
						fileData[i].errorMessage = '';
					}
					else{
						fileData[i].errorMessage = 'Scientific name not found in taxonomic thesaurus';
					}
				}
			}
			else{
				fileData[i].errorMessage = '';
			}
		}
	}
}

function updateDisplay(){
	const fileNodeArr = document.getElementById('uploadList').childNodes;
	for(let n in fileNodeArr){
		if(fileNodeArr.hasOwnProperty(n)){
			const dataObj = {};
			const fileNode = fileNodeArr[n];
			const fileName = fileNode.getElementsByClassName('name')[0].innerHTML;
			let imageFileData = fileData.find((obj) => obj.filename.toLowerCase() === fileName.toLowerCase());
			if(!imageFileData){
				imageFileData = fileData.find((obj) => obj.filename.toLowerCase() === fileName.substring(0, fileName.lastIndexOf('.')).toLowerCase());
			}
			if(imageFileData){
				if(imageFileData.hasOwnProperty('scientificname') && imageFileData.scientificname && !fileNode.querySelectorAll('input[name="scientificname"]')[0].value){
					fileNode.querySelectorAll('input[name="scientificname"]')[0].value = imageFileData.scientificname;
				}
				if(imageFileData.hasOwnProperty('tid') && imageFileData.tid && !fileNode.querySelectorAll('input[name="tid"]')[0].value){
					fileNode.querySelectorAll('input[name="tid"]')[0].value = imageFileData.tid;
				}
				if(imageFileData.hasOwnProperty('photographer') && imageFileData.photographer && !fileNode.querySelectorAll('input[name="photographer"]')[0].value){
					fileNode.querySelectorAll('input[name="photographer"]')[0].value = imageFileData.photographer;
					dataObj['photographer'] = imageFileData.photographer;
				}
				if(imageFileData.hasOwnProperty('caption') && imageFileData.caption && !fileNode.querySelectorAll('input[name="caption"]')[0].value){
					fileNode.querySelectorAll('input[name="caption"]')[0].value = imageFileData.caption;
					dataObj['caption'] = imageFileData.caption;
				}
				if(imageFileData.hasOwnProperty('owner') && imageFileData.owner && !fileNode.querySelectorAll('input[name="owner"]')[0].value){
					fileNode.querySelectorAll('input[name="owner"]')[0].value = imageFileData.owner;
					dataObj['owner'] = imageFileData.owner;
				}
				if(imageFileData.hasOwnProperty('sourceurl') && imageFileData.sourceurl && !fileNode.querySelectorAll('input[name="sourceurl"]')[0].value){
					fileNode.querySelectorAll('input[name="sourceurl"]')[0].value = imageFileData.sourceurl;
					dataObj['sourceurl'] = imageFileData.sourceurl;
				}
				if(imageFileData.hasOwnProperty('copyright') && imageFileData.copyright && !fileNode.querySelectorAll('input[name="copyright"]')[0].value){
					fileNode.querySelectorAll('input[name="copyright"]')[0].value = imageFileData.copyright;
					dataObj['copyright'] = imageFileData.copyright;
				}
				if(imageFileData.hasOwnProperty('locality') && imageFileData.locality && !fileNode.querySelectorAll('input[name="locality"]')[0].value){
					fileNode.querySelectorAll('input[name="locality"]')[0].value = imageFileData.locality;
					dataObj['locality'] = imageFileData.locality;
				}
				if(imageFileData.hasOwnProperty('notes') && imageFileData.notes && !fileNode.querySelectorAll('input[name="notes"]')[0].value){
					fileNode.querySelectorAll('input[name="notes"]')[0].value = imageFileData.notes;
					dataObj['notes'] = imageFileData.notes;
				}
				if(imageFileData.hasOwnProperty('errorMessage') && imageFileData.errorMessage){
					fileNode.getElementsByClassName('errorUploadMessage')[0].innerHTML = imageFileData.errorMessage;
					fileNode.getElementsByClassName('errorUploadMessage')[0].style.display = 'block';
					fileNode.getElementsByClassName('goodUploadMessage')[0].style.display = 'none';
					fileNode.querySelectorAll('button[name="startButton"]')[0].classList.add('disabled');
				}
				else{
					fileNode.getElementsByClassName('errorUploadMessage')[0].innerHTML = '';
					fileNode.getElementsByClassName('errorUploadMessage')[0].style.display = 'none';
					fileNode.getElementsByClassName('goodUploadMessage')[0].style.display = 'block';
					fileNode.querySelectorAll('button[name="startButton"]')[0].classList.remove('disabled');
				}
				if(dataObj !== {}){
					fileNode.getElementsByClassName('linkedDataDisplay')[0].innerHTML = JSON.stringify(dataObj);
					fileNode.getElementsByClassName('linkedDataMessage')[0].style.display = 'block';
				}
				else{
					fileNode.getElementsByClassName('linkedDataMessage')[0].style.display = 'none';
				}
			}
		}
	}
}

function processCsvFile(e, file){
	const reader = new FileReader();
	reader.onload = function (e) {
		const text = e.target.result;
		fileData = csvToArray(text);
		if(taxaNameArr.length > 0){
			setTaxaDataObjFromTaxaArr();
		}
		updateDisplay();
	};
	reader.readAsText(file);
}

function validateSciNameChange(value,fileName) {
	const fileNodeArr = document.getElementById('uploadList').childNodes;
	for(let n in fileNodeArr){
		if(fileNodeArr.hasOwnProperty(n)){
			const fileNode = fileNodeArr[n];
			const nodeFileName = fileNode.getElementsByClassName('name')[0].innerHTML;
			if(nodeFileName === fileName){
				fileNode.getElementsByClassName('errorUploadMessage')[0].innerHTML = 'Validating name...';
				fileNode.getElementsByClassName('errorUploadMessage')[0].style.display = 'block';
				fileNode.getElementsByClassName('goodUploadMessage')[0].style.display = 'none';
				fileNode.querySelectorAll('button[name="startButton"]')[0].classList.add('disabled');
			}
		}
	}
	taxaNameArr.push(value);
	getNewTaxaDataArr(value,fileName,true);
}

function csvToArray(str) {
	const headers = str.slice(0, str.indexOf("\n")).split(',');
	if(str.endsWith("\n")){
		str = str.substring(0, str.length - 2);
	}
	const rows = str.slice(str.indexOf("\n") + 1).split("\n");
	return rows.map(function (row) {
		if (row) {
			document.getElementById('csvDataMessage').style.display = 'inline-block';
			const values = row.split(/,(?=(?:(?:[^"]*"){2})*[^"]*$)/);
			return headers.reduce(function (object, header, index) {
				const fieldName = header.trim();
				let fieldValue = values[index].replace('\r', '');
				if(fieldValue.startsWith('"')){
					fieldValue = fieldValue.replaceAll('"','');
				}
				if (fieldName === 'scientificname' && fieldValue && !taxaNameArr.includes(fieldValue) && !taxaDataArr.hasOwnProperty(fieldValue)) {
					taxaNameArr.push(fieldValue);
				}
				object[fieldName] = fieldValue;
				return object;
			}, {});
		}
	});
}

function setTaxaDataObjFromTaxaArr(){
	const http = new XMLHttpRequest();
	const url = "../rpc/getbatchimageuploadtaxadata.php";
	const params = 'taxa='+encodeURIComponent(JSON.stringify(taxaNameArr));
	//console.log(url+'?'+params);
	http.open("POST", url, true);
	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http.onreadystatechange = function() {
		if(http.readyState === 4 && http.status === 200) {
			if(http.responseText) {
				taxaDataArr = {
					...taxaDataArr,
					...JSON.parse(http.responseText)
				};
				taxaNameArr = [];
				updateFileDataArrTid();
				updateDisplay();
			}
		}
	};
	http.send(params);
}
