<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/ConfigurationManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');

if(!$GLOBALS['IS_ADMIN']) {
    header('Location: ../index.php');
}

$confManager = new ConfigurationManager();

$fullConfArr = $confManager->getConfigurationsArr();
$coreConfArr = $fullConfArr['core'];
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Spatial Configuration Manager</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet" />
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/all.min.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery-ui.js" type="text/javascript"></script>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/ol.css?ver=20220209" type="text/css" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/spatialviewerbase.css?ver=20210415" type="text/css" rel="stylesheet" />
    <style type="text/css">
        .map {
            width:95%;
            height:650px;
            margin-left: auto;
            margin-right: auto;
        }

        #mapinfo, #mapscale_us, #mapscale_metric {
            display: none;
        }
    </style>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/ol/ol.js?ver=20220215" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/symb/spatial.module.js?ver=20220307" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#tabs').tabs({
                beforeLoad: function( event, ui ) {
                    $(ui.panel).html("<p>Loading...</p>");
                }
            });
        });

        function formatPath(path){
            if(path.charAt(path.length - 1) === '/'){
                path = path.substring(0, path.length - 1);
            }
            if(path.charAt(0) !== '/'){
                path = '/' + path;
            }
            return path;
        }
    </script>
</head>
<body>
<?php
include(__DIR__ . '/../header.php');
?>
<div id="innertext">
    <div id="tabs" style="width:95%;">
        <ul>
            <li><a href='#spatialconfig'>Spatial Configurations</a></li>
            <li><a href="#layersconfig">Layers</a></li>
        </ul>

        <div id="spatialconfig">
            <div style="width:95%;margin: 20px auto;">
                Adjust the Base Layer and zoom level, and move the map below to how you would like maps to open by default within the portal.
                Then click the Save Spatial Defaults button to save the settings.
                <div style="display:flex;justify-content: right;">
                    <button type="button" onclick="processSaveSettings();">Save Spatial Defaults</button>
                </div>
            </div>
            <?php include_once(__DIR__ . '/../spatial/viewerElement.php'); ?>
            <div style="clear:both;width:100%;height:40px;"></div>
        </div>

        <div id="layersconfig">

        </div>
    </div>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
<script type="text/javascript">
    function processSaveSettings(){
        const baseLayerValue = document.getElementById('base-map').value;
        const zoomValue = map.getView().getZoom();
        const centerPoint = map.getView().getCenter();
        const centerPointFixed = ol.proj.transform(centerPoint, 'EPSG:3857', 'EPSG:4326');
        const centerPointValue = '[' + centerPointFixed.toString() + ']';
        const http = new XMLHttpRequest();
        const url = "rpc/configurationModelController.php";
        let params = 'action=update&name=SPATIAL_INITIAL_BASE_LAYER&value='+baseLayerValue;
        //console.log(url+'?'+params);
        http.open("POST", url, true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.onreadystatechange = function() {
            if(http.readyState === 4 && http.status === 200) {
                let params = 'action=update&name=SPATIAL_INITIAL_ZOOM&value='+zoomValue;
                //console.log(url+'?'+params);
                http.open("POST", url, true);
                http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                http.onreadystatechange = function() {
                    if(http.readyState === 4 && http.status === 200) {
                        let params = 'action=update&name=SPATIAL_INITIAL_CENTER&value='+centerPointValue;
                        //console.log(url+'?'+params);
                        http.open("POST", url, true);
                        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        http.onreadystatechange = function() {
                            if(http.readyState === 4 && http.status === 200) {
                                location.reload();
                            }
                        };
                        http.send(params);
                    }
                };
                http.send(params);
            }
        };
        http.send(params);
    }
</script>
</body>
</html>
