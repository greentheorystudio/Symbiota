$(document).ready(function() {
	function split( val ) {
		return val.split( /,\s*/ );
	}
	function extractLast( term ) {
		return split( term ).pop();
	}

	$( "#family_answer" )
		.bind( "keydown", function( event ) {
			if ( event.keyCode === $.ui.keyCode.TAB &&
					$( this ).data( "autocomplete" ).menu.active ) {
				event.preventDefault();
			}
		})
		.autocomplete({
			source: function( request, response ) {
				$.getJSON( "rpc/ootdfamilylist.php", {
					term: extractLast( request.term ), t: function() { return document.answers.family_answer.value; }
				}, response );
			},
			search: function() {
				const term = extractLast(this.value);
				if ( term.length < 4 ) {
					return false;
				}
			},
			focus: function() {
				return false;
			},
			select: function( event, ui ) {
				const terms = split(this.value);
				terms.pop();
				terms.push( ui.item.label );
				document.getElementById('family_answer').value = ui.item.value;
				this.value = terms;
				return false;
			}
		},{});
		
	$( "#sciname_answer" )
		.bind( "keydown", function( event ) {
			if ( event.keyCode === $.ui.keyCode.TAB &&
					$( this ).data( "autocomplete" ).menu.active ) {
				event.preventDefault();
			}
		})
		.autocomplete({
			source: function( request, response ) {
				$.getJSON( "rpc/ootdscinamelist.php", {
					term: extractLast( request.term ), t: function() { return document.answers.sciname_answer.value; }
				}, response );
			},
			search: function() {
				const term = extractLast(this.value);
				if ( term.length < 4 ) {
					return false;
				}
			},
			focus: function() {
				return false;
			},
			select: function( event, ui ) {
				const terms = split(this.value);
				terms.pop();
				terms.push( ui.item.label );
				document.getElementById('sciname_answer').value = '';
				this.value = terms;
				return false;
			}
		},{});
		
});
