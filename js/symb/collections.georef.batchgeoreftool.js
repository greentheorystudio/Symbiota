const vStatusArr = ["reviewed - high confidence", "reviewed - medium confidence", "reviewed - low confidence",
	"not reviewed", "expert needed", "custom status 1", "custom status 2", "unable to georeference"];

$(document).ready(function() {
	$("#qvstatus").autocomplete(
		{
			source: vStatusArr
		},
		{
			delay: 0,
			minLength: 1
		}
	);
	$("#georeferenceverificationstatus").autocomplete(
		{
			source: vStatusArr
		},
		{
			delay: 0,
			minLength: 1
		}
	);
});

function verifyQueryForm(){
	document.getElementById("qworkingspan").style.display = "inline";
	return true;
}

function verifyGeorefForm(f){
	if(f.locallist.selectedIndex === -1){
		alert("At least one locality within list must be selected");
		return false;
	}
	if(f.decimallatitude.value === "" || f.decimallongitude.value === ""){
		alert("Please enter coordinates into lat/long decimal fields");
		return false;
	}
	if(!isNumeric(f.decimallatitude.value) || !isNumeric(f.decimallongitude.value)){
		alert("Decimal coordinates must be numeric values only");
		return false;
	}
	if(f.decimallatitude.value > 90 || f.decimallatitude.value < -90){
		alert("Decimal Latitude must be between -90 and 90 degrees");
		return false;
	}
	if(f.decimallongitude.value > 180 || f.decimallongitude.value < -180){
		alert("Decimal Longitude must be between -180 and 180 degrees");
		return false;
	}
	if(!isNumeric(f.minimumelevationinmeters.value) || !isNumeric(f.maximumelevationinmeters.value)){
		alert("Elevation field can only contain numeric values");
		return false;
	}
	if(!isNumeric(f.coordinateuncertaintyinmeters.value)){
		alert("Coordinate Uncertainity can only contain numeric values");
		return false;
	}
	if(f.coordinateuncertaintyinmeters.value === ""){
		return confirm('An "Error (in meters)" value is strongly recommended. Select "OK" to submit without entering an error value?');
	}
	document.getElementById("workingspan").style.display = "inline";
	return true;
}

function updateLatDec(f){
	let latDec = parseInt(f.latdeg.value);
	const latMin = parseFloat(f.latmin.value);
	const latSec = parseFloat(f.latsec.value);
	const latNS = f.latns.value;
	if(!isNumeric(latDec) || !isNumeric(latMin) || !isNumeric(latSec)){
		alert('Degree, minute, and second values must be numeric only');
		return false;
	}
	if(latDec > 90){
		alert("Latitude degrees cannot be greater than 90");
		return false;
	}
	if(latMin > 60){
		alert("The Minutes value cannot be greater than 60");
		return false;
	}
	if(latSec > 60){
		alert("The Seconds value cannot be greater than 60");
		return false;
	}
	if(latMin) {
		latDec = latDec + (f.latmin.value / 60);
	}
	if(latSec) {
		latDec = latDec + (f.latsec.value / 3600);
	}
	if(latNS === "S"){
		if(latDec > 0) latDec = -1*latDec;
	}
	else{
		if(latDec < 0) latDec = -1*latDec;
	}
	f.decimallatitude.value = Math.round(latDec*1000000)/1000000;
}

function updateLngDec(f){
	let lngDec = parseInt(f.lngdeg.value);
	const lngMin = parseFloat(f.lngmin.value);
	const lngSec = parseFloat(f.lngsec.value);
	const lngEW = f.lngew.value;
	if(!isNumeric(lngDec) || !isNumeric(lngMin) || !isNumeric(lngSec)){
		alert("Degree, minute, and second values must be numeric only");
		return false;
	}
	if(lngDec > 180){
		alert("Longitude degrees cannot be greater than 180");
		return false;
	}
	if(lngMin > 60){
		alert("The Minutes value cannot be greater than 60");
		return false;
	}
	if(lngSec > 60){
		alert("The Seconds value cannot be greater than 60");
		return false;
	}
	if(lngMin) lngDec = lngDec + (lngMin / 60);
	if(lngSec) lngDec = lngDec + (lngSec / 3600);
	if(lngEW === "W"){
		if(lngDec > 0) lngDec = -1*lngDec;
	}
	else{
		if(lngDec < 0) lngDec = -1*lngDec;
	}
	f.decimallongitude.value = Math.round(lngDec*1000000)/1000000;
}

function verifyCoordUncertainty(inputObj){
	if(!isNumeric(inputObj.value)){
		alert("Coordinate Uncertainity can only contain numeric values");
	}
}

function geoLocateLocality(){
	const selObj = document.getElementById("locallist");
	let geolocWindow;
	if (selObj.selectedIndex > -1) {
		const f = document.queryform;
		const locality = encodeURIComponent(selObj.options[selObj.selectedIndex].text);
		const country = encodeURIComponent(f.qcountry.value);
		const state = encodeURIComponent(f.qstate.value);
		const county = encodeURIComponent(f.qcounty.value);
		geolocWindow = open("geolocate.php?country=" + country + "&state=" + state + "&county=" + county + "&locality=" + locality, "geoloctool", "resizable=1,scrollbars=1,toolbar=1,width=1050,height=700,left=20,top=20");
		if (geolocWindow.opener == null) {
			geolocWindow.opener = self;
		}
	} else {
		alert("Select a locality in list to open that record set in the editor");
	}
}

function geoLocateUpdateCoord(latValue,lngValue,coordErrValue,footprintWKTValue){
	const f = document.georefform;
	f.decimallatitude.value = latValue;
	f.decimallongitude.value = lngValue;
	if(coordErrValue === "Unavailable") {
		coordErrValue = "";
	}
	f.coordinateuncertaintyinmeters.value = coordErrValue;
	if(footprintWKTValue === "Unavailable") {
		footprintWKTValue = "";
	}
	if(footprintWKTValue.length > 65000){
		footprintWKTValue = "";
		//alert("WKT footprint is too large to save in the database");
	}
	f.footprintwkt.value = footprintWKTValue;
	let baseStr = f.georeferencesources.value;
	if(baseStr){
		const baseTokens = baseStr.split(";");
		baseStr = baseTokens[0]+"; ";
	}
	f.georeferencesources.value = baseStr+"GeoLocate";
}

function geoCloneTool(){
	const selObj = document.getElementById("locallist");
	let cloneWindow;
	if (selObj.selectedIndex > -1) {
		const f = document.queryform;
		let url = "georefclone.php?";
		url = url + "locality=" + selObj.options[selObj.selectedIndex].text;
		url = url + "&country=" + f.qcountry.value;
		url = url + "&state=" + f.qstate.value;
		url = url + "&county=" + f.qcounty.value;
		url = url + "&collid=" + f.collid.value;
		cloneWindow = open(url, "geoclonetool", "resizable=1,scrollbars=1,toolbar=1,width=800,height=600,left=20,top=20");
		if (cloneWindow.opener == null) {
			cloneWindow.opener = self;
		}
	} else {
		alert("Select a locality in list to open that record set in the editor");
	}
}

function analyseLocalityStr(){
	const selObj = document.getElementById("locallist");
	if(selObj.selectedIndex > -1){
		let sourceStr = '';
		const f = document.georefform;
		const locStr = selObj.options[selObj.selectedIndex].text;

		const utmRegEx5 = /(\d{1,2})\D\s(\d{2}\s\d{2}\s\d{3})mE\s(\d{2}\s\d{2}\s\d{3})mN/;
		const llRegEx1 = /(\d{1,2})[^.\d]{1,2}(?:deg)*\s*(\d{1,2}(?:\.[0-9])*)[^.\d]{1,2}(\d{0,2}(?:\.[0-9])*)[^.\d,;]{1,3}[NS,;][.,;]*\s*(\d{1,3})[^.\d](?:deg)*\s*(\d{0,2}(?:\.[0-9]+)*)[^.\d]{1,2}(\d{0,2}(?:\.[0-9]+)*)[^.\d]{1,3}/i;
		const llRegEx2 = /(\d{1,2})[^.\d]{1,2}\s?(\d{1,2}(?:\.[0-9])*)[^.\d,;]{1,2}[NS,;][.,;]*\s*(\d{1,3})[^.\d]{1,2}\s*(\d{1,2}(?:\.[0-9])*)[^.\d]{1,2}/i;
		const llRegEx3 = /(-?\d{1,2}\.\d+)[^\d]{1,2},?\s*(-?\d{1,3}\.\d+)[^\d]/;
		const utmRegEx1 = /(\d{7})N?\s+(\d{6,7})E?\s+(\d{1,2})/;
		const utmRegEx2 = /(\d{1,2})\D{0,2}\s+(\d{7})N\s+(\d{6,7})E/;
		const utmRegEx3 = /(\d{6,7})E?\s+(\d{7})N?\s+(\d{1,2})/;
		const utmRegEx4 = /(\d{1,2})\D{0,2}\s+(\d{6,7})E\s+(\d{7})N/;
		const utmRegEx6 = /(\d{1,2})\D{0,2}\s*(\d{6})\D{0,2}\s*(\d{7})/;
		const utmRegEx7 = /(\d{1,2})\D{0,2}\s*(\d{7})\D{0,2}\s*(\d{6})/;
		let extractStr1 = utmRegEx5.exec(locStr);
		let extractStr2 = llRegEx1.exec(locStr);
		let extractStr3 = llRegEx2.exec(locStr);
		let extractStr4 = llRegEx3.exec(locStr);
		let extractStr5 = utmRegEx1.exec(locStr);
		let extractStr6 = utmRegEx2.exec(locStr);
		let extractStr7 = utmRegEx3.exec(locStr);
		let extractStr8 = utmRegEx4.exec(locStr);
		let extractStr9 = utmRegEx6.exec(locStr);
		let extractStr10 = utmRegEx7.exec(locStr);
		if(extractStr1){
			document.getElementById("utmdiv").style.display = "block";
			f.utmzone.value = extractArr[1];
			f.utmeast.value = extractArr[2].replaceAll(/\s/g,'');
			f.utmnorth.value = extractArr[3].replaceAll(/\s/g,'');
			insertUtm(f);
			sourceStr = 'UTM from label';
		}
		else if(extractStr2){
			f.latdeg.value = extractArr[1];
			f.latmin.value = extractArr[2];
			f.latsec.value = extractArr[3];
			f.lngdeg.value = extractArr[4];
			f.lngmin.value = extractArr[5];
			f.lngsec.value = extractArr[6];
			updateLatDec(f);
			updateLngDec(f);
			sourceStr = 'lat/long (DMS) from label';
		}
		else if(extractStr3){
			f.latdeg.value = extractArr[1];
			f.latmin.value = extractArr[2];
			f.latsec.value = "";
			f.lngdeg.value = extractArr[3];
			f.lngmin.value = extractArr[4];
			f.lngsec.value = "";
			updateLatDec(f);
			updateLngDec(f);
			sourceStr = 'lat/long (DMS) from label';
		}
		else if(extractStr4){
			f.decimallatitude.value = extractArr[1];
			f.decimallongitude.value = extractArr[2];
		}
		else if(extractStr5){
			document.getElementById("utmdiv").style.display = "block";
			f.utmnorth.value = extractArr[1];
			f.utmeast.value = extractArr[2];
			f.utmzone.value = extractArr[3];
			insertUtm(f);
			sourceStr = 'UTM from label';
		}
		else if(extractStr6){
			document.getElementById("utmdiv").style.display = "block";
			f.utmzone.value = extractArr[1];
			f.utmnorth.value = extractArr[2];
			f.utmeast.value = extractArr[3];
			insertUtm(f);
			sourceStr = 'UTM from label';
		}
		else if(extractStr7){
			document.getElementById("utmdiv").style.display = "block";
			f.utmeast.value = extractArr[1];
			f.utmnorth.value = extractArr[2];
			f.utmzone.value = extractArr[3];
			insertUtm(f);
			sourceStr = 'UTM from label';
		}
		else if(extractStr8){
			document.getElementById("utmdiv").style.display = "block";
			f.utmzone.value = extractArr[1];
			f.utmeast.value = extractArr[2];
			f.utmnorth.value = extractArr[3];
			insertUtm(f);
			sourceStr = 'UTM from label';
		}
		else if(extractStr9){
			document.getElementById("utmdiv").style.display = "block";
			f.utmzone.value = extractArr[1];
			f.utmeast.value = extractArr[2];
			f.utmnorth.value = extractArr[3];
			insertUtm(f);
			sourceStr = 'UTM from label';
		}
		else if(extractStr10){
			document.getElementById("utmdiv").style.display = "block";
			f.utmzone.value = extractArr[1];
			f.utmeast.value = extractArr[3];
			f.utmnorth.value = extractArr[2];
			insertUtm(f);
			sourceStr = 'UTM from label';
		}
		else{
			alert("Unable to parse UTM of DMS lat/long");
		}

		if(sourceStr){
			let baseStr = f.georeferencesources.value;
			if(baseStr){
				const baseTokens = baseStr.split(";");
				baseStr = baseTokens[0]+"; ";
			}
			f.georeferencesources.value = baseStr+sourceStr;
		}
	}
	else{
		alert("Select a locality");
	}
}

function openFirstRecSet(){
	const collId = document.georefform.collid.value;
	const selObj = document.getElementById("locallist");
	let occWindow;
	if (selObj.selectedIndex > -1) {
		const occidStr = selObj.options[selObj.selectedIndex].value;
		occWindow = open("../editor/occurrenceeditor.php?collid=" + collId + "&q_catalognumber=occid" + occidStr + "&occindex=0", "occsearch", "resizable=1,scrollbars=1,toolbar=1,width=950,height=700,left=20,top=20");
		if (occWindow.opener == null) {
			occWindow.opener = self;
		}
	} else {
		alert("Select a locality in list to open that record set in the editor");
	}
}

function insertUtm(f) {
	const zValue = f.utmzone.value.replaceAll(/^\s+|\s+$/g, "");
	const hValue = f.hemisphere.value;
	const eValue = f.utmeast.value.replaceAll(/^\s+|\s+$/g, "");
	const nValue = f.utmnorth.value.replaceAll(/^\s+|\s+$/g, "");
	if(zValue && eValue && nValue){
		if(isNumeric(eValue) && isNumeric(nValue)){
			const zNum = parseInt(zValue);
			if(isNumeric(zNum)){
				const latLngStr = utm2LatLng(zNum, eValue, nValue, f.geodeticdatum.value);
				const llArr = latLngStr.split(',');
				if(llArr){
					let latFact = 1;
					if(hValue === "Southern") {
						latFact = -1;
					}
					f.decimallatitude.value = latFact*Math.round(llArr[0]*1000000)/1000000;
					f.decimallongitude.value = Math.round(llArr[1]*1000000)/1000000;
				}
			}
			else{
				alert("Zone fields must contain numeric values only");
			}
		}
		else{
			alert("Easting and northing fields must contain numeric values only");
		}
	}
	else{
		alert("Zone, Easting, and Northing fields must not be empty");
	}
}

function utm2LatLng(zValue, eValue, nValue, datum){
	const d = 0.99960000000000004;
	let d1 = 6378137;
	let d2 = 0.00669438;
	if(datum.match(/nad\s?27/i)){
		d1 = 6378206;
		d2 = 0.006768658;
	}
	else if(datum.match(/nad\s?83/i)){
		d1 = 6378137;
		d2 = 0.00669438;
	}

	const d4 = (1 - Math.sqrt(1 - d2)) / (1 + Math.sqrt(1 - d2));
	const d15 = eValue - 500000;
	const d16 = nValue;
	const d11 = ((zValue - 1) * 6 - 180) + 3;
	const d3 = d2 / (1 - d2);
	const d10 = d16 / d;
	const d12 = d10 / (d1 * (1 - d2 / 4 - (3 * d2 * d2) / 64 - (5 * Math.pow(d2, 3)) / 256));
	const d14 = d12 + ((3 * d4) / 2 - (27 * Math.pow(d4, 3)) / 32) * Math.sin(2 * d12) + ((21 * d4 * d4) / 16 - (55 * Math.pow(d4, 4)) / 32) * Math.sin(4 * d12) + ((151 * Math.pow(d4, 3)) / 96) * Math.sin(6 * d12);
	const d13 = (d14 / Math.PI) * 180;
	const d5 = d1 / Math.sqrt(1 - d2 * Math.sin(d14) * Math.sin(d14));
	const d6 = Math.tan(d14) * Math.tan(d14);
	const d7 = d3 * Math.cos(d14) * Math.cos(d14);
	const d8 = (d1 * (1 - d2)) / Math.pow(1 - d2 * Math.sin(d14) * Math.sin(d14), 1.5);
	const d9 = d15 / (d5 * d);
	const d17 = d14 - ((d5 * Math.tan(d14)) / d8) * (((d9 * d9) / 2 - (((5 + 3 * d6 + 10 * d7) - 4 * d7 * d7 - 9 * d3) * Math.pow(d9, 4)) / 24) + (((61 + 90 * d6 + 298 * d7 + 45 * d6 * d6) - 252 * d3 - 3 * d7 * d7) * Math.pow(d9, 6)) / 720);
	const latValue = (d17 / Math.PI) * 180;
	const d18 = ((d9 - ((1 + 2 * d6 + d7) * Math.pow(d9, 3)) / 6) + (((((5 - 2 * d7) + 28 * d6) - 3 * d7 * d7) + 8 * d3 + 24 * d6 * d6) * Math.pow(d9, 5)) / 120) / Math.cos(d14);
	const lngValue = d11 + ((d18 / Math.PI) * 180);
	return latValue + "," + lngValue;

}

function updateMinElev(minFeetValue){
	const f = document.georefform;
	f.minimumelevationinmeters.value = Math.round(minFeetValue*.0305)*10;
}

function updateMaxElev(maxFeetValue){
	const f = document.georefform;
	f.maximumelevationinmeters.value = Math.round(maxFeetValue*.0305)*10;
}

function toggle(target){
	const objDiv = document.getElementById(target);
	if(objDiv){
		if(objDiv.style.display === "none"){
			objDiv.style.display = "block";
		}
		else{
			objDiv.style.display = "none";
		}
	}
	else{
		const divs = document.getElementsByTagName("div");
		for (let h = 0; h < divs.length; h++) {
			const divObj = divs[h];
			if(divObj.className == target){
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

function isNumeric(sText){
	const validChars = "0123456789-.";
	let isNumber = true;
	let charVar;

	for(let i = 0; i < sText.length && isNumber == true; i++){
   		charVar = sText.charAt(i); 
		if(validChars.indexOf(charVar) === -1){
			isNumber = false;
			break;
      	}
   	}
	return isNumber;
}
