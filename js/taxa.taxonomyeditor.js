let rankLimit;
let rankLow;
let rankHigh;

document.addEventListener("DOMContentLoaded", function() {

	$('#tabs').tabs({active: tabIndex});

	$("#parentstr").autocomplete({
		source: function( request, response ) {
			$.getJSON( "../../api/taxa/autofillsciname.php", {
				term: request.term,
				acceptedonly: 1,
				limit: 10,
				rhigh: document.taxoneditform.rankid.value
			}, response );
		},
		select: function( event, ui ) {
			document.getElementById('parenttid').value = ui.item.id;
		},
		change: function (event, ui) {
			if(!ui.item){
				this.value = '';
				document.getElementById('parenttid').value = '';
				alert("You must select a Parent Taxon from the list");
			}
		},
		minLength: 3,
		autoFocus: true
	});

	$("#aefacceptedstr").autocomplete({
		source: function( request, response ) {
			$.getJSON( "../../api/taxa/autofillsciname.php", { term: request.term, acceptedonly: 1, limit: 10 }, response );
		},
		select: function( event, ui ) {
			document.getElementById('aeftidaccepted').value = ui.item.id;
		},
		change: function (event, ui) {
			if(!ui.item){
				this.value = '';
				document.getElementById('aeftidaccepted').value = '';
				alert("You must select an Accepted Taxon from the list");
			}
		},
		minLength: 3,
		autoFocus: true
	});

	$("#ctnafacceptedstr").autocomplete({
		source: function( request, response ) {
			$.getJSON( "../../api/taxa/autofillsciname.php", { term: request.term, acceptedonly: 1, limit: 10 }, response );
		},
		select: function( event, ui ) {
			document.getElementById('ctnaftidaccepted').value = ui.item.id;
		},
		change: function (event, ui) {
			if(!ui.item){
				this.value = '';
				document.getElementById('ctnaftidaccepted').value = '';
				alert("You must select an Accepted Taxon from the list");
			}
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
	if(f.tidaccepted.value === ''){
		alert("Please enter an Accepted Name");
	}
	else{
		f.submit();
	}
}

function submitUpperTaxForm(f){
	if(f.parenttid.value === ''){
		alert("Please enter an Parent Taxon");
	}
	else{
		f.submit();
	}
}
