$('html').hide();
$(document).ready(function() {
	$('html').show();
});

$(document).ready(function() {
	$('#tabs').tabs({ 
		active: tabIndex,
		beforeLoad: function( event, ui ) {
			$(ui.panel).html("<p>Loading...</p>");
		}
	});
});

function initAutoComplete(formElem){
	$("#"+formElem).autocomplete({
		source: function( request, response ){
			$.ajax({
				url: "../api/checklists/clspeciessuggest.php",
				dataType: "json",
				data: {
					term : request.term,
					cl : $('#clvalue').val() 
				},
				success: function(data) {
					response(data);
				}
			});
        },
		minLength: 3 
	});
}

function linkVoucher(occidIn, clidIn){
	$.ajax({
		type: "POST",
		url: "../api/checklists/linkvoucher.php",
		data: { clid: clidIn, occid: occidIn, sciname: document.getElementById("tid-"+occidIn).value }
	}).done(function( msg ) {
		if(msg === 1){
			alert("Voucher linked successfully!");
		}
		else if(msg === 2){
			alert("Specimen already a voucher for checklist");
		}
		else{
			alert("Voucher link failed: "+msg);
		}
	});
}

function validateSqlFragForm(f){
	if(!isNumeric(f.latnorth.value) || !isNumeric(f.latsouth.value) || !isNumeric(f.lngwest.value) || !isNumeric(f.lngeast.value)){
		alert("Latitude and longitudes values muct be numeric values only");
		return false;
	}
	return true;
}

function validateBatchNonVoucherForm(){
	const dbElements = document.getElementsByName("occids[]");
	for(let i = 0; i < dbElements.length; i++){
		const dbElement = dbElements[i];
		if(dbElement.checked) {
			return true;
		}
	}
   	alert("Please select at least one record to link as a voucher!");
  	return false;
}

function validateBatchMissingForm(){
	const dbElements = document.getElementsByName("occids[]");
	for(let i = 0; i < dbElements.length; i++){
		const dbElement = dbElements[i];
		if(dbElement.checked) {
			return true;
		}
	}
   	alert("Please select at least one record to link as a voucher!");
  	return false;
}


function selectAll(cb){
	let boxesChecked = true;
	if(!cb.checked){
		boxesChecked = false;
	}
	const cName = cb.className;
	const dbElements = document.getElementsByName("occids[]");
	for(let i = 0; i < dbElements.length; i++){
		const dbElement = dbElements[i];
		if(dbElement.className === cName){
			dbElement.checked = boxesChecked;
		}
		else{
			dbElement.checked = false;
		}
	}
}

function openPopup(urlStr,windowName){
	let wWidth = 900;
	if(document.getElementById('innertext').offsetWidth){
		wWidth = document.getElementById('innertext').offsetWidth*1.05;
	}
	else if(document.body.offsetWidth){
		wWidth = document.body.offsetWidth*0.9;
	}
	let newWindow = window.open(urlStr, windowName, 'scrollbars=1,toolbar=1,resizable=1,width=' + (wWidth) + ',height=600,left=20,top=20');
	if (newWindow.opener == null) {
		newWindow.opener = self;
	}
	return false;
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
