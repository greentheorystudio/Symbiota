function openAssocSppAid(){
	const assocWindow = open("assocsppaid.php", "assocaid", "resizable=0,width=550,height=150,left=20,top=20");
	if(assocWindow != null){
		if (assocWindow.opener == null) {
			assocWindow.opener = self;
		}
		fieldChanged("associatedtaxa");
		assocWindow.focus();
	}
	else{
		alert("Unable to open associated species tool, which is likely due to your browser blocking popups. Please adjust your browser settings to allow popups from this website.");
	}
}

function toggleCoordDiv(){
	let coordObj = document.getElementById("coordAidDiv");
	if(coordObj.style.display === "block"){
		coordObj.style.display = "none";
	}
	else{
		document.getElementById("georefExtraDiv").style.display = "block";
		coordObj.style.display = "block";
	}
}

function toggleCsMode(modeId){
	if(modeId == 1){
		document.getElementById("editorCssLink").href = "../../css/occureditorcrowdsource.css?ver=20221204";
		document.getElementById("longtagspan").style.display = "block";
		document.getElementById("shorttagspan").style.display = "none";
	}
	else{
		document.getElementById("editorCssLink").href = "../../css/occureditor.css?ver=20240405";
		document.getElementById("longtagspan").style.display = "none";
		document.getElementById("shorttagspan").style.display = "block";
	}
}

function geoLocateLocality(){
	const f = document.fullform;
	const country = encodeURIComponent(f.country.value);
	let state = encodeURIComponent(f.stateprovince.value);
	if(!state) {
		state = "unknown";
	}
	let county = encodeURIComponent(f.county.value);
	if(!county) {
		county = "unknown";
	}
	let municipality = encodeURIComponent(f.municipality.value);
	if(!municipality) {
		municipality = "unknown";
	}
	let locality = encodeURIComponent(f.locality.value);
	if(!locality){
		locality = country+"; "+state+"; "+county+"; "+municipality;
	}
	if(f.verbatimcoordinates.value) {
		locality = locality + "; " + encodeURIComponent(f.verbatimcoordinates.value);
	}

	if(!country){
		alert("Country is blank and it is a required field for GeoLocate");
	}
	else if(!locality){
		alert("Record does not contain any verbatim locality details for GeoLocate");
	}
	else{
		let geolocWindow;
		geolocWindow = open("../georef/geolocate.php?country="+country+"&state="+state+"&county="+county+"&locality="+locality,"geoloctool","resizable=1,scrollbars=1,toolbar=1,width=1050,height=700,left=20,top=20");
		if(geolocWindow.opener == null){
			geolocWindow.opener = self;
		}
		geolocWindow.focus();
	}
}

function geoLocateUpdateCoord(latValue,lngValue,coordErrValue, footprintWKT){
	document.getElementById("georefExtraDiv").style.display = "block";

	const f = document.fullform;
	f.decimallatitude.value = latValue;
	f.decimallongitude.value = lngValue;
	f.coordinateuncertaintyinmeters.value = coordErrValue;
	if(footprintWKT.length > 0){
		if(footprintWKT === "Unavailable") {
			footprintWKT = "";
		}
		if(footprintWKT.length > 65000){
			footprintWKT = "";
		}
		f.footprintwkt.value = footprintWKT;
		fieldChanged('footprintwkt');
	}
	f.georeferencesources.value = "GeoLocate";
	f.geodeticdatum.value = "WGS84";

	verifyDecimalLatitude(f);
	fieldChanged('decimallatitude');
	verifyDecimalLongitude(f);
	fieldChanged('decimallongitude');
	verifyCoordinates(f);
	f.coordinateuncertaintyinmeters.onchange();
	f.georeferencesources.onchange();
	f.geodeticdatum.onchange();
}

function insertUtm(f) {
	const zValue = document.getElementById("utmzone").value.replaceAll(/^\s+|\s+$/g, "");
	const hValue = document.getElementById("hemisphere").value;
	const eValue = document.getElementById("utmeast").value.replaceAll(/^\s+|\s+$/g, "");
	const nValue = document.getElementById("utmnorth").value.replaceAll(/^\s+|\s+$/g, "");
	if(zValue && eValue && nValue){
		if(!isNaN(eValue) && !isNaN(nValue)){
			let vcStr = f.verbatimcoordinates.value;
			vcStr = vcStr.replaceAll(/\d{2}.*\d+E\s+\d+N[;\s]*/g, "");
			vcStr = vcStr.replaceAll(/(Northern)|(Southern)/g, "");
			vcStr = vcStr.replaceAll(/^\s+|\s+$/g, "");
			vcStr = vcStr.replaceAll(/^;|;$/g, "");
			if(vcStr !== ""){
				vcStr = vcStr + "; ";
			}
			const utmStr = zValue + " " + eValue + "E " + nValue + "N ";
			f.verbatimcoordinates.value = vcStr + utmStr;
			const zNum = parseInt(zValue);
			if(!isNaN(zNum)){
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
			fieldChanged("decimallatitude");
			fieldChanged("decimallongitude");
			fieldChanged("verbatimcoordinates");
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

function insertLatLng(f) {
	const latDeg = document.getElementById("latdeg").value.replaceAll(/^\s+|\s+$/g, "");
	let latMin = document.getElementById("latmin").value.replaceAll(/^\s+|\s+$/g, "");
	let latSec = document.getElementById("latsec").value.replaceAll(/^\s+|\s+$/g, "");
	const latNS = document.getElementById("latns").value;
	const lngDeg = document.getElementById("lngdeg").value.replaceAll(/^\s+|\s+$/g, "");
	let lngMin = document.getElementById("lngmin").value.replaceAll(/^\s+|\s+$/g, "");
	let lngSec = document.getElementById("lngsec").value.replaceAll(/^\s+|\s+$/g, "");
	const lngEW = document.getElementById("lngew").value;
	if(latDeg && latMin && lngDeg && lngMin){
		if(latMin === "") {
			latMin = 0;
		}
		if(latSec === "") {
			latSec = 0;
		}
		if(lngMin === "") {
			lngMin = 0;
		}
		if(lngSec === "") {
			lngSec = 0;
		}
		if(!isNaN(latDeg) && !isNaN(latMin) && !isNaN(latSec) && !isNaN(lngDeg) && !isNaN(lngMin) && !isNaN(lngSec)){
			if(latDeg < 0 || latDeg > 90){
				alert("Latitude degree must be between 0 and 90 degrees");
			}
			else if(lngDeg < 0 || lngDeg > 180){
				alert("Longitude degree must be between 0 and 180 degrees");
			}
			else if(latMin < 0 || latMin > 60 || lngMin < 0 || lngMin > 60 || latSec < 0 || latSec > 60 || lngSec < 0 || lngSec > 60){
				alert("Minute and second values can only be between 0 and 60");
			}
			else{
				let vcStr = f.verbatimcoordinates.value;
				vcStr = vcStr.replaceAll(/-*\d{2}[�\u00B0]+[NS\d.\s'"-�\u00B0]+[EW;]+/g, "");
				vcStr = vcStr.replaceAll(/^\s+|\s+$/g, "");
				vcStr = vcStr.replaceAll(/^;|;$/g, "");
				if(vcStr !== ""){
					vcStr = vcStr + "; ";
				}
				let dmsStr = latDeg + "\u00B0 " + latMin + "' ";
				if(latSec > 0) {
					dmsStr += latSec + '" ';
				}
				dmsStr += latNS + "  " + lngDeg + "\u00B0 " + lngMin + "' ";
				if(lngSec) {
					dmsStr += lngSec + '" ';
				}
				dmsStr += lngEW;
				f.verbatimcoordinates.value = vcStr + dmsStr;
				let latDec = parseInt(latDeg) + (parseFloat(latMin) / 60) + (parseFloat(latSec) / 3600);
				let lngDec = parseInt(lngDeg) + (parseFloat(lngMin) / 60) + (parseFloat(lngSec) / 3600);
				if(latNS === "S") {
					latDec = latDec * -1;
				}
				if(lngEW === "W") {
					lngDec = lngDec * -1;
				}
				f.decimallatitude.value = Math.round(latDec*1000000)/1000000;
				f.decimallongitude.value = Math.round(lngDec*1000000)/1000000;

				fieldChanged("decimallatitude");
				fieldChanged("decimallongitude");
				fieldChanged("verbatimcoordinates");
			}
		}
		else{
			alert("Field values must be numeric only");
		}
	}
	else{
		alert("DMS fields must contain a value");
	}
}

function insertTRS(f) {
	const township = document.getElementById("township").value.replaceAll(/^\s+|\s+$/g, "");
	const townshipNS = document.getElementById("townshipNS").value.replaceAll(/^\s+|\s+$/g, "");
	const range = document.getElementById("range").value.replaceAll(/^\s+|\s+$/g, "");
	const rangeEW = document.getElementById("rangeEW").value.replaceAll(/^\s+|\s+$/g, "");
	const section = document.getElementById("section").value.replaceAll(/^\s+|\s+$/g, "");
	const secdetails = document.getElementById("secdetails").value.replaceAll(/^\s+|\s+$/g, "");
	const meridian = document.getElementById("meridian").value.replaceAll(/^\s+|\s+$/g, "");

	let vCoord;
	if (!township || !range) {
		alert("Township and Range fields must have values");
		return false;
	}
	else if (isNaN(township)) {
		alert("Numeric value expected for Township field. If non-standardize format is used, enter directly into the Verbatim Coordinate Field");
		return false;
	}
	else if (isNaN(range)) {
		alert("Numeric value expected for Range field. If non-standardize format is used, enter directly into the Verbatim Coordinate Field");
		return false;
	}
	else if (isNaN(section)) {
		alert("Numeric value expected for Section field. If non-standardize format is used, enter directly into the Verbatim Coordinate Field");
		return false;
	}
	else if (section > 36) {
		alert("Section field must contain a numeric value between 1-36");
		return false;
	}
	else {
		vCoord = f.verbatimcoordinates;
		if (vCoord.value) {
			vCoord.value = vCoord.value + "; ";
		}
		vCoord.value = vCoord.value + "TRS: T" + township + townshipNS + " R" + range + rangeEW + " sec " + section + " " + secdetails + " " + meridian;
		fieldChanged("verbatimcoordinates");
	}
}
