<?php
include_once(__DIR__ . '/../config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
ini_set('max_execution_time', 180);

$decimalLatitude = array_key_exists('decimallatitude',$_REQUEST)?(float)$_REQUEST['decimallatitude']:null;
$decimalLongitude = array_key_exists('decimallongitude',$_REQUEST)?(float)$_REQUEST['decimallongitude']:null;
$coordArrJson = array_key_exists('coordJson',$_REQUEST)?$_REQUEST['coordJson']:'';
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>" style="background-color:white;">
<?php
include_once(__DIR__ . '/../config/header-includes.php');
?>
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Spatial Viewer</title>
    <meta name="description" content="Spatial viewer for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css?ver=20221204" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/ol.css?ver=20240115" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/spatialviewerbase.css?ver=20230105" rel="stylesheet" type="text/css"/>
    <style>
        .map {
            position: absolute;
            top: 0;
            bottom: 0;
            right: 0;
            left: 0;
            font-family: Arial, sans-serif;
        }
    </style>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/ol.js?ver=20240115" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/spatial.module.core.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
</head>
<body>
    <div>
        <?php include_once(__DIR__ . '/viewerElement.php'); ?>
    </div>
    <script type="text/javascript">
        const decimalLatitude = <?php echo ($decimalLatitude ?: 'null'); ?>;
        const decimalLongitude = <?php echo ($decimalLongitude ?: 'null'); ?>;
        const coordArrJson = '<?php echo $coordArrJson; ?>';
        if(decimalLatitude && decimalLongitude){
            let pointFeature = null;
            const pointGeom = new ol.geom.Point(ol.proj.fromLonLat([
                decimalLongitude, decimalLatitude
            ]));
            pointFeature = new ol.Feature(pointGeom);
            if(pointFeature){
                vectorsource.addFeature(pointFeature);
                map.getView().setCenter(ol.proj.fromLonLat([
                    decimalLongitude, decimalLatitude
                ]));
                map.getView().setZoom(10);
            }
        }
        if(coordArrJson){
            const coordArr = JSON.parse(coordArrJson);
            for(let coords of coordArr){
                let pointFeature = null;
                const pointGeom = new ol.geom.Point(ol.proj.fromLonLat([
                    coords[1], coords[0]
                ]));
                pointFeature = new ol.Feature(pointGeom);
                if(pointFeature){
                    vectorsource.addFeature(pointFeature);
                }
            }
            const vectorextent = vectorsource.getExtent();
            map.getView().fit(vectorextent,map.getSize());
            let fittedZoom = map.getView().getZoom();
            if(fittedZoom > 10){
                map.getView().setZoom(fittedZoom - 8);
            }
        }
    </script>
    <?php
    include_once(__DIR__ . '/../config/footer-includes.php');
    ?>
</body>
</html>
