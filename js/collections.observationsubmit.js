let taxonValid = false;

document.addEventListener("DOMContentLoaded", function() {
	$("#sciname").autocomplete({ 
		source: "../../api/taxa/getspeciessuggest.php",
		minLength: 3,
		change: function() {
			const f = document.obsform;
			if( f.sciname.value ){
				$.ajax({
					type: "POST",
					url: "../../api/taxa/verifysciname.php",
					dataType: "json",
					data: { term: f.sciname.value }
				}).done(function( data ) {
					if(data){
						f.scientificnameauthorship.value = data.author;
						f.family.value = data.family;
						taxonValid = true;
					}
					else{
						alert("Taxon not found. Maybe misspelled or needs to be added to taxonomic thesaurus.");
						f.scientificnameauthorship.value = "";
						f.family.value = "";
						taxonValid = false;
					}
				});
			}
			else{
				f.scientificnameauthorship.value = "";
				f.family.value = "";
			}				
		}
	});
});

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

function insertElevFt(f){
	const elev = document.getElementById("elevft").value;
	f.minimumelevationinmeters.value = Math.round(elev*.03048)*10;
}

function verifyObsForm(f){
    if(f.sciname.value === ""){
		window.alert("Observation must have an identification (scientific name) assigned to it, even if it is only to family rank.");
		return false;
    }
    else{
		if(!taxonValid){
			alert("Scientific name invalid");
			return false;
		}
    }
    if(f.recordedby.value === ""){
		window.alert("Observer field must have a value.");
		return false;
    }
    if(f.eventdate.value === ""){
		window.alert("Observation date must have a value.");
		return false;
    }
	const validDate = /^\d{4}-\d{2}-\d{2}$/;
	if(!validDate.test(f.eventdate.value)){
    	window.alert("Observation date must follow format: yyyy-mm-dd");
		return false;
    }
    if(f.locality.value === ""){
		window.alert("Locality must have a value to submit an observation.");
		return false;
    }
    if(f.decimallatitude.value === "" || f.decimallongitude.value === ""){
		window.alert("Latitude and Longitude are required values. Click on global symbol to open mapping aid.");
		return false;
    }
    if(f.coordinateuncertaintyinmeters.value === ""){
		window.alert("Coordinate uncertainty (in meters) is required.");
		return false;
    }
    if(isNaN(f.decimallatitude.value)){
		window.alert("Latitude must be in the decimal format with numeric characters only (34.5335). ");
		return false;
    }
    if(isNaN(f.decimallongitude.value)){
		window.alert("Longitude must be in the decimal format with numeric characters only. Note that the western hemisphere is represented as a negitive number (-110.5335). ");
		return false;
    }
    if(parseInt(f.decimallongitude.value ) > 0 && (f.country === 'USA' || f.country === 'Canada' || f.country === 'Mexico')){
		window.alert("For North America, the decimal format of longitude should be negitive value. ");
		return false;
    }
    if(isNaN(f.coordinateuncertaintyinmeters.value)){
		window.alert("Coordinate Uncertainty must be a numeric value only (in meters). ");
		return false;
    }
    if(isNaN(f.minimumelevationinmeters.value)){
		window.alert("Elevation must be a numeric value only. ");
		return false;
    }
    if(f.imgfile1.value === ""){
   		window.alert("An observation submitted through this interface must be documented with at least one image.");
		return false;
    }
    return true;
}

function verifyDate(eventDateInput){
	const dateStr = eventDateInput.value;
	if(dateStr === "") {
		return true;
	}

	const dateArr = parseDate(dateStr);
	if(dateArr['y'] == 0){
		alert("Unable to interpret Date. Please use the following formats: yyyy-mm-dd, mm/dd/yyyy, or dd mmm yyyy");
		eventDateInput.value = "";
		return false;
	}
	else{
		try{
			const testDate = new Date(dateArr['y'], dateArr['m'] - 1, dateArr['d']);
			const today = new Date();
			if(testDate > today){
				alert("Was this plant really collected in the future? The date you entered has not happened yet. Please revise.");
				eventDateInput.value = "";
				return false;
			}
		}
		catch(e){}

		if(dateArr['d'] > 28){
			if(dateArr['d'] > 31 
				|| (dateArr['d'] == 30 && dateArr['m'] == 2)
				|| (dateArr['d'] == 31 && (dateArr['m'] == 4 || dateArr['m'] == 6 || dateArr['m'] == 9 || dateArr['m'] == 11))){
				alert("The Day (" + dateArr['d'] + ") is invalid for that month");
				eventDateInput.value = "";
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
		eventDateInput.value = dateArr['y'] + "-" + mStr + "-" + dStr;
	}
	return true;
}

function verifyLatValue(f){
	const inputObj = f.decimallatitude.value;
	inputIsNumeric(inputObj, 'Decimal Latitude');
	if(inputObj.value > 90 || inputObj.value < -90){
		alert('Decimal latitude value should be between -90 and 90 degrees');
	}
	verifyCoordinates(f);
}

function verifyLngValue(f){
	const inputObj = f.decimallongitude.value;
	inputIsNumeric(inputObj, 'Decimal Longitude');
	if(inputObj.value > 180 || inputObj.value < -180){
		alert('Decimal longitude value should be between -180 and 180 degrees');
	}
	verifyCoordinates(f);
}

function verifyElevValue(inputObj){
	inputIsNumeric(inputObj, 'Coordinate Uncertainty');
	if(inputObj.value > 4000){
		alert('Are you sure your elevation value in meters. ' + inputObj.value + ' meters is a very high elevation.');
	}
}

function verifyImageSize(inputObj,maxSize){
	if (!window.FileReader) {
		return;
	}

	const file = inputObj.files[0];
	if(file.size > (maxSize * 1000 * 1000)){
		alert("Image "+file.name+" file size ("+Math.round(file.size/100000)/10+"MB) is larger than is allowed ("+maxSize+"MB)");
    }
}

function inputIsNumeric(inputObj, titleStr){
	if(isNaN(inputObj.value)){
		alert("Input value for " + titleStr + " must be a number value only! " );
	}
}
