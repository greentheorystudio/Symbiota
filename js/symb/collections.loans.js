$(document).ready(function() {
	if(!navigator.cookieEnabled){
		alert("Your browser cookies are disabled. To be able to login and access your profile, they must be enabled for this domain.");
	}

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
			if(divObj.className === target){
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

/**
 * @return {boolean}
 */
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
	let xmlHttp = GetXmlHttpObject();
	if (xmlHttp==null){
		alert ("Your browser does not support AJAX!");
		return false;
	}
	const url = "rpc/generatenextid.php?idtype=" + idType + "&collid=" + collId;
	xmlHttp.onreadystatechange=function(){
		if(xmlHttp.readyState === 4 && xmlHttp.status === 200){
			targetObj.value = xmlHttp.responseText;
		}
	};
	xmlHttp.open("POST",url,true);
	xmlHttp.send(null);
}

function verfifyLoanOutAddForm(f){
	if(f.reqinstitution.options[f.reqinstitution.selectedIndex].value === 0){
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
	if(f.iidowner.options[f.iidowner.selectedIndex].value === 0){
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
	if(f.iid.options[f.iid.selectedIndex].value === 0){
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
		alert("Please select specimens to which you wish to apply the action");
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
		return confirm("Are you sure you want to remove selected specimens from this loan?");
	}

	return true;
}

function addSpecimen(f,splist){
	const catalogNumber = f.catalognumber.value;
	const loanid = f.loanid.value;
	const collid = f.collid.value;
	let xmlHttp;
	if (!catalogNumber) {
		alert("Please enter a catalog number!");
		return false;
	} else {
		xmlHttp = GetXmlHttpObject();
		if (xmlHttp == null) {
			alert("Your browser does not support AJAX!");
			return false;
		}
		let url = "rpc/insertloanspecimens.php";
		url = url + "?loanid=" + loanid;
		url = url + "&catalognumber=" + catalogNumber;
		url = url + "&collid=" + collid;
		xmlHttp.onreadystatechange = function () {
			let responseCode;
			if (xmlHttp.readyState === 4 && xmlHttp.status === 200) {
				responseCode = xmlHttp.responseText;
				if (responseCode === "0") {
					document.getElementById("addspecsuccess").style.display = "none";
					document.getElementById("addspecerr1").style.display = "block";
					document.getElementById("addspecerr2").style.display = "none";
					document.getElementById("addspecerr3").style.display = "none";
					setTimeout(function () {
						document.getElementById("addspecerr1").style.display = "none";
					}, 750);
				} else if (responseCode === "1") {
					document.getElementById("addspecsuccess").style.display = "block";
					document.getElementById("addspecerr1").style.display = "none";
					document.getElementById("addspecerr2").style.display = "none";
					document.getElementById("addspecerr3").style.display = "none";
					setTimeout(function () {
						document.getElementById("addspecsuccess").style.display = "none";
					}, 750);
					if (splist === 0) {
						document.getElementById("speclistdiv").style.display = "block";
						document.getElementById("nospecdiv").style.display = "none";
					}
				} else if (responseCode === "2") {
					document.getElementById("addspecsuccess").style.display = "none";
					document.getElementById("addspecerr1").style.display = "none";
					document.getElementById("addspecerr2").style.display = "block";
					document.getElementById("addspecerr3").style.display = "none";
					setTimeout(function () {
						document.getElementById("addspecerr2").style.display = "none";
					}, 750);
				} else if (responseCode === "3") {
					document.getElementById("addspecsuccess").style.display = "none";
					document.getElementById("addspecerr1").style.display = "none";
					document.getElementById("addspecerr2").style.display = "none";
					document.getElementById("addspecerr3").style.display = "block";
					setTimeout(function () {
						document.getElementById("addspecerr3").style.display = "none";
					}, 750);
				} else {
					f.catalognumber.value = "";
					document.refreshspeclist.emode.value = 1;
					document.refreshspeclist.submit();
				}
			}
		};
		xmlHttp.open("POST", url, true);
		xmlHttp.send(null);
	}
	return false;
}

function openIndPopup(occid){
	openPopup('../individual/index.php?occid=' + occid);
}

function openEditorPopup(occid){
	openPopup('../editor/occurrenceeditor.php?occid=' + occid);
}

function openPopup(urlStr){
	let wWidth = 900;
	if(document.getElementById('maintable').offsetWidth){
		wWidth = document.getElementById('maintable').offsetWidth*1.05;
	}
	else if(document.body.offsetWidth){
		wWidth = document.body.offsetWidth*0.9;
	}
	let newWindow = window.open(urlStr, 'popup', 'scrollbars=1,toolbar=1,resizable=1,width=' + (wWidth) + ',height=600,left=20,top=20');
	if (newWindow.opener == null) {
		newWindow.opener = self;
	}
	return false;
}

function GetXmlHttpObject(){
	let xmlHttp;
	try{
		xmlHttp=new XMLHttpRequest();
	}
	catch (e){
		try{
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch(e){
			xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	return xmlHttp;
}

function verifyDate(eventDateInput){
	const dateStr = eventDateInput.value;
	if(dateStr === "") return true;

	const dateArr = parseDate(dateStr);
	if(dateArr['y'] === 0){
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
	if(dateArr['y'] === 0){
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

function acroCheck(){
	const acroelement = document.getElementById('institutioncode');
	const acronym = acroelement.value;
	if (acronym.length === 0){
  		return;
  	}
	let sutXmlHttp = GetXmlHttpObject();
	if (sutXmlHttp==null){
  		alert ("Your browser does not support AJAX!");
  		return;
  	}
	let url = "rpc/ariz_acrocheck.php";
	url=url+"?acronym="+acronym;
	sutXmlHttp.onreadystatechange=function(){
		if(sutXmlHttp.readyState === 4 && sutXmlHttp.status === 200){
			const responseArr = JSON.parse(sutXmlHttp.responseText);
			if(responseArr){
				acroelement.value="";
				alert("Institution already exists, please select it from drop down menu above.");
			}
		}
	};
	sutXmlHttp.open("POST",url,true);
	sutXmlHttp.send(null);
}

function outIdentCheck(collid){
	const loanoutidentelement = document.getElementById('loanidentifierown');
	const loanidentifierown = loanoutidentelement.value;
	if (loanidentifierown.length === 0){
  		return;
  	}
	let sutXmlHttp = GetXmlHttpObject();
	if (sutXmlHttp==null){
  		alert ("Your browser does not support AJAX!");
  		return;
  	}
	let url = "rpc/loanoutidentifiercheck.php";
	url = url+"?ident="+loanidentifierown;
	url = url+"&collid="+collid;
	sutXmlHttp.onreadystatechange=function(){
		if(sutXmlHttp.readyState === 4 && sutXmlHttp.status === 200){
			const responseArr = JSON.parse(sutXmlHttp.responseText);
			if(responseArr){
				loanoutidentelement.value="";
				alert("There is already a loan with that identifier, please enter a different one.");
			}
		}
	};
	sutXmlHttp.open("POST",url,true);
	sutXmlHttp.send(null);
}

function inIdentCheck(collid){
	const loaninidentelement = document.getElementById('loanidentifierborr');
	const loanidentifierborr = loaninidentelement.value;
	if (loanidentifierborr.length === 0){
  		return;
  	}
	let sutXmlHttp = GetXmlHttpObject();
	if (sutXmlHttp == null){
  		alert ("Your browser does not support AJAX!");
  		return;
  	}
	let url = "rpc/loaninidentifiercheck.php";
	url = url+"?ident="+loanidentifierborr;
	url = url+"&collid="+collid;
	sutXmlHttp.onreadystatechange=function(){
		if(sutXmlHttp.readyState === 4 && sutXmlHttp.status === 200){
			const responseArr = JSON.parse(sutXmlHttp.responseText);
			if(responseArr){
				loaninidentelement.value="";
				alert("There is already a loan with that identifier, please enter a different one.");
			}
		}
	};
	sutXmlHttp.open("POST",url,true);
	sutXmlHttp.send(null);
}

function exIdentCheck(collid){
	const exidentelement = document.getElementById('identifier');
	const identifier = exidentelement.value;
	if (identifier.length === 0){
  		return;
  	}
	let sutXmlHttp = GetXmlHttpObject();
	if (sutXmlHttp == null){
  		alert ("Your browser does not support AJAX!");
  		return;
  	}
	let url = "rpc/exidentifiercheck.php";
	url = url+"?ident="+identifier;
	url = url+"&collid="+collid;
	sutXmlHttp.onreadystatechange=function(){
		if(sutXmlHttp.readyState === 4 && sutXmlHttp.status === 200){
			const responseArr = JSON.parse(sutXmlHttp.responseText);
			if(responseArr){
				exidentelement.value="";
				alert("There is already a transaction with that identifier, please enter a different one.");
			}
		}
	};
	sutXmlHttp.open("POST",url,true);
	sutXmlHttp.send(null);
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
		source: "../editor/rpc/getspeciessuggest.php", 
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
	$.ajax({
		type: "POST",
		url: "../editor/rpc/verifysciname.php",
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
