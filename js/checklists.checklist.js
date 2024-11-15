document.addEventListener("DOMContentLoaded", function() {
	$("#taxonfilter").autocomplete({
		source: function( request, response ) {
			$.getJSON( "../api/checklists/clsearchsuggest.php", { term: request.term, cl: clid }, response );
		}
	},
	{ minLength: 3 });

	$("#speciestoadd").autocomplete({
		source: function( request, response ) {
			$.getJSON( "../api/taxa/autofillsciname.php", {
				term: request.term,
				limit: 10,
				hideauth: true
			}, response );
		}
	},{ minLength: 3 });
});

function toggleVoucherDiv(tid){
	toggle("voucdiv-"+tid);
	toggle("morevouch-"+tid);
	toggle("lessvouch-"+tid);
	return false;
}

function showImagesChecked(f){
	if(f.showimages.checked){
		document.getElementById("wordicondiv").style.display = "none";
		f.showvouchers.checked = false;
		document.getElementById("showvouchersdiv").style.display = "none"; 
		f.showauthors.checked = false;
		document.getElementById("showauthorsdiv").style.display = "none"; 
	}
	else{
		document.getElementById("wordicondiv").style.display = "block";
		document.getElementById("showvouchersdiv").style.display = "block"; 
		document.getElementById("showauthorsdiv").style.display = "block"; 
	}
}

function validateAddSpecies(f){
	const sciName = f.speciestoadd.value;
	if(sciName === ""){
		alert("Enter the scientific name of species you wish to add");
		return false;
	}
	else{
		const http = new XMLHttpRequest();
		const url = "../api/taxa/gettid.php";
		const params = 'sciname=' + sciName;
		//console.log(url+'?'+params);
		http.open("POST", url, true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.onreadystatechange = function() {
			if(http.readyState === 4 && http.status == 200) {
				const testTid = http.responseText;
				if(!testTid){
					alert("ERROR: Scientific name does not exist in database. Did you spell it correctly? If so, contact your data administrator to add this species to the Taxonomic Thesaurus.");
				}
				else{
					f.tidtoadd.value = testTid;
					f.submit();
				}
			}
		};
		http.send(params);
		return false;
	}
}

function changeOptionFormAction(action,target){
	document.optionform.action = action;
	document.optionform.target = target;
}
