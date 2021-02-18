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

function togglePoint(f){
	const objDiv = document.getElementById('pointlldiv');
	if(objDiv){
		if(f.pointtid.value === ""){
			objDiv.style.display = "none";
		}
		else{
			objDiv.style.display = "block";
		}
	}
}

function verifyPointAddForm(f){
	if(f.pointtid.value === ""){
		alert("Please select a taxon");
		return false;
	}
	if(f.pointlat.value === "" || f.pointlng.value === ""){
		alert("Please enter coordinates");
		return false;
	}
	return true;
}

function showImagesDefaultChecked(f){
	if(f.dimages.checked){
		f.dvouchers.checked = false;
		f.dvouchers.disabled = true;
		f.dauthors.checked = false;
		f.dauthors.disabled = true;
	}
	else{
		f.dvouchers.disabled = false; 
		f.dauthors.disabled = false; 
	}
}
