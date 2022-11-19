var sciNameValid = false;
var unitName1Valid = false;
var rankIdValid = false;
var parentNameValid = false;
var parentIdValid = false;
var acceptedNameValid = false;
var acceptedIdValid = false;
var taxonExistsValid = false;

$(document).ready(function() {
	$("#acceptedstr").autocomplete({ 
		source: "../../api/taxa/getacceptedsuggest.php",
		minLength: 2, 
		autoFocus: true 
	});
	
	$("#parentname").autocomplete({
		source: function( request, response ) {
			$.getJSON( "../../api/taxa/autofillsciname.php", { term: request.term, rhigh: $("#rankid").val() }, response );
		},
		change: function() {
			checkParentExistance(document.loaderform);
		},
		minLength: 2,
		autoFocus: true
	});
});

function clearValidations(){
	document.getElementById("submitButton").disabled = true;
	sciNameValid = false;
	unitName1Valid = false;
	rankIdValid = false;
	parentNameValid = false;
	parentIdValid = false;
	acceptedNameValid = false;
	acceptedIdValid = false;
	taxonExistsValid = false;
}

function checkValidations(){
	if(sciNameValid && unitName1Valid && rankIdValid && parentNameValid && parentIdValid && acceptedNameValid && acceptedIdValid && taxonExistsValid){
		document.getElementById("submitButton").disabled = false;
	}
}

function validateLoadForm(f){
	clearValidations();
	if(f.sciname.value === ""){
		alert("Scientific Name field required.");
	}
	else{
		sciNameValid = true;
	}

	if(f.unitname1.value === ""){
		alert("Unit Name 1 (genus or uninomial) field required.");
	}
	else{
		unitName1Valid = true;
	}

	const rankId = Number(f.rankid.value);
	if(rankId === ""){
		alert("Taxon rank field required.");
	}
	else{
		rankIdValid = true;
	}

	if(f.parentname.value === "" && rankId > 10){
		alert("Parent taxon required");
	}
	else{
		parentNameValid = true;
	}

	if(f.parenttid.value === "" && rankId > 10){
		checkParentExistance(f);
	}
	else{
		parentIdValid = true;
	}

	const accStatusObj = f.acceptstatus;
	if(accStatusObj[0].checked === false){
		if(f.acceptedstr.value === ""){
			alert("Accepted name needs to have a value");
		}
		else{
			acceptedNameValid = true;
		}

		if(f.tidaccepted.value === ""){
			checkAcceptedExistance(f);
		}
		else{
			acceptedIdValid = true;
		}
	}
	else{
		acceptedNameValid = true;
		acceptedIdValid = true;
	}

	$.ajax({
		type: "POST",
		url: "../../api/taxa/gettid.php",
		async: false,
		data: { sciname: f.sciname.value, rankid: f.rankid.value, author: f.author.value }
	}).done(function( msg ) {
		if(msg){
			const sciName = document.getElementById("sciname").value;
			alert("Taxon "+sciName+" "+f.author.value+" ("+msg+") already exists in database");
		}
		else{
			taxonExistsValid = true;
			checkValidations();
		}
	});
}

function parseName(f){
	let sciName = f.sciname.value;
	sciName = sciName.replaceAll(/^\s+|\s+$/g,"");
	f.reset();
	f.sciname.value = sciName;
	let sciNameArr;
	let activeIndex = 0;
	let unitName1 = "";
	let unitName2 = "";
	let rankId = "";
	sciNameArr = sciName.split(' ');

	if(sciNameArr[activeIndex].length === 1){
		f.unitind1.value = sciNameArr[activeIndex];
		f.unitname1.value = sciNameArr[activeIndex+1];
		unitName1 = sciNameArr[activeIndex+1];
		activeIndex = 2;
	}
	else{
		f.unitname1.value = sciNameArr[activeIndex];
		unitName1 = sciNameArr[activeIndex];
		activeIndex = 1;
	}
	if(sciNameArr.length > activeIndex){
		if(sciNameArr[activeIndex].length === 1){
			f.unitind2.value = sciNameArr[activeIndex];
			f.unitname2.value = sciNameArr[activeIndex+1];
			unitName2 = sciNameArr[activeIndex+1];
			activeIndex = activeIndex+2;
		}
		else{
			f.unitname2.value = sciNameArr[activeIndex];
			unitName2 = sciNameArr[activeIndex];
			activeIndex = activeIndex+1;
		}
		rankId = 220;
	}
	if (sciNameArr.length > activeIndex) {
		if (sciNameArr[activeIndex].substring(sciNameArr[activeIndex].length - 1, sciNameArr[activeIndex].length) === "." || sciNameArr[activeIndex].length === 1) {
			f.unitind3.value = sciNameArr[activeIndex];
			f.unitname3.value = sciNameArr[activeIndex + 1];
			if (sciNameArr[activeIndex] === "ssp." || sciNameArr[activeIndex] === "subsp.") {
				rankId = 230;
			}
			if (sciNameArr[activeIndex] === "var.") {
				rankId = 240;
			}
			if (sciNameArr[activeIndex] === "f.") {
				rankId = 260;
			}
			if (sciNameArr[activeIndex] === "x" || sciNameArr[activeIndex] === "X") {
				rankId = 220;
			}
		} else {
			f.unitname3.value = sciNameArr[activeIndex];
			rankId = 230;
		}
	}
	if(unitName1.length > 4 && (unitName1.indexOf("aceae") === (unitName1.length - 5) || unitName1.indexOf("idae") === (unitName1.length - 4))){
		rankId = 140;
	}
	f.rankid.value = rankId;
	if(rankId > 180){
		setParent(f);
	}
}

function setParent(f){
	const rankId = Number(f.rankid.value);
	const unitName1 = f.unitname1.value;
	const unitName2 = f.unitname2.value;
	let parentName = "";
	if(rankId === 220){
		parentName = unitName1; 
	}
	else if(rankId > 220){
		parentName = unitName1 + " " + unitName2; 
	}
	if(parentName){
		f.parentname.value = parentName;
		checkParentExistance(f);
	}
}			

function acceptanceChanged(f){
	const accStatusObj = f.acceptstatus;
	if(accStatusObj[0].checked){
		document.getElementById("accdiv").style.display = "none";
	}
	else{
		document.getElementById("accdiv").style.display = "block";
	}
}

function checkAcceptedExistance(f){
	if(f.acceptedstr.value){
		$.ajax({
			type: "POST",
			url: "../../api/taxa/gettid.php",
			async: false,
			data: { sciname: f.acceptedstr.value }
		}).done(function( msg ) {
			if(!msg){
				alert("Accepted name does not exist. Add parent to thesaurus before adding this name.");
			}
			else{
				if(msg.indexOf(",") === -1){
					f.tidaccepted.value = msg;
					acceptedIdValid = true;
					checkValidations();
				}
				else{
					alert("Accepted is matching two different names in the thesaurus. Please select taxon with the correct author.");
				}
			}
		});
	}
	else{
		return false;
	}
}

function checkParentExistance(f){
	const parentStr = f.parentname.value;
	if(parentStr){
		$.ajax({
			type: "POST",
			url: "../../api/taxa/gettid.php",
			async: false,
			data: { sciname: parentStr }
		}).done(function( msg ) {
			if(!msg){
				alert("Parent does not exist. Please first add parent to system.");
			}
			else{
				if(msg.indexOf(",") === -1){
					f.parenttid.value = msg;
					parentIdValid = true;
					checkValidations();
				}
				else{
					alert("Parent is matching two different names in the thesaurus. Please select taxon with the correct author.");
				}
			}
		});
	}
	else{
		return false;
	}
}
