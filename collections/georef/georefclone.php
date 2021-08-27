<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/OccurrenceGeorefTools.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$country = array_key_exists('country',$_REQUEST)?$_REQUEST['country']:'';
$state = array_key_exists('state',$_REQUEST)?$_REQUEST['state']:'';
$county = array_key_exists('county',$_REQUEST)?$_REQUEST['county']:'';
$locality = array_key_exists('locality',$_REQUEST)?$_REQUEST['locality']:'';
$searchType = array_key_exists('searchtype',$_POST)?(int)$_POST['searchtype']:1;
$collType = array_key_exists('colltype',$_POST)?(int)$_POST['colltype']:0;
$collid = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
$submitAction = array_key_exists('submitaction',$_POST)?$_POST['submitaction']:'';

if(!$country || !$state || !$county){
	$locArr = explode(';',$locality);
	$locality = trim(array_pop($locArr));
}
$locality = trim(preg_replace('/[\[\])\d.\-,\s]*$/', '', $locality),'( ');

$geoManager = new OccurrenceGeorefTools();

$clones = $geoManager->getGeorefClones($locality, $country, $state, $county, $searchType, ($collType?$collid:'0'));
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
	<head>
		<title>Georeference Clone Tool</title>
		<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
		<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
		<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/ol.css?ver=2" type="text/css" rel="stylesheet" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/spatialviewerbase.css?ver=20210415" type="text/css" rel="stylesheet" />
        <style type="text/css">
            .map {
                height: 600px;
                overflow: hidden;
            }
        </style>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/all.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery-ui.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/ol.js?ver=4" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/symb/spatial.module.js?ver=20210817" type="text/javascript"></script>
		<script type="text/javascript">
            $(document).ready(function() {
                const cloneArr = JSON.parse('<?php echo json_encode($clones, JSON_THROW_ON_ERROR); ?>');
                for(let id in cloneArr){
                    if(cloneArr.hasOwnProperty(id)){
                        const pointGeom = new ol.geom.Point(ol.proj.fromLonLat([
                            Number(cloneArr[id]['lng']), Number(cloneArr[id]['lat'])
                        ]));
                        const pointFeature = new ol.Feature(pointGeom);
                        let latLngStr = cloneArr[id]['lat'] + ' ' + cloneArr[id]['lng'];
                        if(cloneArr[id]['err']) {
                            latLngStr += ' (+-' + cloneArr[id]['err'] + 'm)';
                        }
                        pointFeature.set('latlngstr',latLngStr);
                        pointFeature.set('georefby',cloneArr[id]['georefby']);
                        pointFeature.set('cnt',cloneArr[id]['cnt'] + ' matching records');
                        pointFeature.set('locality',cloneArr[id]['locality']);
                        pointFeature.set('lat',cloneArr[id]['lat']);
                        pointFeature.set('lng',cloneArr[id]['lng']);
                        pointFeature.set('err',cloneArr[id]['err']);
                        vectorsource.addFeature(pointFeature);
                    }
                }
                const vectorextent = vectorsource.getExtent();
                map.getView().fit(vectorextent,map.getSize());
                let fittedZoom = map.getView().getZoom();
                if(fittedZoom > 10){
                    map.getView().setZoom(fittedZoom - 8);
                }
            });

            function cloneCoord(lat,lng,err){
				try{
					if(err === 0) {
					    err = "";
					}
					opener.document.getElementById("decimallatitude").value = lat;
					opener.document.getElementById("decimallongitude").value = lng;
					opener.document.getElementById("coordinateuncertaintyinmeters").value = err;
					opener.document.getElementById("decimallatitude").onchange(undefined);
					opener.document.getElementById("decimallongitude").onchange(undefined);
					opener.document.getElementById("coordinateuncertaintyinmeters").onchange(undefined);
				}
				catch(myErr){
				}
                self.close();
                return false;
			}


			function verifyCloneForm(f){
				if(f.locality.value === ""){
					alert("Locality field must have a value");
					return false
				}
				if(document.getElementById("deepsearch").checked === true){
                    const locArr = f.locality.value.split(" ");
                    if(locArr.length > 4){
						alert("Locality field cannot contain more than 4 words while doing a Deep Search. Just enter a few keywords.");
						return false
					}
				}
				return true;
			}
		</script>
	</head>
	<body style="background-color:#ffffff;">
		<div id="innertext">
			<fieldset style="padding:10px;">
				<legend><b>Search Form</b></legend>
				<form name="cloneform" action="georefclone.php" method="post" onsubmit="return verifyCloneForm(this)">
					<div>
						Locality: 
						<input name="locality" type="text" value="<?php echo $locality; ?>" style="width:600px" />
					</div>
					<div>
						<input id="exactinput" name="searchtype" type="radio" value="1" <?php echo ($searchType === 1?'checked':''); ?> /> Exact Match
						<input id="wildsearch" name="searchtype" type="radio" value="2" <?php echo ($searchType === 2?'checked':''); ?> /> Contains
						<input id="deepsearch" name="searchtype" type="radio" value="3" <?php echo ($searchType === 3?'checked':''); ?> /> Deep Search
					</div>
					<?php 
					if($collid){
						?>
						<div>
							<input name="colltype" type="radio" value="0" <?php echo ($collType?'':'checked'); ?> /> Search all collections
							<input name="colltype" type="radio" value="1" <?php echo ($collType?'checked':''); ?> /> Target collection only
						</div>
						<?php 
					}
					?>
					<div style="float:left;margin:5px 20px;">
						<input name="country" type="hidden" value="<?php echo $country; ?>" />
						<input name="state" type="hidden" value="<?php echo $state; ?>" />
						<input name="county" type="hidden" value="<?php echo $county; ?>" />
						<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
						<input name="submitaction" type="submit" value="Search" />
					</div>
				</form> 
			</fieldset>
			<?php 
			if($clones){
				?>
				<div style="margin:3px;font-weight:bold;">
					Hold down the alt key and click on markers to view and clone coordinates
				</div>
				<div style="height: 600px;">
                    <?php include_once(__DIR__ . '/../../spatial/viewerElement.php'); ?>
                </div>
                <script type="text/javascript">
                    map.on('singleclick', function(evt) {
                        let infoHTML;
                        if(evt.originalEvent.altKey){
                            infoHTML = '';
                            const feature = map.forEachFeatureAtPixel(evt.pixel, function (feature, layer) {
                                return feature;
                            });
                            if(feature){
                                let errVal = Number(feature.get('err'));
                                if(!errVal){
                                    errVal = 0;
                                }
                                infoHTML = feature.get('latlngstr')+'<br />';
                                if(feature.get('georefby')) {
                                    infoHTML += '<b>Georeferenced by:</b> '+feature.get('georefby')+'<br />';
                                }
                                infoHTML += feature.get('cnt')+'<br />';
                                infoHTML += feature.get('locality')+'<br />';
                                infoHTML += "<a href='#' onclick='cloneCoord("+feature.get('lat')+','+feature.get('lng')+','+errVal+");' title='Clone Coordinates'><b>Use Coordinates</b></a>";
                                if(infoHTML){
                                    popupcontent.innerHTML = infoHTML;
                                    popupoverlay.setPosition(evt.coordinate);
                                }
                            }
                        }
                    });
                </script>
				<?php 
			}
			else{
				?>
				<div style="margin:30px"><h2>Search failed to return occurrence matches</h2></div>
				<?php 
			}
			?>
		</div>
	</body>
</html>
