<?php
/** @var boolean $inputWindowMode */
/** @var boolean $clusterPoints */
include_once(__DIR__ . '/../../classes/OccurrenceManager.php');

$queryId = array_key_exists('queryId',$_REQUEST)?(int)$_REQUEST['queryId']:0;
$stArrJson = array_key_exists('starr',$_REQUEST)?$_REQUEST['starr']:'';

$stArr = array();
$validStArr = false;

$occManager = new OccurrenceManager();

if($stArrJson){
    $stArr = json_decode($stArrJson, true);
    if($occManager->validateSearchTermsArr($stArr)){
        $validStArr = true;
    }
}
?>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/spatial.module.symb.js?ver=20220622" type="text/javascript"></script>
<script type="text/javascript">
    const SOLRMODE = '<?php echo $GLOBALS['SOLR_MODE']; ?>';
    let searchTermsArr = {};

    $(function() {
        let winHeight = $(window).height();
        winHeight = winHeight + "px";
        document.getElementById('spatialpanel').style.height = winHeight;

        $("#sidepanel-accordion").accordion({
            icons: null,
            collapsible: true,
            heightStyle: "fill"
        });
    });

    $(window).resize(function(){
        let winHeight = $(window).height();
        winHeight = winHeight + "px";
        document.getElementById('spatialpanel').style.height = winHeight;
        $("#sidepanel-accordion").accordion("refresh");
    });

    $(document).on("pageloadfailed", function(event){
        event.preventDefault();
    });

    $(document).ready(function() {
        setLayersController();
        if(document.getElementById("taxa")){
            $( "#taxa" )
                .bind( "keydown", function( event ) {
                    if ( event.keyCode == $.ui.keyCode.TAB &&
                        $( this ).data( "autocomplete" ).menu.active ) {
                        event.preventDefault();
                    }
                })
                .autocomplete({
                    source: function( request, response ) {
                        const t = Number(document.getElementById("taxontype").value);
                        let rankLow = '';
                        let rankHigh = '';
                        let rankLimit = '';
                        let source = '';
                        if(t === 5){
                            source = '../webservices/autofillvernacular.php';
                        }
                        else{
                            source = '../webservices/autofillsciname.php';
                        }
                        if(t === 4){
                            rankLow = 21;
                            rankHigh = 139;
                        }
                        else if(t === 2){
                            rankLimit = 140;
                        }
                        else if(t === 3){
                            rankLow = 141;
                        }
                        else{
                            rankLow = 140;
                        }
                        //console.log('term: '+request.term+'rlow: '+rankLow+'rhigh: '+rankHigh+'rlimit: '+rankLimit);
                        $.getJSON( source, {
                            term: extractLast( request.term ),
                            rlow: rankLow,
                            rhigh: rankHigh,
                            rlimit: rankLimit,
                            hideauth: true,
                            limit: 20
                        }, response );
                    },
                    appendTo: "#taxa_autocomplete",
                    search: function() {
                        const term = extractLast( this.value );
                        if ( term.length < 4 ) {
                            return false;
                        }
                    },
                    focus: function() {
                        return false;
                    },
                    select: function( event, ui ) {
                        const terms = split( this.value );
                        terms.pop();
                        terms.push( ui.item.value );
                        this.value = terms.join( ", " );
                        processTaxaParamChange();
                        return false;
                    }
                },{});
        }

        spatialModuleInitialising = true;
        initializeSearchStorage(<?php echo $queryId; ?>);

        $('#criteriatab').tabs({
            beforeLoad: function( event, ui ) {
                $(ui.panel).html("<p>Loading...</p>");
            }
        });
        $('#recordstab').tabs({
            beforeLoad: function( event, ui ) {
                $(ui.panel).html("<p>Loading...</p>");
            }
        });
        $('#vectortoolstab').tabs({
            beforeLoad: function( event, ui ) {
                $(ui.panel).html("<p>Loading...</p>");
            }
        });
        $('#addLayers').popup({
            transition: 'all 0.3s',
            scrolllock: true,
            blur: false
        });
        $('#infopopup').popup({
            transition: 'all 0.3s'
        });
        $('#datasetmanagement').popup({
            transition: 'all 0.3s',
            scrolllock: true
        });
        $('#csvoptions').popup({
            transition: 'all 0.3s',
            scrolllock: true
        });
        $('#mapsettings').popup({
            transition: 'all 0.3s',
            scrolllock: true
        });
        $('#layerqueryselector').popup({
            transition: 'all 0.3s',
            scrolllock: true,
            closetransitionend: function(event, ui) {
                clearLayerQuerySelector();
            }
        });

        <?php
        if(!$clusterPoints){
            echo 'deactivateClustering();';
        }
        if($inputWindowMode){
            echo 'loadInputParentParams();';
        }
        if($queryId || $validStArr){
            if($validStArr){
                ?>
                initializeSearchStorage(<?php echo $queryId; ?>);
                loadSearchTermsArrFromJson('<?php echo $stArrJson; ?>');
                <?php
            }
            ?>
            searchTermsArr = getSearchTermsArr();
            if(validateSearchTermsArr(searchTermsArr)){
                setInputFormBySearchTermsArr();
                createShapesFromSearchTermsArr();
                setCollectionForms();
                loadPoints();
            }
            <?php
        }
        ?>
        spatialModuleInitialising = false;
    });
</script>
