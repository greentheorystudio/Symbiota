$(document).ready(function() {
	$('#tabs').tabs({
		beforeLoad: function( event, ui ) {
			$(ui.panel).html("<p>Loading...</p>");
		}
	});

	resetLanguageSelect(document.searchform);

	$( "#taxagroup" )
		.bind( "keydown", function( event ) {
			if ( event.keyCode == $.ui.keyCode.TAB && $( this ).data( "ui-autocomplete" ).menu.active ) {
				event.preventDefault();
			}
		})
		.autocomplete({
			source: function( request, response ) {
				let reqTerm = request.term;
				reqTerm = reqTerm.split( /,\s*/ );
				reqTerm = reqTerm.pop();
				$.getJSON( "rpc/taxalist.php", {
					term: reqTerm, t: 'single'
				}, response );
			},
			focus: function() {
				return false;
			},
			select: function( event, ui ) {
				const terms = this.value.split(/,\s*/);
				terms.pop();
				terms.push( ui.item.label );
				document.getElementById('tid').value = ui.item.value;
				this.value = terms;
				return false;
			},
			change: function (event, ui) {
				if(!ui.item && this.value !== "") {
					document.getElementById('tid').value = '';
					alert("You must select a name from the list.");
				}
				else if (document.getElementById(ui.item.label)) {
					this.value = '';
					document.getElementById('tid').value = '';
					alert("Taxonomic group has already been added.");
				}
			}
		},{});
});

function resetLanguageSelect(f){
	if($("#searchlanguage").is('select')){
		let tid = f.searchtaxa.value;
		if(tid === '') {
			tid = 0;
		}
		const oldLang = $("#searchlanguage").val();
		$("#searchlanguage").empty();
		$.each(langArr[tid], function(key,value) {
			$("#searchlanguage").append($("<option></option>").attr("value", value).text(value));
		});
		$("#searchlanguage").val(oldLang);
	}
}

function addNewLang(f){
	const newLangStr = f.newlang.value;
	const langObjId = f.language.id;
	if(newLangStr){ 
		$("#" + langObjId).append($("<option></option>").attr("value", newLangStr).text(newLangStr));
		$("#" + langObjId).val(newLangStr);
	}
	toggle('addLangDiv');
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
