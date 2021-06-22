let sec = 0;
let count = 0;

$(document).ready(function() {
	$("#fsciname").autocomplete({
		source: "rpc/getspeciessuggest.php", 
		minLength: 3,
		autoFocus: true,
		change: function() {
			$( "#ftidinterpreted" ).val("");
			$( '#fscientificnameauthorship' ).val("");
			$( '#ffamily' ).val("");
			$( '#flocalitysecurity' ).prop('checked', false);
			if($( "#fsciname" ).val()){
				verifySciName();
			}
		}
	});

	$("#fcountry").autocomplete({
		source: "rpc/lookupCountry.php", 
		minLength: 2,
		autoFocus: true
	});

	$("#fstateprovince").autocomplete({
		source: function( request, response ) {
			$.getJSON( "rpc/lookupState.php", { term: request.term, country: document.defaultform.country.value }, response );
		},
		minLength: 2,
		autoFocus: true
	});

	$("#fcounty").autocomplete({ 
		source: function( request, response ) {
			$.getJSON( "rpc/lookupCounty.php", { term: request.term, "state": document.defaultform.stateprovince.value }, response );
		},
		minLength: 2,
		autoFocus: true
	});

	setInterval( function(){
		$("#seconds").html(pad(++sec%60));
		$("#minutes").html(pad(sec/60,10));
	}, 1000);
});

function showOptions(){
	$( "#optiondiv" ).show();
	$( "#hidespan" ).show();
}

function hideOptions(){
	$( "#optiondiv" ).hide();
	$( "#hidespan" ).hide();
}

function verifySciName(){
	$.ajax({
		type: "POST",
		url: "rpc/verifysciname.php",
		dataType: "json",
		data: { term: $( "#fsciname" ).val() }
	}).done(function( data ) {
		if(data){
			$( "#ftidinterpreted" ).val(data.tid);
			$( '#ffamily' ).val(data.family);
			$( '#fscientificnameauthorship' ).val(data.author);
			if(data.status == 1){
				$( '#flocalitysecurity' ).prop('checked', true);
			}
			else{
				if(data.tid){
					const stateVal = $('#fstateprovince').val();
					if(stateVal !== ""){
						localitySecurityCheck();
					}
				}
			}
		}
		else{
            alert("WARNING: Taxon not found. It may be misspelled or needs to be added to taxonomic thesaurus by a taxonomic editor.");
		}
	});
}

function localitySecurityCheck(){
	const tidIn = $("#ftidinterpreted").val();
	const stateIn = $("#stateprovince").val();
	if(tidIn !== "" && stateIn !== ""){
		$.ajax({
			type: "POST",
			url: "rpc/localitysecuritycheck.php",
			dataType: "json",
			data: { tid: tidIn, state: stateIn }
		}).done(function( data ) {
			if(data == "1"){
				$( '#flocalitysecurity' ).prop('checked', true);
			}
		});
	}
}

function stateProvinceChanged(stateVal){
	const tidVal = $("#ftidinterpreted").val();
	if(tidVal !== "" && stateVal !== ""){
		localitySecurityCheck();
	}
}

function submitDefaultForm(){
	const continueSubmit = true;
	if($("#feventdate").val() !== ""){
		const dateStr = $("#feventdate").val();
		try{
			const validformat1 = /^\s*[<>]?\s?\d{4}-\d{2}-\d{2}\s*$/;
			if(!validformat1.test(dateStr)){
				alert("Event date must follow YYYY-MM-DD format. Note that 00 can be entered for a non-determined month or day.");
				return false;
			}
		}
		catch(ex){}
	}

	if(continueSubmit && $( "#fcatalognumber" ).val() !== ""){
		$.ajax({
			type: "POST",
			url: "rpc/occurAddData.php",
			dataType: "json",
			data: { 
				sciname: $( "#fsciname" ).val(), 
				scientificnameauthorship: $( "#fscientificnameauthorship" ).val(), 
				family: $( "#ffamily" ).val(), 
				localitysecurity: ($( "#flocalitysecurity" ).prop('checked')?"1":"0"),
				country: $( "#fcountry" ).val(), 
				stateprovince: $( "#fstateprovince" ).val(), 
				county: $( "#fcounty" ).val(), 
				processingstatus: $( "#fprocessingstatus" ).val(), 
				recordedby: $( "#frecordedby" ).val(), 
				recordnumber: $( "#frecordnumber" ).val(), 
				eventdate: $( "#feventdate" ).val(), 
				language: $( "#flanguage" ).val(), 
				othercatalognumbers: $( "#fothercatalognumbers" ).val(),
				catalognumber: $( "#fcatalognumber" ).val(),
				collid: $( "#fcollid" ).val(),
				addaction: $( "input[name=addaction]:checked" ).val()
			}
		}).done(function( retObj ) {
			if(retObj.status === "true"){
				const newDiv = createOccurDiv($("#fcatalognumber").val(), retObj.occid, retObj.action);

				const listElem = document.getElementById("occurlistdiv");
				listElem.insertBefore(newDiv,listElem.childNodes[0]);

				incrementCount();
			}
			else{
				if(retObj.error){
					if(retObj.error === 'dupeCatalogNumber'){
						if(confirm("Another record exists with the same catalog number, which is set as not allowed within options. Do you want to view the other record(s)?")){
							openEditPopup(retObj.occid);
						}
					}
					else{
						alert(retObj.error);
					}
				}
				else{
					alert('Failed: unknown error');
				}
			}
		});
	}
	
	$( "#fcatalognumber" ).focus();
	return false;
}

function createOccurDiv(catalogNumber, occid, action){
	const newAnchor = document.createElement('a');
	newAnchor.setAttribute("id", "a-"+occid);
	newAnchor.setAttribute("href", "#");
	newAnchor.setAttribute("onclick", "openEditPopup("+occid+",false);return false;");
	const newText = document.createTextNode(catalogNumber);
	newAnchor.appendChild(newText);

	const newAnchor2 = document.createElement('a');
	newAnchor2.setAttribute("id", "a2-"+occid);
	newAnchor2.setAttribute("href", "#");
	newAnchor2.setAttribute("onclick", "openEditPopup("+occid+",true);return false;");
	const newImg = document.createElement('i');
	newImg.setAttribute("class", "far fa-file-image");
	newImg.setAttribute("style", "height:15px;width:15px;margin-left:5px;");

	newAnchor2.appendChild(newImg);

	const newDiv = document.createElement('div');
	newDiv.setAttribute("id", "o-"+occid);
	newDiv.appendChild(newAnchor);
	newDiv.appendChild(newAnchor2);
	if(action === "update"){
		const newSpan = document.createElement("span");
		const spanText = document.createTextNode(" (update of existing record)");
		newSpan.appendChild(spanText);
		newDiv.appendChild(newSpan);
	}

	return newDiv;
}

function deleteOccurrence(occid){
	if(imgAssocCleared && voucherAssocCleared){
		const elem = document.getElementById("delapprovediv");
		elem.style.display = "block";
	}
}

function eventDateChanged(eventDateInput){
	const dateStr = eventDateInput.value;
	if(dateStr === "") {
		return true;
	}

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

function openEditPopup(occidStr,targetImgTab){
	let collid = $("#fcollid").val();
	let urlStr = "occurrenceeditor.php?collid=" + collid + "&q_catalognumber=occid" + occidStr + "&occindex=0";
	if(targetImgTab) {
		urlStr = urlStr + '&tabtarget=2';
	}

	let wWidth = 900;
	if(document.getElementById('innertext').offsetWidth){
		wWidth = document.getElementById('innertext').offsetWidth*1.05;
	}
	else if(document.body.offsetWidth){
		wWidth = document.body.offsetWidth*0.9;
	}
	const newWindow = window.open(urlStr, 'popup', 'scrollbars=1,toolbar=1,resizable=1,width=' + (wWidth) + ',height=600,left=20,top=20');
	if(newWindow != null){
		if (newWindow.opener == null) newWindow.opener = self;
	}
	else{
		alert("Unable to display record, which is likely due to your browser blocking popups. Please adjust your browser settings to allow popups from this website.");
	}
	return false;
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

function pad( val ){ 
	return val > 9 ? val : "0" + val; 
}

function incrementCount(){
	$("#count").html(++count);
	$("#rate").html(Math.round(3660*count/sec));
}
