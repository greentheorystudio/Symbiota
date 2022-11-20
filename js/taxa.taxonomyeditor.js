let rankLimit;
let rankLow;
let rankHigh;

$(document).ready(function() {

	$('#tabs').tabs({active: tabIndex});

	$("#parentstr").autocomplete({
		source: function( request, response ) {
			$.getJSON( "../../api/taxa/autofillsciname.php", {
				term: request.term,
				limit: 10,
				rhigh: document.taxoneditform.rankid.value
			}, response );
		},
		minLength: 3,
		autoFocus: true
	});

	$("#aefacceptedstr").autocomplete({
		source: function( request, response ) {
			$.getJSON( "../../api/taxa/autofillsciname.php", { term: request.term, acceptedonly: 1 }, response );
		},
		select: function( event, ui ) {
			document.getElementById('aeftidaccepted').value = ui.item.id;
		},
		minLength: 3,
		autoFocus: true
	});

	$("#ctnafacceptedstr").autocomplete({
		source: function( request, response ) {
			$.getJSON( "../../api/taxa/autofillsciname.php", { term: request.term, acceptedonly: 1 }, response );
		},
		select: function( event, ui ) {
			document.getElementById('ctnaftidaccepted').value = ui.item.id;
		},
		minLength: 3,
		autoFocus: true
	});
});

function toggleEditFields(){
  	toggle('editfield');
	toggle('kingdomdiv');
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
		url: "../../api/taxa/gettid.php",
		data: { sciname: f.acceptedstr.value }
	}).done(function( msg ) {
		if(!msg){
			alert("ERROR: Accepted taxon not found in thesaurus. It is either misspelled or needs to be added to the thesaurus.");
		}
		else{
			f.tidaccepted.value = msg;
			f.submit();
		}
	});
}

function submitUpperTaxForm(f){
	$.ajax({
		type: "POST",
		url: "../../api/taxa/gettid.php",
		data: { sciname: f.parentstr.value }
	}).done(function( msg ) {
		if(!msg){
			alert("ERROR: Parent taxon not found in thesaurus. It is either misspelled or needs to be added to the thesaurus.");
		}
		else{
			f.parenttid.value = msg;
			f.submit();
		}
	});
}
