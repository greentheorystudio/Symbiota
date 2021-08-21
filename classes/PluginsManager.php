<?php

class PluginsManager {

	private $conn;
    private $quickSearchScinamePlaceholder = 'Scientific Name';
    private $quickSearchCommonPlaceholder = 'Common Name';
    private $quickSearchShowSelector = false;
    private $quickSearchDefaultSetting = 'sciname';

	public function createQuickSearch($buttonText,$searchText = null): string
	{
		$searchTextCssDisplay = ($searchText?'block':'none');
        $selectorTextCssDisplay = ($this->quickSearchShowSelector?'flex':'none');
        $commonChecked = ($this->quickSearchDefaultSetting === 'common'?'checked':'');
        $initialPlaceholder = ($this->quickSearchDefaultSetting === 'sciname'?$this->quickSearchScinamePlaceholder:$this->quickSearchCommonPlaceholder);
        $clientRoot = $GLOBALS['CLIENT_ROOT'];
return <<<EOD
    <link href="$clientRoot/css/jquery-ui.css" type="text/css" rel="stylesheet" />
    <script type='text/javascript'>
        if(!window.jQuery){
            const jqresource = document.createElement("script");
            jqresource.src = "$clientRoot/js/jquery.js";
            document.getElementsByTagName("head")[0].appendChild(jqresource);
            jqresource.onload = function(){
                const jquiresource = document.createElement("script");
                jquiresource.src = "$clientRoot/js/jquery-ui.js";
                document.getElementsByTagName("head")[0].appendChild(jquiresource);
                jquiresource.onload = function() {
                    initializeQuickSearch();
                };
            };
        }
        else{
            $(document).ready(function() {
                $("#quicksearchtaxon").autocomplete({
                    source: function( request, response ) {
                        var quicksearchcommonselectorchecked = document.quicksearch.quicksearchselector.checked;
                        if(quicksearchcommonselectorchecked){
                            $.getJSON( "$clientRoot/webservices/autofillvernacular.php", {
                                term: request.term,
                                limit: 10
                            }, response );
                        }
                        else{
                            $.getJSON( "$clientRoot/webservices/autofillsciname.php", {
                                term: request.term,
                                limit: 10,
                                hideauth: true,
                                taid: 1
                            }, response );
                        }
                    },
                    appendTo: "#quicksearchdiv",
                    select: function( event, ui ) {
                        this.value = ui.item.value;
                        document.getElementById('quicksearchtaxonvalue').value = ui.item.id;
                    }
                },{ minLength: 3 });
            });
        }
        function initializeQuickSearch(){
            $("#quicksearchtaxon").autocomplete({
                source: function( request, response ) {
                    var quicksearchcommonselectorchecked = document.quicksearch.quicksearchselector.checked;
                    if(quicksearchcommonselectorchecked){
                        $.getJSON( "$clientRoot/webservices/autofillvernacular.php", {
                            term: request.term,
                            limit: 10
                        }, response );
                    }
                    else{
                        $.getJSON( "$clientRoot/webservices/autofillsciname.php", {
                            term: request.term,
                            limit: 10,
                            hideauth: true,
                            taid: 1
                        }, response );
                    }
                },
                appendTo: "#quicksearchdiv",
                select: function( event, ui ) {
                    this.value = ui.item.value;
                    document.getElementById('quicksearchtaxonvalue').value = ui.item.id;
                },
                change: function (event, ui) {
                    if (!ui.item) {
                        document.getElementById('quicksearchtaxon').value = '';
                        document.getElementById('quicksearchtaxonvalue').value = '';
                    }
                }
            },{ minLength: 3 });
        }
        function verifyQuickSearch(){
            if(document.getElementById("quicksearchtaxon").value === ""){
                alert("Please enter a scientific name to search for.");
                return false;
            }
            return true;
        }
        function quicksearchselectorchange(){
            var quicksearchcommonselectorchecked = document.quicksearch.quicksearchselector.checked;
            var placeholdertext = '';
            document.getElementById('quicksearchtaxon').value = '';
            document.getElementById('quicksearchtaxonvalue').value = '';
            if(quicksearchcommonselectorchecked){
                placeholdertext = '$this->quickSearchCommonPlaceholder';
            }
            else{
                placeholdertext = '$this->quickSearchScinamePlaceholder';
            }
            document.getElementById("quicksearchtaxon").placeholder = placeholdertext;
        }
    </script>
    <form name="quicksearch" id="quicksearch" action="$clientRoot/taxa/index.php" method="get" onsubmit="return verifyQuickSearch();">
        <div id="quicksearchtext" style="display:$searchTextCssDisplay;"><b>$searchText</b></div>
        <div id="quicksearchinputcontainer">
            <div class="quicksearchselectorcontainer" style="display:$selectorTextCssDisplay;">
                <div class="quicksearchscinameselectorlabel">Scientific Name</div>
                <div>
                    <label>
                        <input type="checkbox" class="switch" name="quicksearchselector" id="quicksearchcommonselector" onchange="quicksearchselectorchange();" autocomplete="off" $commonChecked>
                        <div class="switch"></div>
                    </label>
                </div>
                <div class="quicksearchcommonselectorlabel">Common Name</div>
            </div>
            <input type="text" name="quicksearchtaxon" placeholder="$initialPlaceholder" id="quicksearchtaxon" title="Enter taxon name here." />
        </div>
        <input type="hidden" name="taxon" id="quicksearchtaxonvalue" />
        <button name="formsubmit"  id="quicksearchbutton" type="submit" value="Search Terms">$buttonText</button>
    </form>
EOD;
    }

    public function setQuickSearchScinamePlaceholder($val): void
    {
        $this->quickSearchScinamePlaceholder = $val;
    }

    public function setQuickSearchCommonPlaceholder($val): void
    {
        $this->quickSearchCommonPlaceholder = $val;
    }

    public function setQuickSearchShowSelector($val): void
    {
        $this->quickSearchShowSelector = $val;
    }

    public function setQuickSearchDefaultSetting($val): void
    {
        $this->quickSearchDefaultSetting = $val;
    }
}
