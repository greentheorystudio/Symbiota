$(document).ready(function() {
	$('#tabs').tabs({ active: tabIndex });
});

function selectAll(cb){
	let boxesChecked = true;
	if(!cb.checked){
		boxesChecked = false;
	}
	const dbElements = document.getElementsByName("occid[]");
	for(let i = 0; i < dbElements.length; i++){
		const dbElement = dbElements[i];
		dbElement.checked = boxesChecked;
	}
}

function ProcessReport(){
	if(document.pressed === 'invoice'){
		document.reportsform.action ="reports/defaultinvoice.php";
	}
	else if(document.pressed === 'spec'){
		document.reportsform.action ="reports/defaultspecimenlist.php";
	}
	else if(document.pressed === 'label'){
		document.reportsform.action ="reports/defaultmailinglabel.php";
	}
	else if(document.pressed === 'envelope'){
		document.reportsform.action ="reports/defaultenvelope.php";
	}
	if(document.getElementById("printbrowser").checked){
		document.reportsform.target = "_blank";
	}
	if(document.getElementById("printdoc").checked){
		document.reportsform.target = "_self";
	}
	return true;
}

function displayNewLoanOut(){
	if(document.getElementById("loanoutToggle")){
		toggle('newloanoutdiv');
	}
	const f = document.newloanoutform;
	if(f.loanidentifierown.value === ""){
		generateNewId(f.collid.value,f.loanidentifierown,"out");
	}
}

function displayNewLoanIn(){
	if(document.getElementById("loaninToggle")){
		toggle('newloanindiv');
	}
	const f = document.newloaninform;
	if(f.loanidentifierborr.value === ""){
		generateNewId(f.collid.value,f.loanidentifierborr,"in");
	}
}

function displayNewExchange(){
	if(document.getElementById("exchangeToggle")){
		toggle('newexchangediv');
	}
	const f = document.newexchangegiftform;
	if(f.identifier.value === ""){
		generateNewId(f.collid.value,f.identifier,"ex");
	}
}

function generateNewId(collId,targetObj,idType){
	const http = new XMLHttpRequest();
	const url = "../../api/occurrenceloans/generatenextid.php";
	let params = 'idtype=' + idType + '&collid=' + collId;
	//console.log(url+'?'+params);
	http.open("POST", url, true);
	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http.onreadystatechange = function() {
		if(http.readyState === 4 && http.status === 200) {
			targetObj.value = http.responseText;
		}
	};
	http.send(params);
}

function verfifyLoanOutAddForm(f){
	if(f.reqinstitution.options[f.reqinstitution.selectedIndex].value == 0){
		alert("Select an institution");
		return false;
	}
	if(f.loanidentifierown.value === ""){
		alert("Enter a loan identifier");
		return false;
	}
	return true;
}

function verifyLoanInAddForm(f){
	if(f.iidowner.options[f.iidowner.selectedIndex].value == 0){
		alert("Select an institution");
		return false;
	}
	if(f.loanidentifierborr.value === ""){
		alert("Enter a loan identifier");
		return false;
	}
	return true;
}

function verfifyExchangeAddForm(f){
	if(f.iid.options[f.iid.selectedIndex].value == 0){
		alert("Select an institution");
		return false;
	}
	if(f.identifier.value === ""){
		alert("Enter an exchange identifier");
		return false;
	}
	return true;
}

function verifySpecEditForm(f){
	let cbChecked = false;
	const dbElements = document.getElementsByName("occid[]");
	for(let i = 0; i < dbElements.length; i++){
		const dbElement = dbElements[i];
		if(dbElement.checked){
			cbChecked = true;
			break;
		}
	}
	if(!cbChecked){
		alert("Please select records to which you wish to apply the action");
		return false;
	}

	const applyTaskObj = f.applytask;
	const l = applyTaskObj.length;
	let applyTaskValue = "";
	for(let i = 0; i < l; i++) {
		if(applyTaskObj[i].checked) {
			applyTaskValue = applyTaskObj[i].value;
		}
	}
	if(applyTaskValue === "delete"){
		return confirm("Are you sure you want to remove selected records from this loan?");
	}

	return true;
}

function addSpecimen(f,splist){
	const catalogNumber = f.catalognumber.value;
	const loanid = f.loanid.value;
	const collid = f.collid.value;
	if(!catalogNumber){
		alert("Please enter a catalog number!");
		return false;
	}
	else{
		const http = new XMLHttpRequest();
		const url = "../../api/occurrenceloans/insertloanspecimens.php";
		let params = 'loanid=' + loanid + '&catalognumber=' + catalogNumber + '&collid=' + collid;
		//console.log(url+'?'+params);
		http.open("POST", url, true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.onreadystatechange = function() {
			if(http.readyState === 4 && http.status === 200) {
				responseCode = Number(http.responseText);
				if (responseCode === 0) {
					document.getElementById("addspecsuccess").style.display = "none";
					document.getElementById("addspecerr1").style.display = "block";
					document.getElementById("addspecerr2").style.display = "none";
					document.getElementById("addspecerr3").style.display = "none";
					document.getElementById("addspecerr1").style.display = "none";
				}
				else if (responseCode === 1) {
					document.getElementById("addspecsuccess").style.display = "block";
					document.getElementById("addspecerr1").style.display = "none";
					document.getElementById("addspecerr2").style.display = "none";
					document.getElementById("addspecerr3").style.display = "none";
					document.getElementById("addspecsuccess").style.display = "none";
					if(splist === 0){
						document.getElementById("speclistdiv").style.display = "block";
						document.getElementById("nospecdiv").style.display = "none";
					}
				}
				else if (responseCode === 2) {
					document.getElementById("addspecsuccess").style.display = "none";
					document.getElementById("addspecerr1").style.display = "none";
					document.getElementById("addspecerr2").style.display = "block";
					document.getElementById("addspecerr3").style.display = "none";
					document.getElementById("addspecerr2").style.display = "none";
				}
				else if (responseCode === 3) {
					document.getElementById("addspecsuccess").style.display = "none";
					document.getElementById("addspecerr1").style.display = "none";
					document.getElementById("addspecerr2").style.display = "none";
					document.getElementById("addspecerr3").style.display = "block";
					document.getElementById("addspecerr3").style.display = "none";
				}
				else {
					f.catalognumber.value = "";
					document.refreshspeclist.emode.value = 1;
					document.refreshspeclist.submit();
				}
				return true;
			}
			else{
				return false;
			}
		};
		http.send(params);
	}
}

function openIndPopup(occid){
	openPopup('../individual/index.php?occid=' + occid);
}

function openEditorPopup(occid){
	openPopup('../editor/occurrenceeditor.php?occid=' + occid);
}

function verifyDate(eventDateInput){
	const dateStr = eventDateInput.value;
	if(dateStr === "") return true;

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
				alert("The date you entered has not happened yet. Please revise.");
				return false;
			}
		}
		catch(e){
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

function verifyDueDate(eventDateInput){
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
			if(testDate < today){
				alert("The due date you entered has already passed. Please revise.");
				return false;
			}
		}
		catch(e){
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

function outIdentCheck(collid){
	const loanoutidentelement = document.getElementById('loanidentifierown');
	const loanidentifierown = loanoutidentelement.value;
	if(loanidentifierown.length === 0){
  		return;
  	}
	const http = new XMLHttpRequest();
	const url = "../../api/occurrenceloans/loanoutidentifiercheck.php";
	let params = 'ident=' + loanidentifierown + '&collid=' + collid;
	//console.log(url+'?'+params);
	http.open("POST", url, true);
	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http.onreadystatechange = function() {
		if(http.readyState === 4 && http.status === 200) {
			if(http.responseText){
				loanoutidentelement.value = "";
				alert("There is already a loan with that identifier, please enter a different one.");
			}
		}
	};
	http.send(params);
}

function inIdentCheck(collid){
	const loaninidentelement = document.getElementById('loanidentifierborr');
	const loanidentifierborr = loaninidentelement.value;
	if (loanidentifierborr.length === 0){
  		return;
  	}
	const http = new XMLHttpRequest();
	const url = "../../api/occurrenceloans/loaninidentifiercheck.php";
	let params = 'ident=' + loanidentifierborr + '&collid=' + collid;
	//console.log(url+'?'+params);
	http.open("POST", url, true);
	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http.onreadystatechange = function() {
		if(http.readyState === 4 && http.status === 200) {
			console.log(http.responseText);
			if(http.responseText){
				loaninidentelement.value="";
				alert("There is already a loan with that identifier, please enter a different one.");
			}
		}
	};
	http.send(params);
}

function exIdentCheck(collid){
	const exidentelement = document.getElementById('identifier');
	const identifier = exidentelement.value;
	if (identifier.length === 0){
  		return;
  	}
	const http = new XMLHttpRequest();
	const url = "../../api/occurrenceloans/exidentifiercheck.php";
	let params = 'ident=' + identifier + '&collid=' + collid;
	//console.log(url+'?'+params);
	http.open("POST", url, true);
	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http.onreadystatechange = function() {
		if(http.readyState === 4 && http.status === 200) {
			console.log(http.responseText);
			if(http.responseText){
				exidentelement.value="";
				alert("There is already a transaction with that identifier, please enter a different one.");
			}
		}
	};
	http.send(params);
}

function verifyLoanDet(){
	if(document.getElementById('dafsciname').value === ""){
		alert("Scientific Name field must have a value");
		return false;
	}
	if(document.getElementById('identifiedby').value === ""){
		alert("Determiner field must have a value (enter 'unknown' if not defined)");
		return false;
	}
	if(document.getElementById('dateidentified').value === ""){
		alert("Determination Date field must have a value (enter 'unknown' if not defined)");
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

function initLoanDetAutocomplete(f){
	$( f.sciname ).autocomplete({ 
		source: "../../api/taxa/getspeciessuggest.php",
		minLength: 3,
		change: function() {
			if(f.sciname.value){
				pauseSubmit = true;
				verifyLoanDetSciName(f);
			}
			else{
				f.scientificnameauthorship.value = "";
				f.family.value = "";
				f.tidtoadd.value = "";
			}				
		}
	});
}

function verifyLoanDetSciName(f){
	const http = new XMLHttpRequest();
	const url = "../../api/taxa/verifysciname.php";
	let params = 'term=' + f.sciname.value;
	//console.log(url+'?'+params);
	http.open("POST", url, true);
	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http.onreadystatechange = function() {
		if(http.readyState === 4 && http.status === 200) {
			const data = JSON.parse(http.responseText);
			if(data.hasOwnProperty('tid')){
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
		}
	};
	http.send(params);
}
