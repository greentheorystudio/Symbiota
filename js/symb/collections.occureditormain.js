let pauseSubmit = false;
let imgAssocCleared = false;
let voucherAssocCleared = false;
let abortFormVerification = false;

$(document).ready(function() {

	function split( val ) {
		return val.split( /,\s*/ );
	}
	function extractLast( term ) {
		return split( term ).pop();
	}

	$("#occedittabs").tabs({
		select: function() {
			if(verifyLeaveForm()){
				document.fullform.submitaction.disabled = true;
			}
			else{
				return false;
			}
			const statusObj = document.getElementById("statusdiv");
			if(statusObj){
				statusObj.style.display = "none";
			}
			return true;
		},
		active: tabTarget,
		beforeLoad: function( event, ui ) {
			$(ui.panel).html("<p>Loading...</p>");
		}
	});

	$( "#exstitleinput" ).autocomplete({
		source: "rpc/exsiccatisuggest.php",
		minLength: 2,
		autoFocus: true,
		select: function( event, ui ) {
			if(ui.item){
				$( "#ometidinput" ).val(ui.item.id);
				fieldChanged('ometid');
			}
			else{
				$( "#ometidinput" ).val("");
				fieldChanged('ometid');
			}
		},
		change: function() {
			if($( this ).val() === ""){
				$( "#ometidinput" ).val("");
			}
			else{
				if($( "#ometidinput" ).val() === ""){
					$.ajax({
						type: "POST",
						url: "rpc/exsiccativalidation.php",
						data: { term: $( this ).val() }
					}).done(function( msg ) {
						if(msg === ""){
							alert("Exsiccati title not found within system");
						}
						else{
							$( "#ometidinput" ).val(msg);
							fieldChanged('ometid');
						}
					});
				}
			}
		}
	});

	$("#ffsciname").autocomplete({ 
		source: "rpc/getspeciessuggest.php", 
		minLength: 3,
		change: function() {
			$( "#tidinterpreted" ).val("");
			$( 'input[name=scientificnameauthorship]' ).val("");
			$( 'input[name=family]' ).val("");
			$( 'input[name=localitysecurity]' ).prop('checked', false);
			fieldChanged('sciname');
			fieldChanged('tidinterpreted');
			fieldChanged('scientificnameauthorship');
			fieldChanged('family');
			fieldChanged('localitysecurity');
			if($( "#ffsciname" ).val()){
				verifyFullFormSciName();
			}
			else{
				$( "#tidinterpreted" ).val("");
				$( 'input[name=scientificnameauthorship]' ).val("");
				$( 'input[name=family]' ).val("");
				$( 'input[name=localitysecurity]' ).prop('checked', false);
			}
		}
	});

	const cookies = document.cookie;
	if(cookies.indexOf("localauto") > -1){
		const cookieName = "localauto=";
		const ca = document.cookie.split(';');
		for(let i = 0; i <ca.length; i++) {
			let c = ca[i];
			while (c.charAt(0) === ' ') {
				c = c.substring(1);
			}
			if(c.indexOf(cookieName) == 0) {
				if(c.substring(cookieName.length) == "1") $( 'input[name=localautodeactivated]' ).prop('checked', true);
			}
		}
	}

	if(localityAutoLookup){
		$("#fflocality").autocomplete({ 
			source: function( request, response ) {
				$.ajax( {
					url: "rpc/getlocality.php",
					data: { 
						recordedby: $( "input[name=recordedby]" ).val(), 
						eventdate: $( "input[name=eventdate]" ).val(), 
						locality: request.term 
					},
					success: function( data ) {
						response( data );
		            }
				});
			},
			minLength: 4,
			select: function( event, ui ) {
				$.each(ui.item, function(k, v) {
					if($( "input[name="+k+"]" ).val() === ""){
						$( "input[name="+k+"]" ).val(v);
						$( "input[name="+k+"]" ).css("backgroundColor","lightblue");
						fieldChanged(k);
					}
				});
			}
		});
		if($( "input[name=localautodeactivated]" ).is(':checked')){
			$( "#fflocality" ).autocomplete( "option", "disabled", true );
			$( "#fflocality" ).attr('autocomplete','on');
		}
	}

	$("#ffcountry").autocomplete({
		source: function( request, response ) {
			$.getJSON( "rpc/lookupCountry.php", { term: request.term }, response );
		},
		minLength: 2,
		autoFocus: true,
		change: function(){
			fieldChanged("country");
		}
	});

	$("#ffstate").autocomplete({
		source: function( request, response ) {
			$.getJSON( "rpc/lookupState.php", { term: request.term, "country": document.fullform.country.value }, response );
		},
		minLength: 2,
		autoFocus: true,
		change: function(){
			fieldChanged("stateprovince");
		}
	});

	$("#ffcounty").autocomplete({ 
		source: function( request, response ) {
			$.getJSON( "rpc/lookupCounty.php", { term: request.term, "state": document.fullform.stateprovince.value }, response );
		},
		minLength: 2,
		autoFocus: true,
		change: function(){
			fieldChanged("county");
		}
	});

	$("#ffmunicipality").autocomplete({ 
		source: function( request, response ) {
			$.getJSON( "rpc/lookupMunicipality.php", { term: request.term, "state": document.fullform.stateprovince.value }, response );
		},
		minLength: 2,
		autoFocus: true,
		change: function(){
			fieldChanged("municipality");
		}
	});
	
	$("textarea[name=associatedtaxa]").autocomplete({
		source: function( request, response ) {
			$.getJSON( "rpc/getassocspp.php", { term: extractLast( request.term ) }, response );
		},
		search: function() {
			const term = extractLast(this.value);
			if ( term.length < 4 ) {
				return false;
			}
		},
		focus: function() {
			return false;
		},
		select: function( event, ui ) {
			const terms = split(this.value);
			terms.pop();
			terms.push( ui.item.value );
			this.value = terms.join( ", " );
			return false;
		}
	},{autoFocus: true});


	$("#catalognumber").keydown(function(event){
		if ((event.keyCode == 13)) {
			return false;
		}
	});
	
	if(document.getElementById('hostDiv')){
		$("#quickhost").autocomplete({
			source: function( request, response ) {
				const name = request.term.replaceAll(" ", "+");
				$.getJSON( "rpc/getcolspeciessuggest.php", { term: name }, response );
			},
			minLength: 4,
			autoFocus: true,
			change: function(){
				fieldChanged("host");
			}
		});
	}

	const apstatus = getCookie("autopstatus");
	if(getCookie("autopstatus")) {
		document.fullform.autoprocessingstatus.value = apstatus;
	}
	if(getCookie("autodupe") == 1) {
		document.fullform.autodupe.checked = true;
	}
});

function toggleStyle(){
	const cssObj = document.getElementById('editorCssLink');
	if(cssObj.href === "../../css/occureditorcrowdsource.css?ver=20150402"){
		cssObj.href = "../../css/occureditor.css?ver=20150402";
	}
	else{
		cssObj.href = "../../css/occureditorcrowdsource.css?ver=20150402";
	}
}

function toggleQueryForm(){
	toggle("querydiv");
	const statusDiv = document.getElementById('statusdiv');
	if(statusDiv) {
		statusDiv.style.display = 'none';
	}
}

function verifyFullFormSciName(){
	$.ajax({
		type: "POST",
		url: "rpc/verifysciname.php",
		dataType: "json",
		data: { term: $( "#ffsciname" ).val() }
	}).done(function( data ) {
		if(data){
			$( "#tidinterpreted" ).val(data.tid);
			$( 'input[name=family]' ).val(data.family);
			$( 'input[name=scientificnameauthorship]' ).val(data.author);
			if(data.status == 1){
				$( 'input[name=localitysecurity]' ).prop('checked', true);
			}
			else{
				if(data.tid){
					const stateVal = $('input[name=stateprovince]').val();
					if(stateVal !== ""){
						localitySecurityCheck();
					}
				}
			}
		}
		else{
			$( 'select[name=confidenceranking]' ).val(5);
            alert("WARNING: Taxon not found. It may be misspelled or needs to be added to taxonomic thesaurus by a taxonomic editor.");
		}
	});
}

function localitySecurityCheck(){
	const tidIn = $("input[name=tidinterpreted]").val();
	const stateIn = $("input[name=stateprovince]").val();
	if(tidIn !== "" && stateIn !== ""){
		$.ajax({
			type: "POST",
			url: "rpc/localitysecuritycheck.php",
			dataType: "json",
			data: { tid: tidIn, state: stateIn }
		}).done(function( data ) {
			if(data == "1"){
				$( 'input[name=localitysecurity]' ).prop('checked', true);
			}
		});
	}
}

function localAutoChanged(cbObj){
	if(cbObj.checked == true){
		$( "#fflocality" ).autocomplete( "option", "disabled", true );
		$( "#fflocality" ).attr('autocomplete','on');
		document.cookie = "localauto=1";
	}
	else{
		$( "#fflocality" ).autocomplete( "option", "disabled", false );
		$( "#fflocality" ).attr('autocomplete','off');
		document.cookie = "localauto=;expires=Thu, 01 Jan 1970 00:00:01 GMT;";
	}
}

function fieldChanged(fieldName){
	try{
		document.fullform.editedfields.value = document.fullform.editedfields.value + fieldName + ";";
	}
	catch(ex){}
	document.fullform.submitaction.disabled = false;
}

function recordNumberChanged(){
	fieldChanged('recordnumber');
	autoDupeSearch();
}

function stateProvinceChanged(stateVal){ 
	fieldChanged('stateprovince');
	const tidVal = $("#tidinterpreted").val();
	if(tidVal !== "" && stateVal !== ""){
		localitySecurityCheck();
	}
}

function decimalLatitudeChanged(f){
	verifyDecimalLatitude(f);
	fieldChanged('decimallatitude');
}

function decimalLongitudeChanged(f){
	verifyDecimalLongitude(f);
	verifyCoordinates(f);
	fieldChanged('decimallongitude');
}

function coordinateUncertaintyInMetersChanged(f){
	if(!isNumeric(f.coordinateuncertaintyinmeters.value)){
		alert("Coordinate uncertainty field must be numeric only");
	}
	fieldChanged('coordinateuncertaintyinmeters');
}

function footPrintWktChanged(formObj){
	fieldChanged('footprintwkt');
	if(formObj.value.length > 65000){
		formObj.value = "";
		alert("WKT footprint is too large to save in the database");
	}
}

function minimumElevationInMetersChanged(f){
	verifyMinimumElevationInMeters(f);
	fieldChanged('minimumelevationinmeters');
}

function maximumElevationInMetersChanged(f){
	verifyMaximumElevationInMeters(f);
	fieldChanged('maximumelevationinmeters');
}

function verbatimElevationChanged(f){
	if(!f.minimumelevationinmeters.value){
		parseVerbatimElevation(f);
	}
	fieldChanged("verbatimelevation");
}

function parseVerbatimElevation(f){
	if(f.verbatimelevation.value){
		let min = "";
		let max = "";
		let verbElevStr = f.verbatimelevation.value;
		verbElevStr = verbElevStr.replaceAll(/,/g ,"");

		const regEx1 = /(\d+)\s*-\s*(\d+)\s*[fte|']/i;
		const regEx2 = /(\d+)\s*[fte|']/i;
		const regEx3 = /(\d+)\s*-\s*(\d+)\s?m/i;
		const regEx4 = /(\d+)\s?-\s?(\d+)\s?m/i;
		const regEx5 = /(\d+)\s?m/i;
		let extractArr = [];
		if(regEx1.exec(verbElevStr)){
			extractArr = regEx1.exec(verbElevStr);
			min = Math.round(extractArr[1]*.3048);
			max = Math.round(extractArr[2]*.3048);
		}
		else if(regEx2.exec(verbElevStr)){
			extractArr = regEx2.exec(verbElevStr);
			min = Math.round(extractArr[1]*.3048);
		}
		else if(regEx3.exec(verbElevStr)){
			extractArr = regEx3.exec(verbElevStr);
			min = extractArr[1];
			max = extractArr[2];
		}
		else if(regEx4.exec(verbElevStr)){
			extractArr = regEx4.exec(verbElevStr);
			min = extractArr[1];
			max = extractArr[2];
		}
		else if(regEx5.exec(verbElevStr)){
			extractArr = regEx5.exec(verbElevStr);
			min = extractArr[1];
		}

		if(min){
			f.minimumelevationinmeters.value = min;
			fieldChanged("minimumelevationinmeters");
			if(max){
				f.maximumelevationinmeters.value = max;
				fieldChanged("maximumelevationinmeters");
			}
		}
	}
}

function minimumDepthInMetersChanged(f){
	if(!isNumeric(f.minimumdepthinmeters.value)){
		alert("Depth values must be numeric only");
		return false;
	}
	fieldChanged('minimumdepthinmeters');
}

function maximumDepthInMetersChanged(f){
	if(!isNumeric(f.maximumdepthinmeters.value)){
		alert("Depth values must be numeric only");
		return false;
	}
	fieldChanged('maximumdepthinmeters');
}

function verbatimCoordinatesChanged(f){
	if(!f.decimallatitude.value){
		parseVerbatimCoordinates(f,0);
	}
	fieldChanged("verbatimcoordinates");
}

function parseVerbatimCoordinates(f,verbose){
	let lngDeg;
	let lngMin;
	let latDeg;
	let latMin;
	if(f.verbatimcoordinates.value){
		let latDec = null;
		let lngDec = null;
		let verbCoordStr = f.verbatimcoordinates.value;
		verbCoordStr = verbCoordStr.replaceAll(/’/g,"'");

		const tokenArr = verbCoordStr.split(" ");

		let z = null;
		let e = null;
		let n = null;
		const zoneEx = /^\D?(\d{1,2})\D*$/;
		const eEx1 = /^(\d{6,7})E/i;
		const nEx1 = /^(\d{7})N/i;
		const eEx2 = /^E(\d{6,7})\D*$/i;
		const nEx2 = /^N(\d{4,7})\D*$/i;
		const eEx3 = /^0?(\d{6})\D*$/i;
		const nEx3 = /^(\d{7})\D*$/i;
		let extractArr = [];
		for(let i = 0; i < tokenArr.length; i++) {
			if(zoneEx.exec(tokenArr[i])){
				extractArr = zoneEx.exec(tokenArr[i]);
				z = extractArr[1];
			}
			else if(eEx1.exec(tokenArr[i])){
				extractArr = eEx1.exec(tokenArr[i]);
				e = extractArr[1];
			}
			else if(nEx1.exec(tokenArr[i])){
				extractArr = nEx1.exec(tokenArr[i]);
				n = extractArr[1];
			}
			else if(eEx2.exec(tokenArr[i])){
				extractArr = eEx2.exec(tokenArr[i]);
				e = extractArr[1];
			}
			else if(nEx2.exec(tokenArr[i])){
				extractArr = nEx2.exec(tokenArr[i]);
				n = extractArr[1];
			}
			else if(eEx3.exec(tokenArr[i])){
				extractArr = eEx3.exec(tokenArr[i]);
				e = extractArr[1];
			}
			else if(nEx3.exec(tokenArr[i])){
				extractArr = nEx3.exec(tokenArr[i]);
				n = extractArr[1];
			}
		}
		
		if(z && e && n){
			const datum = f.geodeticdatum.value;
			const llStr = utm2LatLng(z, e, n, datum);
			if(llStr){
				const llArr = llStr.split(",");
				if(llArr.length == 2){
					latDec = Math.round(llArr[0]*1000000)/1000000;
					lngDec = Math.round(llArr[1]*1000000)/1000000;
				}
			}
		}

		if(!latDec || !lngDec){
			const llEx1 = /(\d{1,2})[\D\s]{1,2}(\d{1,2}\.?\d*)['m]\s*(\d{1,2}\.?\d*)['"s]{1,2}\s*([NS]?)[\D\s]*(\d{1,3})[\D\s]{1,2}(\d{1,2}\.?\d*)['m]\s*(\d{1,2}\.?\d*)['"s]{1,2}\s*([EW]?)/i;
			const llEx2 = /(\d{1,2})[\D\s]{1,2}(\d{1,2}\.?\d*)['m]\s*([NS]?)\D*\s*(\d{1,3})[\D\s]{1,2}(\d{1,2}\.?\d*)['m]\s*([EW]?)/i;
			let extractArr = [];
			if(llEx1.exec(verbCoordStr)){
				extractArr = llEx1.exec(verbCoordStr);
				latDeg = parseInt(extractArr[1]);
				latMin = parseInt(extractArr[2]);
				const latSec = parseFloat(extractArr[3]);
				if(latDeg > 90){
					alert("Latitude degrees cannot be greater than 90");
					return '';
				}
				if(latMin > 60){
					alert("Latitude minutes cannot be greater than 60");
					return '';
				}
				if(latSec > 60){
					alert("Latitude seconds cannot be greater than 60");
					return '';
				}
				lngDeg = parseInt(extractArr[5]);
				lngMin = parseInt(extractArr[6]);
				const lngSec = parseFloat(extractArr[7]);
				if(lngDeg > 180){
					alert("Longitude degrees cannot be greater than 180");
					return '';
				}
				if(lngMin > 60){
					alert("Longitude minutes cannot be greater than 60");
					return '';
				}
				if(lngSec > 60){
					alert("Longitude seconds cannot be greater than 60");
					return '';
				}
				latDec = latDeg+(latMin/60)+(latSec/3600);
				lngDec = lngDeg+(lngMin/60)+(lngSec/3600);
				if((extractArr[4] === "S" || extractArr[4] === "s") && latDec > 0) {
					latDec = latDec*-1;
				}
				if(lngDec > 0 && extractArr[8] !== "E" && extractArr[8] !== "e") {
					lngDec = lngDec*-1;
				}
			}
			else if(llEx2.exec(verbCoordStr)){
				extractArr = llEx2.exec(verbCoordStr);
				latDeg = parseInt(extractArr[1]);
				latMin = parseFloat(extractArr[2]);
				if(latDeg > 90){
					alert("Latitude degrees cannot be greater than 90");
					return '';
				}
				if(latMin > 60){
					alert("Latitude minutes cannot be greater than 60");
					return '';
				}
				lngDeg = parseInt(extractArr[4]);
				lngMin = parseFloat(extractArr[5]);
				if(lngDeg > 180){
					alert("Longitude degrees cannot be greater than 180");
					return '';
				}
				if(lngMin > 60){
					alert("Longitude minutes cannot be greater than 60");
					return '';
				}
				latDec = latDeg+(latMin/60);
				lngDec = lngDeg+(lngMin/60);
				if((extractArr[3] === "S" || extractArr[3] === "s") && latDec > 0) {
					latDec = latDec*-1;
				}
				if(lngDec > 0 && extractArr[6] !== "E" && extractArr[6] !== "e") {
					lngDec = lngDec*-1;
				}
			}
		}

		if(latDec && lngDec){
			f.decimallatitude.value = Math.round(latDec*1000000)/1000000;
			f.decimallongitude.value = Math.round(lngDec*1000000)/1000000;
			decimalLatitudeChanged(f);
			decimalLongitudeChanged(f);
		}
		else{
			if(verbose) alert("Unable to parse coordinates");
		}
	}
}

function verifyFullForm(f){
	f.submitaction.focus();
	if(abortFormVerification) {
		return true;
	}

	if(searchDupesCatalogNumber(f,false)) {
		return false;
	}
	const validformat1 = /^\d{4}-[0][0-9]-\d{1,2}$/;
	const validformat2 = /^\d{4}-[1][0-2]-\d{1,2}$/;
	if(f.eventdate.value && !(validformat1.test(f.eventdate.value) || validformat2.test(f.eventdate.value))){
		alert("Event date is invalid");
		return false;
	}
	if(!isNumeric(f.year.value)){
		alert("Collection year field must be numeric only");
		return false;
	}
	if(!isNumeric(f.month.value)){
		alert("Collection month field must be numeric only");
		return false;
	}
	if(!isNumeric(f.day.value)){
		alert("Collection day field must be numeric only");
		return false;
	}
	if(!isNumeric(f.startdayofyear.value)){
		alert("Start day of year field must be numeric only");
		return false;
	}
	if(!isNumeric(f.enddayofyear.value)){
		alert("End day of year field must be numeric only");
		return false;
	}
	if(f.ometid && ((f.ometid.value !== "" && f.exsnumber.value === "") || (f.ometid.value === "" && f.exsnumber.value !== ""))){
		alert("You must have both an exsiccati title and exsiccati number or neither");
		return false;
	}
	if(!verifyDecimalLatitude(f)){
		return false;
	}
	if(!verifyDecimalLongitude(f)){
		return false;
	}
	if(!isNumeric(f.coordinateuncertaintyinmeters.value)){
		alert("Coordinate uncertainty field must be numeric only");
		return false;
	}
	if(!verifyMinimumElevationInMeters(f)){
		return false;
	}
	if(!verifyMaximumElevationInMeters(f)){
		return false;
	}
	if(f.maximumelevationinmeters.value){
		if(!f.minimumelevationinmeters.value){
			alert("Maximun elevation field contains a value yet minumum does not. If elevation consists of a single value rather than a range, enter the value in the minimun field.");
			return false;
		}
		else if(parseInt(f.minimumelevationinmeters.value) > parseInt(f.maximumelevationinmeters.value)){
			alert("Maximun elevation value can not be greater than the minumum value.");
			return false;
		}
	}
	if(!isNumeric(f.duplicatequantity.value)){
		alert("Duplicate Quantity field must be numeric only");
		return false;
	}
	return true;
}

function verifyFullFormEdits(f){
	if(f.editedfields && f.editedfields.value === ""){
		setTimeout(function () { 
			if(f.editedfields.value){
				f.submitaction.click();
			}
			else{
				alert("No fields appear to have been changed. If you have just changed the scientific name field, there may not have enough time to verify name. Try to submit again.");
			}
		}, 1000);
		return false;
	}
	return true;
}

function verifyGotoNew(f){
	abortFormVerification = true;
	f.gotomode.value = 1;
	f.submit();
}

function verifyDecimalLatitude(f){
	if(!isNumeric(f.decimallatitude.value)){
		alert("Input value for Decimal Latitude must be a number value only! " );
		return false;
	}
	if(parseInt(f.decimallatitude.value) > 90){
		alert("Decimal Latitude can not be greater than 90 degrees " );
		return false;
	}
	if(parseInt(f.decimallatitude.value) < -90){
		alert("Decimal Latitude can not be less than -90 degrees " );
		return false;
	}
	return true;
}

function verifyDecimalLongitude(f){
	const lngValue = f.decimallongitude.value;
	if(!isNumeric(lngValue)){
		alert("Input value for Decimal Longitude must be a number value only! " );
		return false;
	}
	if(parseInt(lngValue) > 180){
		alert("Decimal Longitude can not be greater than 180 degrees " );
		return false;
	}
	if(parseInt(lngValue) < -180){
		alert("Decimal Longitude can not be less than -180 degrees " );
		return false;
	}
	return true;
}

function verifyMinimumElevationInMeters(f){
	if(!isNumeric(f.minimumelevationinmeters.value)){
		alert("Elevation values must be numeric only");
		return false;
	}
	if(parseInt(f.minimumelevationinmeters.value) > 8000){
		alert("Was this collection really made above the elevation of Mount Everest?" );
		return false;
	}
	return true;
}

function verifyMaximumElevationInMeters(f){
	if(!isNumeric(f.maximumelevationinmeters.value)){
		alert("Elevation values must be numeric only");
		return false;
	}
	if(parseInt(f.maximumelevationinmeters.value) > 8000){
		alert("Was this collection really made above the elevation of Mount Everest?" );
		return false;
	}
	return true;
}

function verifyDeletion(f){
	const occId = f.occid.value;
	document.getElementById("delverimgspan").style.display = "block";
	verifyAssocImages(occId);
	
	document.getElementById("delvervouspan").style.display = "block";
	verifyAssocVouchers(occId);
}

function verifyAssocImages(occidIn){
	$.ajax({
		type: "POST",
		url: "rpc/getassocimgcnt.php",
		dataType: "json",
		data: { occid: occidIn }
	}).done(function( imgCnt ) {
		document.getElementById("delverimgspan").style.display = "none";
		if(imgCnt > 0){
			document.getElementById("delimgfailspan").style.display = "block";
		}
		else{
			document.getElementById("delimgappdiv").style.display = "block";
		}
		imgAssocCleared = true;
		displayDeleteSubmit();
	});
}

function verifyAssocVouchers(occidIn){
	$.ajax({
		type: "POST",
		url: "rpc/getassocvouchers.php",
		dataType: "json",
		data: { occid: occidIn }
	}).done(function( vList ) {
		document.getElementById("delvervouspan").style.display = "none";
		if(vList !== ''){
			document.getElementById("delvoulistdiv").style.display = "block";
			let strOut = "";
			for(const key in vList){
				if(vList.hasOwnProperty(key)){
					strOut = strOut + "<li><a href='../../checklists/checklist.php?cl="+key+"' target='_blank'>"+vList[key]+"</a></li>";
				}
			}
			document.getElementById("voucherlist").innerHTML = strOut;
		}
		else{
			document.getElementById("delvouappdiv").style.display = "block";
		}
		voucherAssocCleared = true;
		displayDeleteSubmit();
	});
}

function displayDeleteSubmit(){
	if(imgAssocCleared && voucherAssocCleared){
		const elem = document.getElementById("delapprovediv");
		elem.style.display = "block";
	}
}

function eventDateChanged(eventDateInput){
	const dateStr = eventDateInput.value;
	if(dateStr !== ""){
		const dateArr = parseDate(dateStr);
		if(dateArr['y'] == 0){
			alert("Unable to interpret Date. Please use the following formats: yyyy-mm-dd, mm/dd/yyyy, or dd mmm yyyy");
			return false;
		}
		else{
			try{
				const testDate = new Date(dateArr['y'], dateArr['m'] - 1, dateArr['d']);
				const today = new Date();
				if(testDate > today){
					alert("Was this plant really collected in the future? The date you entered has not happened yet. Please revise.");
					return false;
				}
			}
			catch(e){}
	
			if(dateArr['m'] > 12){
				alert("Month cannot be greater than 12. Note that the format should be YYYY-MM-DD");
				return false;
			}
	
			if(dateArr['d'] > 28){
				if(dateArr['d'] > 31 
					|| (dateArr['d'] == 30 && dateArr['m'] == 2)
					|| (dateArr['d'] == 31 && (dateArr['m'] == 4 || dateArr['m'] == 6 || dateArr['m'] == 9 || dateArr['m'] == 11))){
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
			eventDateInput.value = dateArr['y'] + "-" + mStr + "-" + dStr;
			if(dateArr['y'] > 0) distributeEventDate(dateArr['y'],dateArr['m'],dateArr['d']);
		}
	}
	fieldChanged('eventdate');
	const f = eventDateInput.form;
	if(!eventDateInput.form.recordnumber.value && f.recordedby.value) {
		autoDupeSearch();
	}
	return true;
}

function distributeEventDate(y,m,d){
	const f = document.fullform;
	if(y !== "0000"){
		f.year.value = y;
		fieldChanged("year");
	}
	if(m === "00"){
		f.month.value = "";
	}
	else{
		f.month.value = m;
		fieldChanged("year");
	}
	if(d === "00"){
		f.day.value = "";
	}
	else{
		f.day.value = d;
		fieldChanged("day");
	}
	f.startdayofyear.value = "";
	let eDate;
	try {
		if (m === 0 || d === 0) {
			f.startdayofyear.value = "";
		} else {
			eDate = new Date(y, m - 1, d);
			if (eDate instanceof Date && eDate !== "Invalid Date") {
				const onejan = new Date(y, 0, 1);
				f.startdayofyear.value = Math.ceil((eDate - onejan) / 86400000) + 1;
				fieldChanged("startdayofyear");
			}
		}
	} catch (e) {}
}

function endDateChanged(){
	const dateStr = document.getElementById("endDate").value;
	let eDate;
	if (dateStr !== "") {
		const dateArr = parseDate(dateStr);
		if (dateArr['y'] === 0) {
			alert("Unable to interpret Date. Please use the following formats: yyyy-mm-dd, mm/dd/yyyy, or dd mmm yyyy");
			return false;
		} else {
			try {
				const testDate = new Date(dateArr['y'], dateArr['m'] - 1, dateArr['d']);
				const today = new Date();
				if (testDate > today) {
					alert("Was this plant really collected in the future? The date you entered has not happened yet. Please revise.");
					return false;
				}
			} catch (e) {
			}

			if (dateArr['m'] > 12) {
				alert("Month cannot be greater than 12. Note that the format should be YYYY-MM-DD");
				return false;
			}

			if (dateArr['d'] > 28) {
				if (dateArr['d'] > 31
					|| (dateArr['d'] === 30 && dateArr['m'] === 2)
					|| (dateArr['d'] === 31 && (dateArr['m'] === 4 || dateArr['m'] === 6 || dateArr['m'] === 9 || dateArr['m'] === 11))) {
					alert("The Day (" + dateArr['d'] + ") is invalid for that month");
					return false;
				}
			}

			let mStr = dateArr['m'];
			if (mStr.length === 1) {
				mStr = "0" + mStr;
			}
			let dStr = dateArr['d'];
			if (dStr.length === 1) {
				dStr = "0" + dStr;
			}
			document.getElementById("endDate").value = dateArr['y'] + "-" + mStr + "-" + dStr;
			if (dateArr['y'] > 0) {
				const f = document.fullform;
				f.enddayofyear.value = "";
				try {
					if (dateArr['m'] === 0 || dateArr['d'] === 0) {
						f.enddayofyear.value = "";
					} else {
						eDate = new Date(dateArr['y'], dateArr['m'] - 1, dateArr['d']);
						if (eDate instanceof Date) {
							const onejan = new Date(dateArr['y'], 0, 1);
							f.enddayofyear.value = Math.ceil((eDate - onejan) / 86400000) + 1;
							fieldChanged("enddayofyear");
						}
					}
				} catch (e) {}
			}
		}
	}
    return true;
}

function parseDate(dateStr){
	const dateObj = new Date(dateStr);
	let dateTokens;
	let y = 0;
	let m = 0;
	let d = 0;
	let mText;
	try {
		const validformat1 = /^\d{4}-\d{1,2}-\d{1,2}$/;
		const validformat2 = /^\d{1,2}\/\d{1,2}\/\d{2,4}$/;
		const validformat3 = /^\d{1,2} \D+ \d{2,4}$/;
		if (validformat1.test(dateStr)) {
			dateTokens = dateStr.split("-");
			y = dateTokens[0];
			m = dateTokens[1];
			d = dateTokens[2];
		} else if (validformat2.test(dateStr)) {
			dateTokens = dateStr.split("/");
			m = dateTokens[0];
			d = dateTokens[1];
			y = dateTokens[2];
			if (y.length === 2) {
				if (y < 20) {
					y = "20" + y;
				} else {
					y = "19" + y;
				}
			}
		} else if (validformat3.test(dateStr)) {
			dateTokens = dateStr.split(" ");
			d = dateTokens[0];
			mText = dateTokens[1];
			y = dateTokens[2];
			if (y.length === 2) {
				if (y < 15) {
					y = "20" + y;
				} else {
					y = "19" + y;
				}
			}
			mText = mText.substring(0, 3);
			mText = mText.toLowerCase();
			const mNames = ["jan", "feb", "mar", "apr", "may", "jun", "jul", "aug", "sep", "oct", "nov", "dec"];
			m = mNames.indexOf(mText) + 1;
		} else if (dateObj instanceof Date) {
			y = dateObj.getFullYear();
			m = dateObj.getMonth() + 1;
			d = dateObj.getDate();
		}
	} catch (ex) {}
	const retArr = [];
	retArr["y"] = y.toString();
	retArr["m"] = m.toString();
	retArr["d"] = d.toString();
	return retArr;
}

function initDetAutocomplete(f){
	$( f.sciname ).autocomplete({ 
		source: "rpc/getspeciessuggest.php", 
		minLength: 3,
		change: function() {
			if(f.sciname.value){
				pauseSubmit = true;
				verifyDetSciName(f);
			}
			else{
				f.scientificnameauthorship.value = "";
				f.family.value = "";
				f.tidtoadd.value = "";
			}				
		}
	});
}

function verifyDetSciName(f){
	$.ajax({
		type: "POST",
		url: "rpc/verifysciname.php",
		dataType: "json",
		data: { term: f.sciname.value }
	}).done(function( data ) {
		if(data){
			f.scientificnameauthorship.value = data.author;
			f.family.value = data.family;
			f.tidtoadd.value = data.tid;
		}
		else{
            alert("WARNING: Taxon not found. It may be misspelled or needs to be added to taxonomic thesaurus by a taxonomic editor.");
			f.scientificnameauthorship.value = "";
			f.family.value = "";
			f.tidtoadd.value = "";
		}
	});
} 

function detDateChanged(f){
	let isNew = false;
	const newDateStr = f.dateidentified.value;
	if(newDateStr){
		let dateIdentified = document.fullform.dateidentified.value;
		if(dateIdentified === "") {
			dateIdentified = document.fullform.eventdate.value;
		}
		if(dateIdentified){
			const yearPattern = /[1,2]\d{3}/;
			const newYear = newDateStr.match(yearPattern);
			const curYear = dateIdentified.match(yearPattern);
			if(curYear && newYear && newYear[0] > curYear[0]){
				isNew = true;
			}
		}
		else{
			isNew = true;
		}
	}
	f.makecurrent.checked = isNew;
}

function verifyDetForm(f){
	if(f.sciname.value === ""){
		alert("Scientific Name field must have a value");
		return false;
	}
	if(f.identifiedby.value === ""){
		alert("Determiner field must have a value (enter 'unknown' if not defined)");
		return false;
	}
	if(f.dateidentified.value === ""){
		alert("Determination Date field must have a value (enter 'unknown' if not defined)");
		return false;
	}
	if(f.sortsequence && !isNumeric(f.sortsequence.value)){
		alert("Sort Sequence field must be a numeric value only");
		return false;
	}
	if(pauseSubmit){
		const date = new Date();
		let curDate = null;
		do{ 
			curDate = new Date(); 
		}
		while(curDate - date < 5000 && pauseSubmit);
	}
	return true;
}

function verifyImgAddForm(f){
    if(f.elements["imgfile"].value.replace(/\s/g, "") === ""){
		const imgUrl = f.elements["imgurl"].value.replaceAll(/\s/g, "");
		if(imgUrl === ""){
        	alert("Select an image file or enter a URL to an existing image");
			return false;
        }
    }
    return true;
}

function verifyImgEditForm(f){
	if(f.url.value === ""){
		alert("Web URL field must have a value");
		return false;
	}
	return true;
}

function verifyImgDelForm(){
	return confirm('Are you sure you want to delete this image? Note that the physical image will be deleted from the server if checkbox is selected.');

}

function verifyImgRemapForm(f){
	if(f.targetoccid.value === ''){
		alert("Enter the occurrence record identifier (occid) of the occurrence record you want to transfer to");
		return false;
	}
	return true;
}

function dwcDoc(dcTag){
	let dwcWindow = open("http://symbiota.org/docs/symbiota-occurrence-data-fields-2/#" + dcTag, "dwcaid", "width=1250,height=300,left=20,top=20,scrollbars=1");
	if(dwcWindow.opener == null) {
		dwcWindow.opener = self;
	}
	dwcWindow.focus();
	return false;
}

function openOccurrenceSearch(target) {
	collId = document.fullform.collid.value;
	let occWindow = open("../misc/occurrencesearch.php?targetid=" + target + "&collid=" + collId, "occsearch", "resizable=1,scrollbars=1,toolbar=1,width=750,height=600,left=20,top=20");
	occWindow.focus();
	if (occWindow.opener == null) {
		occWindow.opener = self;
	}
}

function localitySecurityChanged(){
	fieldChanged('localitysecurity');
	$("#locsecreason").show();
}

function localitySecurityReasonChanged(){
	fieldChanged('localitysecurityreason');
	if($("input[name=localitysecurityreason]").val() === ''){
		$("input[name=lockLocalitySecurity]").prop('checked', false);
	}
	else{
		$("input[name=lockLocalitySecurity]").prop('checked', true);
	}
}

function securityLockChanged(cb){
	if(cb.checked === true){
		if($("input[name=localitysecurityreason]").val() === '') {
			$("input[name=localitysecurityreason]").val("<Security Setting Locked>");
		}
	}
	else{
		$("input[name=localitysecurityreason]").val("")
	}
	fieldChanged('localitysecurityreason');
}

function autoProcessingStatusChanged(selectObj){
	const selValue = selectObj.value;
	if(selValue){
		document.cookie = "autopstatus=" + selValue;
	}
	else{
		document.cookie = "autopstatus=;expires=Thu, 01 Jan 1970 00:00:01 GMT;";
	}
}

function autoDupeChanged(dupeCbObj){
	if(dupeCbObj.checked){
		document.cookie = "autodupe=1";
	}
	else{
		document.cookie = "autodupe=;expires=Thu, 01 Jan 1970 00:00:01 GMT;";
	}
}

function inputIsNumeric(inputObj, titleStr){
	if(!isNumeric(inputObj.value)){
		alert("Input value for " + titleStr + " must be a number value only! " );
	}
}

function isNumeric(sText){
	const validChars = "0123456789-.";
	let isNumber = true;
	let charVar;

	for(let i = 0; i < sText.length && isNumber === true; i++){
   		charVar = sText.charAt(i); 
		if(validChars.indexOf(charVar) === -1){
			isNumber = false;
			break;
      	}
   	}
	return isNumber;
}

function getCookie(cName){
	let x, y;
	const cookieArr = document.cookie.split(";");
	for(let i = 0;i<cookieArr.length;i++){
		x = cookieArr[i].substr(0,cookieArr[i].indexOf("="));
		y = cookieArr[i].substr(cookieArr[i].indexOf("=")+1);
		x = x.replaceAll(/^\s+|\s+$/g,"");
		if (x === cName){
			return unescape(y);
		}
	}
}
