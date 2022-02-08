let rankLimit;
let rankLow;
let rankHigh;

$(document).ready(function() {

	$('#tabs').tabs({active: tabIndex});

	$("#parentstr").autocomplete({
		source: function( request, response ) {
			$.getJSON( "rpc/gettaxasuggest.php", { term: request.term, rhigh: document.taxoneditform.rankid.value }, response );
		},
		minLength: 3,
		autoFocus: true
	});

	$("#aefacceptedstr").autocomplete({ 
		source: "rpc/getacceptedsuggest.php",
		minLength: 3,
		autoFocus: true
	});

	$("#ctnafacceptedstr").autocomplete({ 
		source: "rpc/getacceptedsuggest.php",
		minLength: 3,
		autoFocus: true
	});
});

function toggleEditFields(){
  	toggle('editfield');
	toggle('kingdomdiv');
}

function toggle(target){
	const ele = document.getElementById(target);
	if(ele){
		if(ele.style.display === "none"){
			ele.style.display="";
  		}
	 	else {
	 		ele.style.display="none";
	 	}
	}
	else{
		const divs = document.getElementsByTagName("div");
		for(let i = 0; i < divs.length; i++) {
			const divObj = divs[i];
			if(divObj.className == target){
				if(divObj.style.display === "none"){
					divObj.style.display="block";
				}
			 	else {
			 		divObj.style.display="none";
			 	}
			}
		}

		const spans = document.getElementsByTagName("span");
		for(let j = 0; j < spans.length; j++) {
			const spanObj = spans[j];
			if(spanObj.className == target){
				if(spanObj.style.display === "none"){
					spanObj.style.display="inline";
				}
			 	else {
			 		spanObj.style.display="none";
			 	}
			}
		}
	}
}

function validateTaxonEditForm(f){
	if(f.unitname1.value.trim() === ""){
		alert('Unitname 1 field must have a value');
		return false;
	}
	return true;
}

function verifyAcceptEditsForm(f){
	if(f.acceptedstr.value === ""){
		alert("Please enter an accepted name to link this name to!");
		return false;
	}
	submitLinkToAccepted(f);
	return false;
}

function verifyChangeToNotAcceptedForm(f){
	if(f.acceptedstr.value === ""){
		alert("Please enter an accepted name to link this name to!");
		return false;
	}
	submitLinkToAccepted(f);
	return false;
}

function submitLinkToAccepted(f){
	$.ajax({
		type: "POST",
		url: "rpc/gettid.php",
		data: { sciname: f.acceptedstr.value }
	}).done(function( msg ) {
		if(msg == 0){
			alert("ERROR: Accepted taxon not found in thesaurus. It is either misspelled or needs to be added to the thesaurus.");
		}
		else{
			f.tidaccepted.value = msg;
			f.submit();
		}
	});
}

function submitTaxStatusForm(f){
	$.ajax({
		type: "POST",
		url: "rpc/gettid.php",
		data: { sciname: f.parentstr.value }
	}).done(function( msg ) {
		if(msg == 0){
			alert("ERROR: Parent taxon not found in thesaurus. It is either misspelled or needs to be added to the thesaurus.");
		}
		else{
			f.parenttid.value = msg;
			f.submit();
		}
	});
}
