$(document).ready(function() {
	$("#taxonfilter").autocomplete({ 
		source: "rpc/getTaxonFilter.php", 
		dataType: "json",
		minLength: 3,
		select: function( event, ui ) {
			$("#tidfilter").val(ui.item.id);
		}
	});

	$("#taxonfilter").change(function(){
		$("#tidfilter").val("");
		if($( this ).val() !== ""){
			$( "#filtersubmit" ).prop( "disabled", true );
			$( "#verify-span" ).show();
			$( "#notvalid-span" ).hide();
								
			$.ajax({
				type: "POST",
				url: "rpc/getTaxonFilter.php",
				data: { term: $( this ).val(), exact: 1 }
			}).done(function( msg ) {
				if(msg === ""){
					$( "#notvalid-span" ).show();
				}
				else{
					$("#tidfilter").val(msg[0].id);
				}
				$( "#filtersubmit" ).prop( "disabled", false );
				$( "#verify-span" ).hide();
			});
		}
	});
	
});

function traitChanged(traitID){
	const el_id = 'stateid-' + traitID;
	const inputElement = document.getElementById(el_id);
	$('input[name="stateid-'+traitID+'[]"]').each(function(){
		if(inputElement.checked == true){
			$("div.child-"+this.value).show();
		}
		else{
			$("div.child-"+this.value).hide();
			$("input:checkbox.child-"+this.value).each(function(){
				inputElement.checked = false;
			});
			$("input:radio.child-"+this.value).each(function(){
				inputElement.checked = false;
			});
		}
	});
	$('input[name="submitform"]').prop('disabled', false);
}
