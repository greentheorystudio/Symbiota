<?php 
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/ImageLibraryManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$queryId = array_key_exists('queryId',$_REQUEST)?$_REQUEST['queryId']:0;
$target = array_key_exists('target',$_REQUEST)?trim($_REQUEST['target']): '';
$cntPerPage = array_key_exists('cntperpage',$_REQUEST)?$_REQUEST['cntperpage']:100;
$pageNumber = array_key_exists('page',$_REQUEST)?$_REQUEST['page']:1;
$view = array_key_exists('imagedisplay',$_REQUEST)?$_REQUEST['imagedisplay']:'';
$stArrJson = array_key_exists('starr',$_REQUEST)?$_REQUEST['starr']:'';
$catId = array_key_exists('catid',$_REQUEST)?$_REQUEST['catid']:0;
if(!$catId && isset($GLOBALS['DEFAULTCATID']) && $GLOBALS['DEFAULTCATID']) {
    $catId = $GLOBALS['DEFAULTCATID'];
}
$action = array_key_exists('submitaction',$_REQUEST)?$_REQUEST['submitaction']:'';

$imgLibManager = new ImageLibraryManager();

$collList = $imgLibManager->getFullCollectionList($catId);
$specArr = ($collList['spec'] ?? null);
$obsArr = ($collList['obs'] ?? null);
$imageArr = array();
$taxaList = array();

if($action && $action === 'Load Images') {
    if($stArr){
        $imgLibManager->setSearchTermsArr($stArr);
    }
    else{
        $imgLibManager->readRequestVariables();
        $stArr = $imgLibManager->getSearchTermsArr();
    }
    $imgLibManager->setSqlWhere();
    if($view === 'thumbnail'){
        $imageArr = $imgLibManager->getImageArr($pageNumber,$cntPerPage);
    }
    if($view === 'taxalist'){
        $taxaList = $imgLibManager->getFamilyList();
    }
    $recordCnt = $imgLibManager->getRecordCnt();
    $jsonStArr = json_encode($stArr);
}
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Image Search</title>
	<link href="../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<link href="../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<link href="../css/jquery-ui.css" type="text/css" rel="stylesheet" />
	<script src="../js/jquery.js" type="text/javascript"></script>
	<script src="../js/jquery-ui.js" type="text/javascript"></script>
	<script src="../js/jquery.manifest.js" type="text/javascript"></script>
	<script src="../js/jquery.marcopolo.js" type="text/javascript"></script>
	<script src="../js/symb/images.index.js?ver=20210809" type="text/javascript"></script>
    <script src="../js/symb/search.term.manager.js?ver=20210420" type="text/javascript"></script>
	<?php include_once(__DIR__ . '/../config/googleanalytics.php'); ?>
	<script type="text/javascript">
        let stArr = {};
        let phArr = <?php echo (isset($previousCriteria['phjson'])&&$previousCriteria['phjson']?"JSON.parse('".$previousCriteria['phjson']."')": 'new Array()'); ?>;
        const starr = JSON.stringify(<?php echo $jsonStArr; ?>);
        const view = '<?php echo $view; ?>';
        let selectedFamily = '';

        $(document).ready(function() {
			let qtaxaArr;
            $('#tabs').tabs({
				active: <?php echo (($imageArr || $taxaList)?'2':'0'); ?>,
				beforeLoad: function( event, ui ) {
					$(ui.panel).html("<p>Loading...</p>");
				}
			});
			
			$('#photographer').manifest({
				required: true,
				marcoPolo: {
					url: 'rpc/imagesearchautofill.php',
					data: {
						t: 'photographer'
					},
					formatItem: function (data){
						return data.name;
					}
				}
			});

            $('#taxa').manifest({
                marcoPolo: {
                    url: 'rpc/imagesearchautofill.php',
                    data: {
                        t: 'taxa'
                    },
                    formatItem: function (data) {
                        return data.name;
                    }
                }
            });

            $('#common').manifest({
                marcoPolo: {
                    url: 'rpc/imagesearchautofill.php',
                    data: {
                        t: 'common'
                    },
                    formatItem: function (data) {
                        return data.name;
                    }
                }
            });

            $('#country').manifest({
                marcoPolo: {
                    url: 'rpc/imagesearchautofill.php',
                    data: {
                        t: 'country'
                    },
                    formatItem: function (data) {
                        return data.name;
                    }
                }
            });

            $('#state').manifest({
                marcoPolo: {
                    url: 'rpc/imagesearchautofill.php',
                    data: {
                        t: 'state'
                    },
                    formatItem: function (data) {
                        return data.name;
                    }
                }
            });

            $('#keywords').manifest({
                marcoPolo: {
                    url: 'rpc/imagesearchautofill.php',
                    data: {
                        t: 'keywords'
                    },
                    formatItem: function (data) {
                        return data.name;
                    }
                }
            });

            initializeSearchStorage(<?php echo $queryId; ?>);
            <?php
            if($queryId || $stArrJson){
                if($stArrJson){
                    ?>
                    initializeSearchStorage(<?php echo $queryId; ?>);
                    loadSearchTermsArrFromJson('<?php echo $stArrJson; ?>');
                    <?php
                }
                ?>
                stArr = getSearchTermsArr();
                setParamsForm();
                setCollectionForms();
                loadPoints();
                <?php
            }
            ?>
			
			<?php
			if($stArr){
				if(array_key_exists('nametype',$previousCriteria) && $previousCriteria['nametype'] !== '3'){
					?>
					if(document.getElementById('taxastr').value){
                        qtaxaArr = document.getElementById('taxastr').value.split(";");
                        for(let i = 0; i < qtaxaArr.length; i++){
							$('#taxa').manifest('add',qtaxaArr[i]);
						}
					}
					<?php
				}
				elseif(array_key_exists('nametype',$previousCriteria) && $previousCriteria['nametype'] === '3'){
					?>
					if(document.getElementById('taxastr').value){
                        qtaxaArr = document.getElementById('taxastr').value.split(";");
                        for(let i = 0; i < qtaxaArr.length; i++){
							$('#common').manifest('add',qtaxaArr[i]);
						}
					}
					<?php
				}
				?>
				if(document.getElementById('countrystr').value){
                    const qcountryArr = document.getElementById('countrystr').value.split(";");
                    for(let i = 0; i < qcountryArr.length; i++){
						$('#country').manifest('add',qcountryArr[i]);
					}
				}
				if(document.getElementById('statestr').value){
                    const qstateArr = document.getElementById('statestr').value.split(";");
                    for(let i = 0; i < qstateArr.length; i++){
						$('#state').manifest('add',qstateArr[i]);
					}
				}
				if(document.getElementById('keywordstr').value){
                    const qkeywordArr = document.getElementById('keywordstr').value.split(";");
                    for(let i = 0; i < qkeywordArr.length; i++){
						$('#keywords').manifest('add',qkeywordArr[i]);
					}
				}
				if(document.getElementById('phjson').value){
                    const qphArr = JSON.parse(document.getElementById('phjson').value);
                    for(let i = 0; i < qphArr.length; i++){
						$('#photographer').manifest('add',qphArr[i].name);
					}
				}
				<?php
			}
			?>
			
			$('#photographer').on('marcopoloselect', function (event, data) {
				phArr.push({name:data.name,id:data.id});
			});
			
			$('#photographer').on('manifestremove',function (event, data){
				for (let i = 0; i < phArr.length; i++) {
					if(phArr[i].name === data){
						phArr.splice(i,1);
					}
				}
			});
			<?php
			
			if($view === 'thumbnail' && !$imageArr){
				echo "alert('There were no images matching your search critera');";
			}
			?>
		});
		
		function changeImagePage(taxonIn,viewIn,starrIn,pageIn){
            document.getElementById("imagebox").innerHTML = "<p>Loading... <img src='../images/workingcircle.gif' style='width:15px;' /></p>";
            const http = new XMLHttpRequest();
            const url = "rpc/changeimagepage.php";
            const queryid = document.getElementById('queryId').value;
            const params = 'starr='+encodeURIComponent(JSON.stringify(stArr))+'&queryId='+queryid+'&page='+pageIn+'&view='+viewIn+'&taxon='+taxonIn;
            //console.log(url+'?'+params);
            http.open("POST", url, true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.onreadystatechange = function() {
                if(http.readyState === 4 && http.status === 200) {
                    if(!http.responseText) {
                        http.responseText = "<p>An error occurred retrieving records.</p>";
                    }
                    document.getElementById("imagebox").innerHTML = http.responseText;
                    if(viewIn === 'thumb'){
                        document.getElementById("imagetab").innerHTML = 'Images';
                    }
                    else{
                        document.getElementById("imagetab").innerHTML = 'Taxa List';
                    }
                }
            };
            http.send(params);
        }

        function setParamsForm(){
            const stArr = getSearchTermsArr();
            if(stArr['usethes']){
                document.harvestparams.thes.checked = true;
            }
            if(stArr['taxontype']){
                document.harvestparams.type.value = stArr['taxontype'];
            }
            if(stArr['taxa']){
                document.harvestparams.taxa.value = stArr['taxa'];
            }
            let countryStr = '';
            if (stArr['country']) {
                countryStr = stArr['country'];
                countryArr = countryStr.split(";");
                if (countryArr.indexOf('USA') > -1 || countryArr.indexOf('usa') > -1) {
                    countryStr = countryArr[0];
                }
                document.harvestparams.country.value = countryStr;
            }
            if(stArr['state']){
                document.harvestparams.state.value = stArr['state'];
            }
            if(stArr['county']){
                document.harvestparams.county.value = stArr['county'];
            }
            if(stArr['local']){
                document.harvestparams.local.value = stArr['local'];
            }
            if(stArr['elevlow']){
                document.harvestparams.elevlow.value = stArr['elevlow'];
            }
            if(stArr['elevhigh']){
                document.harvestparams.elevhigh.value = stArr['elevhigh'];
            }
            if(stArr['assochost']){
                document.harvestparams.assochost.value = stArr['assochost'];
            }
            if(stArr['upperlat']){
                document.harvestparams.upperlat.value = stArr['upperlat'];
                document.harvestparams.bottomlat.value = stArr['bottomlat'];
                document.harvestparams.leftlong.value = stArr['leftlong'];
                document.harvestparams.rightlong.value = stArr['rightlong'];
            }
            if(stArr['pointlat']){
                document.harvestparams.pointlat.value = stArr['pointlat'];
                document.harvestparams.pointlong.value = stArr['pointlong'];
                document.harvestparams.radius.value = stArr['radius'];
                document.harvestparams.groundradius.value = stArr['groundradius'];
                document.harvestparams.radiustemp.value = stArr['radiustemp'];
                document.harvestparams.radiusunits.value = stArr['radiusunits'];
            }
            if(stArr['polyArr']){
                document.harvestparams.polyArr.value = stArr['polyArr'];
                document.getElementById("spatialParamasNoCriteria").style.display = "none";
                document.getElementById("spatialParamasCriteria").style.display = "block";
            }
            if(stArr['circleArr']){
                document.harvestparams.circleArr.value = stArr['circleArr'];
                document.getElementById("spatialParamasNoCriteria").style.display = "none";
                document.getElementById("spatialParamasCriteria").style.display = "block";
            }
            if(stArr['collector']){
                document.harvestparams.collector.value = stArr['collector'];
            }
            if(stArr['collnum']){
                document.harvestparams.collnum.value = stArr['collnum'];
            }
            if(stArr['eventdate1']){
                document.harvestparams.eventdate1.value = stArr['eventdate1'];
            }
            if(stArr['eventdate2']){
                document.harvestparams.eventdate2.value = stArr['eventdate2'];
            }
            if(stArr['occurrenceRemarks']){
                document.harvestparams.occurrenceRemarks.value = stArr['occurrenceRemarks'];
            }
            if(stArr['catnum']){
                document.harvestparams.catnum.value = stArr['catnum'];
            }
            document.harvestparams.othercatnum.checked = !!stArr['othercatnum'];
            if(stArr['typestatus']){
                document.harvestparams.typestatus.checked = true;
            }
            if(stArr['hasimages']){
                document.harvestparams.hasimages.checked = true;
            }
            if(stArr['hasgenetic']){
                document.harvestparams.hasgenetic.checked = true;
            }
            if(sessionStorage.collsearchtableview){
                document.getElementById('showtable').checked = true;
                changeTableDisplay();
            }
        }

        function verifyCollForm(f){
            let formVerified = false;
            for(let h=0; h<f.length; h++){
                if(f.elements[h].name === "db[]" && f.elements[h].checked){
                    formVerified = true;
                }
                else{
                    document.getElementById("dballcb").checked = false;
                }
                if(f.elements[h].name === "cat[]" && f.elements[h].checked){
                    formVerified = true;
                }
            }
            if(!formVerified){
                alert("Please choose at least one collection!");
                return false;
            }
            else{
                for(let i=0; i<f.length; i++){
                    if(f.elements[i].name === "cat[]" && f.elements[i].checked){
                        const childrenEle = document.getElementById('cat-' + f.elements[i].value).children;
                        for(let j=0; j<childrenEle.length; j++){
                            if(childrenEle[j].tagName === "DIV"){
                                const divChildren = childrenEle[j].children;
                                for(let k=0; k<divChildren.length; k++){
                                    const divChildren2 = divChildren[k].children;
                                    for(let l=0; l<divChildren2.length; l++){
                                        if(divChildren2[l].tagName === "INPUT"){
                                            divChildren2[l].checked = false;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return formVerified;
        }

        function submitImageForm(){
            let taxastr;
            const taxavals = $('#taxa').manifest('values');
            const commonvals = $('#common').manifest('values');
            const countryvals = $('#country').manifest('values');
            const statevals = $('#state').manifest('values');
            const keywordsvals = $('#keywords').manifest('values');
            if(taxavals.length > 0){
                taxastr = taxavals.join(";");
                document.getElementById('taxastr').value = taxastr;
            }
            else if(commonvals.length > 0){
                taxastr = commonvals.join(";");
                document.getElementById('taxastr').value = taxastr;
            }
            else{
                document.getElementById('taxastr').value = '';
            }
            if(countryvals.length > 0){
                document.getElementById('countrystr').value = countryvals.join();
            }
            else{
                document.getElementById('countrystr').value = '';
            }
            if(statevals.length > 0){
                document.getElementById('statestr').value = statevals.join();
            }
            else{
                document.getElementById('statestr').value = '';
            }
            if(keywordsvals.length > 0){
                document.getElementById('keywordstr').value = keywordsvals.join();
            }
            else{
                document.getElementById('keywordstr').value = '';
            }
            if(phArr.length > 0){
                const phids = [];
                for(let i = 0; i < phArr.length; i++){
                    phids.push(phArr[i].id);
                }
                document.getElementById('phuidstr').value = phids.join();
                document.getElementById('phjson').value = JSON.stringify(phArr);
            }
            else{
                document.getElementById('phuidstr').value = '';
                document.getElementById('phjson').value = '';
            }
            return verifyCollForm(document.getElementById('imagesearchform'));
        }

        function processTaxonTypeChange(){
            const taxonType = Number(document.getElementById('taxontype').value);
            if(taxonType === 2){
                clearCommonNameVals();
                document.getElementById('thesdiv').style.display = "block";
                document.getElementById('commonbox').style.display = "none";
                document.getElementById('taxabox').style.display = "block";

            }
            if(taxonType === 3){
                clearSciNameVals();
                document.getElementById('commonbox').style.display = "block";
                document.getElementById('taxabox').style.display = "none";
                document.getElementById('thesdiv').style.display = "none";
                document.getElementById('thes').checked = false;

            }
        }

        function processImageDisplayChange(){
            const displayValue = document.getElementById('imagedisplay').value;
		    if(displayValue === "thumbnail"){
                document.getElementById('imagetabtext').innerHTML = "Images";
            }
		    if(displayValue === "taxalist"){
                clearSciNameVals();
                clearCommonNameVals();
                document.getElementById('imagetabtext').innerHTML = "Taxa List";
            }
        }

        function clearSciNameVals(){
            let tvals = $('#taxa').manifest('values');
            for (let i = 0; i < tvals.length; i++) {
                $('#taxa').manifest('remove', i);
            }
            document.getElementById('taxastr').value = "";
        }

        function clearCommonNameVals(){
            let vals = $('#common').manifest('values');
            for (let i = 0; i < vals.length; i++) {
                $('#common').manifest('remove', i);
            }
            document.getElementById('taxastr').value = "";
        }
    </script>
</head>
<body>

	<?php
	include(__DIR__ . '/../header.php');
    echo '<div class="navpath">';
    echo '<a href="../index.php">Home</a> &gt;&gt; ';
    echo '<a href="contributors.php">Image Contributors</a> &gt;&gt; ';
    echo '<b>Image Search</b>';
    echo '</div>';
	?> 
	<div id="innertext">
		<div id="tabs" style="margin:0;">
			<ul>
				<li><a href="#criteriadiv">Search Criteria</a></li>
				<li><a href="#collectiondiv">Collections</a></li>
                <li id="imagetab" style="display:none;"><a href="#imagesdiv"><span id="imagetabtext">Images</span></a></li>
			</ul>
			
			<form name="imagesearchform" id="imagesearchform" action="search.php" method="get" onsubmit="return submitImageForm();">
				<div id="criteriadiv">
					<div id="thesdiv" style="margin-left:160px;display:block;" >
						<input type='checkbox' id='thes' name='thes' onchange="processTaxaParamChange();" value='1' checked /> Include Synonyms
					</div>
					<div style="clear:both;">
						<div style="float:left;">
							<select id="taxontype" name="nametype" onchange="processTaxonTypeChange();processTaxaParamChange();" style="padding:5px;margin:5px 10px;">
								<option id='sciname' value='2'>Scientific Name</option>
								<option id='commonname' value='3'>Common Name</option>
							</select>
						</div>
						<div id="taxabox" style="float:left;margin-bottom:10px;display:<?php echo ((array_key_exists('nametype',$previousCriteria) && $previousCriteria['nametype'] === '3')?'none':'block'); ?>;">
							<input id="taxa" type="text" style="width:450px;" name="taxa" value="" title="Separate multiple names w/ commas" autocomplete="off" />
						</div>
						<div id="commonbox" style="margin-bottom:10px;display:<?php echo ((array_key_exists('nametype',$previousCriteria) && $previousCriteria['nametype'] === '3')?'block':'none'); ?>;">
							<input id="common" type="text" style="width:450px;" name="common" value="" title="Separate multiple names w/ commas" autocomplete="off" />
						</div>
					</div>
					<div style="clear:both;margin:5px 0 5px 0;"><hr /></div>
					<div>
						<div style="float:left;margin-right:8px;padding-top:8px;">
							Photographers: 
						</div>
						<div style="float:left;margin-bottom:10px;">
							<input type="text" id="photographer" style="width:450px;" name="photographer" value="" title="Separate multiple photographers w/ commas" />
						</div>
					</div>
					<div style="clear:both;margin:5px 0 5px 0;"><hr /></div>
					<?php
					$tagArr = $imgLibManager->getTagArr();
					if($tagArr){
						?>
						<div>
							Image Tags: 
							<select id="tags" style="width:350px;" name="tags" >
								<option value="">Select Tag</option>
								<option value="">--------------</option>
								<?php 
								foreach($tagArr as $k){
									echo '<option value="'.$k.'" '.((array_key_exists('tags',$previousCriteria))&&($previousCriteria['tags'] === $k)?'SELECTED ':'').'>'.$k.'</option>';
								}
								?>
							</select>
						</div>
						<?php
					}
					?>
					<div style="margin-top:5px;">
						<div style="float:left;margin-right:8px;padding-top:8px;">
							Image Keywords: 
						</div>
						<div style="float:left;margin-bottom:10px;">
							<input type="text" id="keywords" style="width:350px;" name="keywords" value="" title="Separate multiple keywords w/ commas" />
						</div>
					</div>
                    <div style="clear:both;margin-top:5px;">
                        <div style="float:left;margin-right:8px;padding-top:8px;">
                            Date Uploaded:
                        </div>
                        <div style="float:left;margin-bottom:10px;">
                            <input type="text" id="uploaddate1" size="32" name="uploaddate1" style="width:100px;" value="<?php echo (array_key_exists('uploaddate1',$previousCriteria)?$previousCriteria['uploaddate1']:''); ?>" title="Single date or start date of range" /> -
                            <input type="text" id="uploaddate2" size="32" name="uploaddate2" style="width:100px;" value="<?php echo (array_key_exists('uploaddate2',$previousCriteria)?$previousCriteria['uploaddate2']:''); ?>" title="End date of range; leave blank if searching for single date" />
                        </div>
                    </div>
					<div style="clear:both;margin:5px 0 5px 0;"><hr /></div>
					<div style="margin-top:5px;">
						Limit Image Counts: 
						<select id="imagecount" name="imagecount">
							<option value="all" <?php echo ((array_key_exists('imagecount',$previousCriteria))&&($previousCriteria['imagecount'] === 'all')?'SELECTED ':''); ?>>All images</option>
							<option value="taxon" <?php echo ((array_key_exists('imagecount',$previousCriteria))&&($previousCriteria['imagecount'] === 'taxon')?'SELECTED ':''); ?>>One per taxon</option>
							<option value="specimen" <?php echo ((array_key_exists('imagecount',$previousCriteria))&&($previousCriteria['imagecount'] === 'specimen')?'SELECTED ':''); ?>>One per occurrence</option>
						</select>
					</div>
					<div style="margin-top:5px;">
						Image Display: 
						<select id="imagedisplay" name="imagedisplay" onchange="processImageDisplayChange();processTextParamChange();">
							<option value="thumbnail">Thumbnails</option>
							<option value="famlist">Taxa List</option>
						</select>
					</div>
					<table>
						<tr>
							<td>
								<div style="margin-top:5px;">
									<p><b>Limit Image Type:</b></p>
								</div>
								<div style="margin-top:5px;">
									<input type='radio' name='imagetype' value='all' <?php echo (((!array_key_exists('imagetype',$previousCriteria)) || (array_key_exists('imagetype',$previousCriteria) && $previousCriteria['imagetype'] === 'all'))?'CHECKED':''); ?> > All Images
								</div>
								<div style="margin-top:5px;">
									<input type='radio' name='imagetype' value='specimenonly' <?php echo ((array_key_exists('imagetype',$previousCriteria) && $previousCriteria['imagetype'] === 'specimenonly')?'CHECKED':''); ?> > Occurrence Images
								</div>
								<div style="margin-top:5px;">
									<input type='radio' name='imagetype' value='observationonly' <?php echo ((array_key_exists('imagetype',$previousCriteria) && $previousCriteria['imagetype'] === 'observationonly')?'CHECKED':''); ?> > Image Vouchered Observations
								</div>
								<div style="margin-top:5px;">
									<input type='radio' name='imagetype' value='fieldonly' <?php echo ((array_key_exists('imagetype',$previousCriteria) && $previousCriteria['imagetype'] === 'fieldonly')?'CHECKED':''); ?> > Field Images (lacking specific locality details)
								</div>
							</td>
						</tr>
					</table>
					<div><hr></div>
					<input id="taxastr" name="taxastr" type="hidden" value="<?php echo ((array_key_exists('taxastr',$previousCriteria))?$previousCriteria['taxastr']:''); ?>" />
					<input id="countrystr" name="countrystr" type="hidden" value="<?php echo ((array_key_exists('countrystr',$previousCriteria))?$previousCriteria['countrystr']:''); ?>" />
					<input id="statestr" name="statestr" type="hidden" value="<?php echo ((array_key_exists('statestr',$previousCriteria))?$previousCriteria['statestr']:''); ?>" />
					<input id="keywordstr" name="keywordstr" type="hidden" value="<?php echo ((array_key_exists('keywordstr',$previousCriteria))?$previousCriteria['keywordstr']:''); ?>" />
					<input id="phuidstr" name="phuidstr" type="hidden" value="<?php echo ((array_key_exists('phuidstr',$previousCriteria))?$previousCriteria['phuidstr']:''); ?>" />
					<input id="phjson" name="phjson" type="hidden" value='<?php echo ((array_key_exists('phjson',$previousCriteria))?$previousCriteria['phjson']:''); ?>' />
                    <input type="hidden" id="queryId" name="queryId" value='<?php echo $queryId; ?>' />
					<button id="loadimages" style='margin: 20px' name="submitaction" type="submit" value="Load Images" >Load Images</button>
					<div style="clear:both;"></div>
				</div>
				
				<div id="collectiondiv">
					<?php 
					if($specArr || $obsArr){
						?>
						<div id="specobsdiv">
							<div style="margin:0 0 10px 20px;">
								<input id="dballcb" name="db[]" class="specobs" value='all' type="checkbox" onclick="selectAll(this);" />
								Select/Deselect all Collections
							</div>
							<?php 
							if($specArr){
								echo '<button id="loadimages" style="float:right;" name="submitaction" type="submit" value="Load Images" >Load Images</button>';
								$imgLibManager->outputFullMapCollArr($dbArr,$specArr);
							}
							if($specArr && $obsArr) {
                                echo '<hr style="clear:both;margin:20px 0;"/>';
                            }
							if($obsArr){
								echo '<button id="loadimages" style="float:right;" name="submitaction" type="submit" value="Load Images" >Load Images</button>';
								$imgLibManager->outputFullMapCollArr($dbArr,$obsArr);
							}
							?>
							<div style="clear:both;"></div>
						</div>
						<?php 
					}
					?>
					<div style="clear:both;"></div>
				</div>
			</form>

            <div id="imagesdiv">
                <div id="imagebox"></div>
            </div>
		
		</div>
	</div>
	<?php 
	include(__DIR__ . '/../footer.php');
	?>
</body>
</html>
