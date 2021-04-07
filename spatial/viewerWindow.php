<?php
include_once(__DIR__ . '/../config/symbini.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
ini_set('max_execution_time', 180);

$decimalLatitude = array_key_exists('decimallatitude',$_REQUEST)?(float)$_REQUEST['decimallatitude']:null;
$decimalLongitude = array_key_exists('decimallongitude',$_REQUEST)?(float)$_REQUEST['decimallongitude']:null;
$coordArrJson = array_key_exists('coordJson',$_REQUEST)?$_REQUEST['coordJson']:'';
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Spatial Viewer</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet">
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet">
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet" />
    <style type="text/css">
        .map {
            position: absolute;
            top: 0;
            bottom: 0;
            right: 0;
            left: 0;
            font-family: Arial, sans-serif;
        }


    </style>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery-ui.js" type="text/javascript"></script>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/ol.css?ver=2" type="text/css" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/spatialviewerbase.css?ver=2" type="text/css" rel="stylesheet" />
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/all.min.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/ol.js?ver=4" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/symb/spatial.module.js?ver=20210325" type="text/javascript"></script>
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
</body>
</html>
