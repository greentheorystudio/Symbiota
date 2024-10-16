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
    $stArr = json_decode(str_replace('%squot;', "'",$stArrJson), true);
    if($occManager->validateSearchTermsArr($stArr)){
        $validStArr = true;
    }
}
?>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/spatial.module.symb.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script type="text/javascript">
    let searchTermsArr = {};

    $(function() {
        let winHeight = $(window).height();
        winHeight = (winHeight - 5) + "px";
        document.getElementById('spatialpanel').style.height = winHeight;

        $("#sidepanel-accordion").accordion({
            icons: null,
            collapsible: true,
            heightStyle: "fill"
        });
    });

    $(window).resize(function(){
        let winHeight = $(window).height();
        winHeight = (winHeight - 5) + "px";
        document.getElementById('spatialpanel').style.height = winHeight;
        if(!INPUTWINDOWMODE){
            $("#sidepanel-accordion").accordion("refresh");
        }
    });

    document.addEventListener("DOMContentLoaded", function() {
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
                            source = '../api/taxa/autofillvernacular.php';
                        }
                        else{
                            source = '../api/taxa/autofillsciname.php';
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
                            term: request.term.split( /,\s*/ ).pop(),
                            rlow: rankLow,
                            rhigh: rankHigh,
                            rlimit: rankLimit,
                            hideauth: true,
                            limit: 20
                        }, response );
                    },
                    appendTo: "#taxa_autocomplete",
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
                        terms.push( ui.item.value );
                        this.value = terms.join( ", " );
                        processTaxaParamChange();
                        return false;
                    }
                },{}
            );
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
        $('#rastertoolstab').tabs({
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
        $('#setclusterdistance').spinner({
            step: 1,
            min: 0,
            numberFormat: "n",
            spin: function() {
                changeClusterDistance();
            },
            change: function() {
                changeClusterDistance();
            }
        });
        $('#heatmapradius').spinner({
            step: 1,
            min: 0,
            numberFormat: "n",
            spin: function() {
                changeHeatMapRadius();
            },
            change: function() {
                changeHeatMapRadius();
            }
        });
        $('#heatmapblur').spinner({
            step: 1,
            min: 0,
            numberFormat: "n",
            spin: function() {
                changeHeatMapBlur();
            },
            change: function() {
                changeHeatMapBlur();
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
                loadPointsEvent = true;
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

    const blankdragdropsource = new ol.source.Vector({
        wrapX: true
    });
    layersObj['dragDrop1'] = new ol.layer.Vector({
        zIndex: 1,
        source: blankdragdropsource,
        style: getVectorLayerStyle(SPATIAL_DRAGDROP_FILL_COLOR, SPATIAL_DRAGDROP_BORDER_COLOR, SPATIAL_DRAGDROP_BORDER_WIDTH, SPATIAL_DRAGDROP_POINT_RADIUS, SPATIAL_DRAGDROP_OPACITY)
    });
    layersArr.push(layersObj['dragDrop1']);
    layersObj['dragDrop2'] = new ol.layer.Vector({
        zIndex: 2,
        source: blankdragdropsource,
        style: getVectorLayerStyle(SPATIAL_DRAGDROP_FILL_COLOR, SPATIAL_DRAGDROP_BORDER_COLOR, SPATIAL_DRAGDROP_BORDER_WIDTH, SPATIAL_DRAGDROP_POINT_RADIUS, SPATIAL_DRAGDROP_OPACITY)
    });
    layersArr.push(layersObj['dragDrop2']);
    layersObj['dragDrop3'] = new ol.layer.Vector({
        zIndex: 3,
        source: blankdragdropsource,
        style: getVectorLayerStyle(SPATIAL_DRAGDROP_FILL_COLOR, SPATIAL_DRAGDROP_BORDER_COLOR, SPATIAL_DRAGDROP_BORDER_WIDTH, SPATIAL_DRAGDROP_POINT_RADIUS, SPATIAL_DRAGDROP_OPACITY)
    });
    layersArr.push(layersObj['dragDrop3']);
    layersObj['dragDrop4'] = new ol.layer.Image({
        zIndex: 4,
    });
    layersObj['dragDrop5'] = new ol.layer.Image({
        zIndex: 5,
    });
    layersObj['dragDrop6'] = new ol.layer.Image({
        zIndex: 6,
    });

    let uncertaintycirclesource = new ol.source.Vector({
        wrapX: true
    });
    layersObj['uncertainty'] = new ol.layer.Vector({
        zIndex: 7,
        source: uncertaintycirclesource,
        style: new ol.style.Style({
            fill: new ol.style.Fill({
                color: 'rgba(255,0,0,0.3)'
            }),
            stroke: new ol.style.Stroke({
                color: '#000000',
                width: 1
            }),
            image: new ol.style.Circle({
                radius: 7,
                stroke: new ol.style.Stroke({
                    color: '#000000',
                    width: 1
                }),
                fill: new ol.style.Fill({
                    color: 'rgba(255,0,0)'
                })
            })
        })
    });
    layersArr.push(layersObj['uncertainty']);

    const selectsource = new ol.source.Vector({
        wrapX: true
    });
    layersObj['select'] = new ol.layer.Vector({
        zIndex: 8,
        source: selectsource,
        style: getVectorLayerStyle(SPATIAL_SHAPES_FILL_COLOR, SPATIAL_SHAPES_BORDER_COLOR, SPATIAL_SHAPES_BORDER_WIDTH, SPATIAL_SHAPES_POINT_RADIUS, SPATIAL_SHAPES_OPACITY)
    });
    layersArr.push(layersObj['select']);

    let pointvectorsource = new ol.source.Vector({
        wrapX: true
    });
    layersObj['pointv'] = new ol.layer.Vector({
        zIndex: 9,
        source: pointvectorsource
    });
    layersArr.push(layersObj['pointv']);

    layersObj['heat'] = new ol.layer.Heatmap({
        zIndex: 10,
        source: pointvectorsource,
        weight: function (feature) {
            return 1;
        },
        gradient: ['#00f', '#0ff', '#0f0', '#ff0', '#f00'],
        blur: parseInt(heatMapBlur.toString(), 10),
        radius: parseInt(heatMapRadius.toString(), 10),
        visible: false
    });
    layersArr.push(layersObj['heat']);

    layersObj['spider'] = new ol.layer.Vector({
        zIndex: 11,
        source: new ol.source.Vector({
            features: new ol.Collection(),
            useSpatialIndex: true
        })
    });
    layersArr.push(layersObj['spider']);

    let rasteranalysissource = new ol.source.Vector({
        wrapX: true
    });
    layersObj['rasteranalysis'] = new ol.layer.Vector({
        zIndex: 12,
        source: rasteranalysissource,
        style: new ol.style.Style({
            fill: new ol.style.Fill({
                color: 'rgba(255,0,0,0.3)'
            }),
            stroke: new ol.style.Stroke({
                color: 'rgba(255,0,0,1)',
                width: 5
            })
        })
    });
    layersArr.push(layersObj['rasteranalysis']);
</script>
