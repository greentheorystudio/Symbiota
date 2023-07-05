document.addEventListener("DOMContentLoaded", function() {
	$( "#targettaxon" ).autocomplete({
		source: "../api/search/gettaxasuggest.php",
		minLength: 3,
		focus: function( event, ui ) {
			$( "#targettaxon" ).val(ui.item.label);
			return false;
		},
		select: function( event, ui ) {
			$( "#targettid" ).val(ui.item.value);
			return false;
		}
	});
});

function verifyEditForm(f){
	if(f.url.value.replace(/\s/g, "") === "" ){
		window.alert("ERROR: File path must be entered");
		return false;
	}
	return true;
}

function verifyChangeTaxonForm(f){
	const sciName = f.targettaxon.value;
	const tid = f.targettid.value;
	if(sciName === ""){
		alert("Enter a taxon name to which the image will be transferred");
	}
	else if(tid === ""){
		alert("Taxon name not found in the taxonomic thesaurus");
	}
	else{
		f.submit();
	}
}

function openOccurrenceSearch(target) {
	let occWindow = open("../collections/misc/occurrencesearch.php?targetid=" + target, "occsearch", "resizable=1,scrollbars=0,width=750,height=500,left=20,top=20");
	if (occWindow.opener == null) {
		occWindow.opener = self;
	}
}
