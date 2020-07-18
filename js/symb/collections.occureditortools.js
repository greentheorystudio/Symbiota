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

function geoCloneTool(){
	const f = document.fullform;
	if(f.locality.value){
		let url = "../georef/georefclone.php?";
		url = url + "locality=" + f.locality.value;
		url = url + "&country=" + f.country.value;
		url = url + "&state=" + f.stateprovince.value;
		url = url + "&county=" + f.county.value;
		url = url + "&collid=" + f.collid.value;
		cloneWindow=open(url,"geoclonetool","resizable=1,scrollbars=1,toolbar=1,width=1000,height=600,left=20,top=20");
		if(cloneWindow.opener == null) {
			cloneWindow.opener = self;
		}
	}
	else{
		alert("Locality field must have a value to use this function");
		return false;
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
		document.getElementById("editorCssLink").href = "includes/config/occureditorcrowdsource.css?ver=170201";
		document.getElementById("longtagspan").style.display = "block";
		document.getElementById("shorttagspan").style.display = "none";
	}
	else{
		document.getElementById("editorCssLink").href = "../../css/occureditor.css";
		document.getElementById("longtagspan").style.display = "none";
		document.getElementById("shorttagspan").style.display = "block";
	}
}

function openMappingAid() {
	const f = document.fullform;
	const latDef = f.decimallatitude.value;
	const lngDef = f.decimallongitude.value;
	const errRadius = f.coordinateuncertaintyinmeters.value;
	let zoom = 5;
	if(latDef && lngDef) {
		zoom = 11;
	}
	const mapWindow = open("mappointaid.php?latdef=" + latDef + "&lngdef=" + lngDef + "&errrad=" + errRadius + "&zoom=" + zoom, "mappointaid", "resizable=0,width=800,height=700,left=20,top=20");
	if(mapWindow != null){
		if (mapWindow.opener == null) {
			mapWindow.opener = self;
		}
		mapWindow.focus();
	}
	else{
		alert("Unable to open map, which is likely due to your browser blocking popups. Please adjust your browser settings to allow popups from this website.");
	}
}

function openMappingPolyAid() {
	const zoom = 5;
	const mapWindow = open("../../tools/mappolyaid.php?zoom=" + zoom, "mappolyaid", "resizable=0,width=800,height=700,left=20,top=20");
	if(mapWindow != null){
		if (mapWindow.opener == null) mapWindow.opener = self;
		mapWindow.focus();
	}
	else{
		alert("Unable to open map, which is likely due to your browser blocking popups. Please adjust your browser settings to allow popups from this website.");
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
	const zValue = document.getElementById("utmzone").value.replace(/^\s+|\s+$/g, "");
	const hValue = document.getElementById("hemisphere").value;
	const eValue = document.getElementById("utmeast").value.replace(/^\s+|\s+$/g, "");
	const nValue = document.getElementById("utmnorth").value.replace(/^\s+|\s+$/g, "");
	if(zValue && eValue && nValue){
		if(isNumeric(eValue) && isNumeric(nValue)){
			let vcStr = f.verbatimcoordinates.value;
			vcStr = vcStr.replace(/\d{2}.*\d+E\s+\d+N[;\s]*/g, "");
			vcStr = vcStr.replace(/(Northern)|(Southern)/g, "");
			vcStr = vcStr.replace(/^\s+|\s+$/g, "");
			vcStr = vcStr.replace(/^;|;$/g, "");
			if(vcStr !== ""){
				vcStr = vcStr + "; ";
			}
			const utmStr = zValue + " " + eValue + "E " + nValue + "N ";
			f.verbatimcoordinates.value = vcStr + utmStr;
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
	const latDeg = document.getElementById("latdeg").value.replace(/^\s+|\s+$/g, "");
	let latMin = document.getElementById("latmin").value.replace(/^\s+|\s+$/g, "");
	let latSec = document.getElementById("latsec").value.replace(/^\s+|\s+$/g, "");
	const latNS = document.getElementById("latns").value;
	const lngDeg = document.getElementById("lngdeg").value.replace(/^\s+|\s+$/g, "");
	let lngMin = document.getElementById("lngmin").value.replace(/^\s+|\s+$/g, "");
	let lngSec = document.getElementById("lngsec").value.replace(/^\s+|\s+$/g, "");
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
		if(isNumeric(latDeg) && isNumeric(latMin) && isNumeric(latSec) && isNumeric(lngDeg) && isNumeric(lngMin) && isNumeric(lngSec)){
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
				vcStr = vcStr.replace(/-*\d{2}[�\u00B0]+[NS\d.\s'"-�\u00B0]+[EW;]+/g, "");
				vcStr = vcStr.replace(/^\s+|\s+$/g, "");
				vcStr = vcStr.replace(/^;|;$/g, "");
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
	const township = document.getElementById("township").value.replace(/^\s+|\s+$/g, "");
	const townshipNS = document.getElementById("townshipNS").value.replace(/^\s+|\s+$/g, "");
	const range = document.getElementById("range").value.replace(/^\s+|\s+$/g, "");
	const rangeEW = document.getElementById("rangeEW").value.replace(/^\s+|\s+$/g, "");
	const section = document.getElementById("section").value.replace(/^\s+|\s+$/g, "");
	const secdetails = document.getElementById("secdetails").value.replace(/^\s+|\s+$/g, "");
	const meridian = document.getElementById("meridian").value.replace(/^\s+|\s+$/g, "");

	let vCoord;
	if (!township || !range) {
		alert("Township and Range fields must have values");
		return false;
	} else if (!isNumeric(township)) {
		alert("Numeric value expected for Township field. If non-standardize format is used, enter directly into the Verbatim Coordinate Field");
		return false;
	} else if (!isNumeric(range)) {
		alert("Numeric value expected for Range field. If non-standardize format is used, enter directly into the Verbatim Coordinate Field");
		return false;
	} else if (!isNumeric(section)) {
		alert("Numeric value expected for Section field. If non-standardize format is used, enter directly into the Verbatim Coordinate Field");
		return false;
	} else if (section > 36) {
		alert("Section field must contain a numeric value between 1-36");
		return false;
	} else {
		vCoord = f.verbatimcoordinates;
		if (vCoord.value) {
			vCoord.value = vCoord.value + "; ";
		}
		vCoord.value = vCoord.value + "TRS: T" + township + townshipNS + " R" + range + rangeEW + " sec " + section + " " + secdetails + " " + meridian;
		fieldChanged("verbatimcoordinates");
	}
}

function searchDupesCatalogNumber(f,verbose){
	const cnValue = f.catalognumber.value;
	if(cnValue){
		const occid = f.occid.value;
		if(verbose){
			document.getElementById("dupeMsgDiv").style.display = "block";
			document.getElementById("dupesearch").style.display = "block";
			document.getElementById("dupenone").style.display = "none";
		}

		$.ajax({
			type: "POST",
			url: "rpc/dupequerycatnum.php",
			data: { catnum: cnValue, collid: f.collid.value, occid: f.occid.value }
		}).done(function( msg ) {
			if(msg){
				if(confirm("Record(s) of same catalog number already exists. Do you want to view this record?")){
					const occWindow = open("dupesearch.php?occidquery=catnu:" + msg + "&collid=" + f.collid.value + "&curoccid=" + occid, "occsearch", "resizable=1,scrollbars=1,toolbar=1,width=900,height=600,left=20,top=20");
					if(occWindow != null){
						if (occWindow.opener == null) {
							occWindow.opener = self;
						}
						occWindow.focus();
					}
					else{
						alert("Unable to display record, which is likely due to your browser blocking popups. Please adjust your browser settings to allow popups from this website.");
					}
				}
				if(verbose){
					document.getElementById("dupesearch").style.display = "none";
					document.getElementById("dupeMsgDiv").style.display = "none";
				}
				return true;
			}
			else{
				if(verbose){
					document.getElementById("dupesearch").style.display = "none";
					document.getElementById("dupenone").style.display = "block";
					setTimeout(function () { 
						document.getElementById("dupenone").style.display = "none";
						document.getElementById("dupeMsgDiv").style.display = "none";
						}, 3000);
				}
				return false;
			}
		});
	}
}

function searchDupesOtherCatalogNumbers(f){
	const ocnValue = f.othercatalognumbers.value;
	if(ocnValue){
		document.getElementById("dupeMsgDiv").style.display = "block";
		document.getElementById("dupesearch").style.display = "block";
		document.getElementById("dupenone").style.display = "none";

		$.ajax({
			type: "POST",
			url: "rpc/dupequeryothercatnum.php",
			data: { othercatnum: ocnValue, collid: f.collid.value, occid: f.occid.value }
		}).done(function( msg ) {
			if(msg.length > 6){
				if(confirm("Record(s) using the same identifier already exists. Do you want to view this record?")){
					const occWindow = open("dupesearch.php?occidquery=" + msg + "&collid=" + f.collid.value + "&curoccid=" + f.occid.value, "occsearch", "resizable=1,scrollbars=1,toolbar=1,width=900,height=600,left=20,top=20");
					if(occWindow != null){
						if (occWindow.opener == null) {
							occWindow.opener = self;
						}
						occWindow.focus();
					}
					else{
						alert("Unable to show record, which is likely due to your browser blocking popups. Please adjust your browser settings to allow popups from this website.");
					}
				}						
				document.getElementById("dupesearch").style.display = "none";
				document.getElementById("dupeMsgDiv").style.display = "none";
			}
			else{
				document.getElementById("dupesearch").style.display = "none";
				document.getElementById("dupenone").style.display = "block";
				setTimeout(function () { 
					document.getElementById("dupenone").style.display = "none";
					document.getElementById("dupeMsgDiv").style.display = "none";
				}, 3000);
			}
		});

	}
}

function searchDupes(f,silent){
	const cNameIn = f.recordedby.value;
	const cNumIn = f.recordnumber.value;
	const cDateIn = f.eventdate.value;
	let ometidIn = "";
	let exsNumberIn = "";
	if(f.ometid){
		ometidIn = f.ometid.value;
		exsNumberIn = f.exsnumber.value;
	}
	const currOccidIn = f.occid.value;

	if((!cNameIn || (!cNumIn && !cDateIn)) && (!ometidIn || !exsNumberIn)){
		if(!silent) {
			alert("Criteria not complete for duplicate search (collector name, number, date, or exsiccati");
		}
		return false;
	}

	document.getElementById("dupeMsgDiv").style.display = "block";
	document.getElementById("dupesearch").style.display = "block";
	document.getElementById("dupenone").style.display = "none";

	$.ajax({
		type: "POST",
		url: "rpc/dupequery.php",
		data: { cname: cNameIn, cnum: cNumIn, cdate: cDateIn, ometid: ometidIn, exsnumber: exsNumberIn, curoccid: currOccidIn }
	}).done(function( msg ) {
		if(msg){
			const dupOccWindow = open("dupesearch.php?occidquery=" + msg + "&collid=" + f.collid.value + "&curoccid=" + currOccidIn, "occsearch", "resizable=1,scrollbars=1,toolbar=1,width=900,height=600,left=20,top=20");
			if(dupOccWindow != null){
				if(dupOccWindow.opener == null) {
					dupOccWindow.opener = self;
				}
				dupOccWindow.focus();
				document.getElementById("dupesearch").style.display = "none";
				document.getElementById("dupeMsgDiv").style.display = "none";
			}
			else{
				alert("Duplicate found but unable to display. This is likely due to your browser blocking popups. Please adjust your browser settings to allow popups from this website.");
				document.getElementById("dupeMsgDiv").style.display = "none";
				document.getElementById("dupesearch").style.display = "none";
			}
		}
		else{
			document.getElementById("dupesearch").style.display = "none";
			document.getElementById("dupenone").style.display = "block";
			setTimeout(function () { 
				document.getElementById("dupenone").style.display = "none";
				document.getElementById("dupeMsgDiv").style.display = "none";
			}, 5000);
		}
	});
}

function autoDupeSearch(){
	const f = document.fullform;
	if(f.autodupe && f.autodupe.checked === true){
		searchDupes(f,true);
	}
}
