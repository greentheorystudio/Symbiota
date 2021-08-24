<?php 
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/ImageLibraryManager.php');
include_once(__DIR__ . '/../classes/OccurrenceManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$queryId = array_key_exists('queryId',$_REQUEST)?(int)$_REQUEST['queryId']:0;
$target = array_key_exists('taxon',$_REQUEST)?trim($_REQUEST['taxon']):'';
$cntPerPage = array_key_exists('cntperpage',$_REQUEST)?(int)$_REQUEST['cntperpage']:100;
$pageNumber = array_key_exists('page',$_REQUEST)?(int)$_REQUEST['page']:1;
$stArrJson = array_key_exists('starr',$_REQUEST)?$_REQUEST['starr']:'';
$catId = array_key_exists('catid',$_REQUEST)?(int)$_REQUEST['catid']:0;

if(!$catId && isset($GLOBALS['DEFAULTCATID']) && $GLOBALS['DEFAULTCATID']) {
    $catId = (int)$GLOBALS['DEFAULTCATID'];
}

$imgLibManager = new ImageLibraryManager();
$collManager = new OccurrenceManager();

$collList = $collManager->getFullCollectionList($catId);
$specArr = ($collList['spec'] ?? null);
$obsArr = ($collList['obs'] ?? null);
$otherCatArr = $collManager->getOccurVoucherProjects();

$imageArr = array();
$taxaList = array();
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
	<script src="../js/symb/images.index.js?ver=20210810" type="text/javascript"></script>
    <script src="../js/symb/search.term.manager.js?ver=20210824" type="text/javascript"></script>
	<?php include_once(__DIR__ . '/../config/googleanalytics.php'); ?>
	<script type="text/javascript">
        $('html').hide();
        let stArr = {};
        let phArr = new Array();
        let selectedFamily = '';

        $(document).ready(function() {
			let qtaxaArr;
            $('#tabs').tabs({
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

            $('#taxainput').manifest({
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

            $('#commoninput').manifest({
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

            $('#keywordsinput').manifest({
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
                if(validateSearchTermsArr(stArr)){
                    changeImagePage("",stArr['imagedisplay'],1);
                }
                <?php
            }
            ?>

			$('#taxainput').on('manifestchange', function (event, data) {
                const taxavals = $('#taxainput').manifest('values');
                if(taxavals.length > 0){
                    document.getElementById('taxa').value = taxavals.join();
                }
                else{
                    document.getElementById('taxa').value = '';
                }
                processTaxaParamChange();
            });

            $('#commoninput').on('manifestchange', function (event, data) {
                const taxavals = $('#commoninput').manifest('values');
                if(taxavals.length > 0){
                    document.getElementById('taxa').value = taxavals.join();
                }
                else{
                    document.getElementById('taxa').value = '';
                }
                processTaxaParamChange();
            });

            $('#keywordsinput').on('manifestchange', function (event, data) {
                const keywordvals = $('#keywordsinput').manifest('values');
                if(keywordvals.length > 0){
                    document.getElementById('imagekeyword').value = keywordvals.join();
                }
                else{
                    document.getElementById('imagekeyword').value = '';
                }
                processTextParamChange();
            });
			
			$('#photographer').on('marcopoloselect', function (event, data) {
				phArr.push({name:data.name,id:data.id});
                processPhotographerChange();
			});
			
			$('#photographer').on('manifestremove',function (event, data){
				for (let i = 0; i < phArr.length; i++) {
					if(phArr[i].name === data){
						phArr.splice(i,1);
					}
				}
                processPhotographerChange();
			});
            $('html').show();
		});

        function processParamsForm(){
            const f = document.getElementById('imagesearchform');
            stArr = getSearchTermsArr();
            if(!validateSearchTermsArr(stArr)){
                alert('Please enter search criteria.');
                return false;
            }
            let formVerified = false;
            for(let h=0; h < f.length; h++){
                if(f.elements[h].name === "db[]" && f.elements[h].checked){
                    formVerified = true;
                    break;
                }
                if(f.elements[h].name === "cat[]" && f.elements[h].checked){
                    formVerified = true;
                    break;
                }
            }
            if(!formVerified){
                alert("Please choose at least one collection!");
                return false;
            }
            else{
                for(let i=0; i < f.length; i++){
                    if(f.elements[i].name === "cat[]" && f.elements[i].checked && document.getElementById('cat-' + f.elements[i].value)){
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
                changeImagePage("",stArr['imagedisplay'],1);
            }
        }
		
		function changeImagePage(taxon,view,page){
            if(!view){
                view = document.getElementById('imagedisplay').value;
            }
            document.getElementById("imagebox").innerHTML = "<p>Loading... <img src='../images/workingcircle.gif' style='width:15px;' /></p>";
            const http = new XMLHttpRequest();
            const url = "rpc/changeimagepage.php";
            const queryid = document.getElementById('queryId').value;
            const params = 'starr='+encodeURIComponent(JSON.stringify(stArr))+'&queryId='+queryid+'&page='+page+'&view='+view+'&taxon='+taxon;
            //console.log(url+'?'+params);
            http.open("POST", url, true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.onreadystatechange = function() {
                if(http.readyState === 4 && http.status === 200) {
                    if(!http.responseText) {
                        http.responseText = "<p>An error occurred retrieving records.</p>";
                    }
                    document.getElementById("imagebox").innerHTML = http.responseText;
                    if(view === 'thumbnail'){
                        document.getElementById("imagetabtext").innerHTML = 'Images';
                    }
                    else{
                        document.getElementById("imagetabtext").innerHTML = 'Taxa List';
                    }
                    document.getElementById('imagetab').style.display = "block";
                    $('#tabs').tabs({ active: 2 });
                }
            };
            http.send(params);
        }

        function setParamsForm(){
            const stArr = getSearchTermsArr();
            if(stArr['usethes']){
                document.getElementById("thes").checked = true;
            }
            if(stArr['taxontype']){
                document.getElementById("taxontype").value = stArr['taxontype'];
                processTaxonTypeChange();
            }
            if(stArr['taxa']){
                document.getElementById('taxa').value = stArr['taxa'];
                const qtaxaArr = document.getElementById('taxa').value.split(";");
                if(Nuber(document.getElementById("taxontype").value) === 1){
                    for(let i = 0; i < qtaxaArr.length; i++){
                        $('#taxainput').manifest('add',qtaxaArr[i]);
                    }
                }
                if(Nuber(document.getElementById("taxontype").value) === 5){
                    for(let i = 0; i < qtaxaArr.length; i++){
                        $('#commoninput').manifest('add',qtaxaArr[i]);
                    }
                }
            }
            if(stArr['imagedisplay']){
                document.getElementById("imagedisplay").value = stArr['imagedisplay'];
                processImageDisplayChange();
            }
            if(stArr['phuid']){
                document.getElementById("phuid").value = stArr['phuid'];
            }
            if(stArr['phjson']){
                document.getElementById("phjson").value = stArr['phjson'];
                phArr = JSON.parse(document.getElementById('phjson').value);
                for(let i = 0; i < phArr.length; i++){
                    $('#photographer').manifest('add',phArr[i].name);
                }
            }
            if(stArr['imagetag']){
                document.getElementById("imagetag").value = stArr['imagetag'];
            }
            if(stArr['imagekeyword']){
                document.getElementById("imagekeyword").value = stArr['imagekeyword'];
                const qkeywordArr = document.getElementById('imagekeyword').value.split(";");
                for(let i = 0; i < qkeywordArr.length; i++){
                    $('#keywordsinput').manifest('add',qkeywordArr[i].name);
                }
            }
            if(stArr['uploaddate1']){
                document.getElementById("uploaddate1").value = stArr['uploaddate1'];
            }
            if(stArr['uploaddate2']){
                document.getElementById("uploaddate2").value = stArr['uploaddate2'];
            }
            if(stArr['imagecount']){
                document.getElementById("imagecount").value = stArr['imagecount'];
            }
            document.getElementById("imagetypeall").checked = stArr['imagetypeall'];
            document.getElementById("imagetypespecimenonly").checked = stArr['imagetypespecimenonly'];
            document.getElementById("imagetypeobservationonly").checked = stArr['imagetypeobservationonly'];
            document.getElementById("imagetypefieldonly").checked = stArr['imagetypefieldonly'];
        }

        function processTaxonTypeChange(){
            const taxonType = Number(document.getElementById('taxontype').value);
            if(taxonType === 1){
                clearCommonNameVals();
                document.getElementById('thesdiv').style.display = "block";
                document.getElementById('commonbox').style.display = "none";
                document.getElementById('taxabox').style.display = "block";
            }
            if(taxonType === 5){
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
            let tvals = $('#taxainput').manifest('values');
            for (let i = 0; i < tvals.length; i++) {
                $('#taxainput').manifest('remove', i);
            }
            document.getElementById('taxa').value = "";
        }

        function clearCommonNameVals(){
            let vals = $('#commoninput').manifest('values');
            for (let i = 0; i < vals.length; i++) {
                $('#commoninput').manifest('remove', i);
            }
            document.getElementById('taxa').value = "";
        }

        function processPhotographerChange(){
            if(phArr.length > 0){
                const phids = [];
                for(let i = 0; i < phArr.length; i++){
                    phids.push(phArr[i].id);
                }
                document.getElementById('phuid').value = phids.join();
                document.getElementById('phjson').value = JSON.stringify(phArr);
            }
            else{
                document.getElementById('phuid').value = '';
                document.getElementById('phjson').value = '';
            }
            processTextParamChange();
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
			
			<form id="imagesearchform" action="search.php" method="post">
				<div id="criteriadiv">
					<div id="thesdiv" style="margin-left:160px;display:block;" >
						<input type='checkbox' id='thes' onchange="processTaxaParamChange();" value='1' checked /> Include Synonyms
					</div>
					<div style="clear:both;">
						<div style="float:left;">
							<select id="taxontype" onchange="processTaxonTypeChange();processTaxaParamChange();" style="padding:5px;margin:5px 10px;">
								<option id='sciname' value='1'>Family or Scientific Name</option>
								<option id='commonname' value='5'>Common Name</option>
							</select>
						</div>
						<div id="taxabox" style="float:left;margin-bottom:10px;display:block;">
							<input id="taxainput" type="text" style="width:450px;" title="Separate multiple names w/ commas" autocomplete="off" />
						</div>
						<div id="commonbox" style="margin-bottom:10px;display:none;">
							<input id="commoninput" type="text" style="width:450px;" title="Separate multiple names w/ commas" autocomplete="off" />
						</div>
					</div>
					<div style="clear:both;margin:5px 0 5px 0;"><hr /></div>
					<div>
						<div style="float:left;margin-right:8px;padding-top:8px;">
							Photographers: 
						</div>
						<div style="float:left;margin-bottom:10px;">
							<input type="text" id="photographer" style="width:450px;" title="Separate multiple photographers w/ commas" />
						</div>
					</div>
					<div style="clear:both;margin:5px 0 5px 0;"><hr /></div>
					<?php
					$tagArr = $imgLibManager->getTagArr();
					if($tagArr){
						?>
						<div>
							Image Tags: 
							<select id="imagetag" style="width:350px;" onchange="processTextParamChange();">
								<option value="">Select Tag</option>
								<option value="">--------------</option>
								<?php 
								foreach($tagArr as $k){
									echo '<option value="'.$k.'" >'.$k.'</option>';
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
							<input type="text" id="keywordsinput" style="width:350px;" title="Separate multiple keywords w/ commas" />
						</div>
					</div>
                    <div style="clear:both;margin-top:5px;">
                        <div style="float:left;margin-right:8px;padding-top:8px;">
                            Date Uploaded:
                        </div>
                        <div style="float:left;margin-bottom:10px;">
                            <input type="text" id="uploaddate1" size="32" style="width:100px;" onchange="processTextParamChange();" title="Single date or start date of range" /> -
                            <input type="text" id="uploaddate2" size="32" style="width:100px;" onchange="processTextParamChange();" title="End date of range; leave blank if searching for single date" />
                        </div>
                    </div>
					<div style="clear:both;margin:5px 0 5px 0;"><hr /></div>
					<div style="margin-top:5px;">
						Limit Image Counts: 
						<select id="imagecount" onchange="processTextParamChange();">
							<option value="all">All images</option>
							<option value="taxon">One per taxon</option>
							<option value="specimen">One per occurrence</option>
						</select>
					</div>
					<div style="margin-top:5px;">
						Image Display: 
						<select id="imagedisplay" onchange="processImageDisplayChange();processTextParamChange();">
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
									<input type='radio' id='imagetypeall' value='all' onchange="processTextParamChange();" checked> All Images
								</div>
								<div style="margin-top:5px;">
									<input type='radio' id='imagetypespecimenonly' value='specimenonly' onchange="processTextParamChange();"> Occurrence Images
								</div>
								<div style="margin-top:5px;">
									<input type='radio' id='imagetypeobservationonly' value='observationonly' onchange="processTextParamChange();"> Image Vouchered Observations
								</div>
								<div style="margin-top:5px;">
									<input type='radio' id='imagetypefieldonly' value='fieldonly' onchange="processTextParamChange();"> Field Images (lacking specific locality details)
								</div>
							</td>
						</tr>
					</table>
					<div><hr></div>
					<input id="taxa" type="hidden" value="" />
					<input id="imagekeyword" type="hidden" value="" />
					<input id="phuid" type="hidden" value="" />
					<input id="phjson" type="hidden" value='' />
                    <input type="hidden" id="queryId" value='<?php echo $queryId; ?>' />
					<button style='margin: 20px' type="button" value="Load Images" onclick="processParamsForm();">Load Images</button>
					<div style="clear:both;"></div>
				</div>
				
				<div id="collectiondiv">
					<?php 
					if($specArr || $obsArr){
						?>
						<div id="specobsdiv">
                            <div style="margin:0 0 10px 20px;">
                                <input id="dballcb" name="db[]" class="specobs" value='all' type="checkbox" onclick="selectAll(this);" checked />
                                Select/Deselect All
                            </div>
							<?php 
							if($specArr){
								echo '<button style="float:right;" type="button" value="Load Images" onclick="processParamsForm();">Load Images</button>';
                                $collManager->outputFullCollArr($specArr,false);
							}
							if($specArr && $obsArr) {
                                echo '<hr style="clear:both;margin:20px 0;"/>';
                            }
							if($obsArr){
								echo '<button style="float:right;" type="button" value="Load Images" onclick="processParamsForm();">Load Images</button>';
                                $collManager->outputFullCollArr($obsArr,false);
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
