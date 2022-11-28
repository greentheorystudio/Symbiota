$(document).ready(function() {
	$( "#family_answer" )
		.bind( "keydown", function( event ) {
			if ( event.keyCode == $.ui.keyCode.TAB &&
					$( this ).data( "autocomplete" ).menu.active ) {
				event.preventDefault();
			}
		})
		.autocomplete({
			source: function( request, response ) {
				$.getJSON( "../../api/games/ootdfamilylist.php", {
					term: request.term.split( /,\s*/ ).pop(), t: function() { return document.answers.family_answer.value; }
				}, response );
			},
			search: function() {
				const term = this.value.split( /,\s*/ ).pop();
				if ( term.length < 4 ) {
					return false;
				}
			},
			focus: function() {
				return false;
			},
			select: function( event, ui ) {
				const terms = this.value.split( /,\s*/ );
				terms.pop();
				terms.push( ui.item.label );
				document.getElementById('family_answer').value = ui.item.value;
				this.value = terms;
				return false;
			}
		},{});
		
	$( "#sciname_answer" )
		.bind( "keydown", function( event ) {
			if ( event.keyCode == $.ui.keyCode.TAB &&
					$( this ).data( "autocomplete" ).menu.active ) {
				event.preventDefault();
			}
		})
		.autocomplete({
			source: function( request, response ) {
				$.getJSON( "../../api/games/ootdscinamelist.php", {
					term: request.term.split( /,\s*/ ).pop(), t: function() { return document.answers.sciname_answer.value; }
				}, response );
			},
			search: function() {
				const term = this.value.split( /,\s*/ ).pop();
				if ( term.length < 4 ) {
					return false;
				}
			},
			focus: function() {
				return false;
			},
			select: function( event, ui ) {
				const terms = this.value.split( /,\s*/ );
				terms.pop();
				terms.push( ui.item.label );
				document.getElementById('sciname_answer').value = '';
				this.value = terms;
				return false;
			}
		},{});
		
});
