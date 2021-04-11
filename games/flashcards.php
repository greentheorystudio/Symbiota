<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/GamesManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$clid = array_key_exists('clid',$_REQUEST)?$_REQUEST['clid']:0;
$dynClid = array_key_exists('dynclid',$_REQUEST)?$_REQUEST['dynclid']:0;
$taxonFilter = array_key_exists('taxonfilter',$_REQUEST)?$_REQUEST['taxonfilter']:0;
$showCommon = array_key_exists('showcommon',$_REQUEST)?$_REQUEST['showcommon']:0;
$lang = array_key_exists('lang',$_REQUEST)?$_REQUEST['lang']:$GLOBALS['DEFAULT_LANG'];

$fcManager = new GamesManager();
$fcManager->setClid($clid);
$fcManager->setDynClid($dynClid);
$fcManager->setTaxonFilter($taxonFilter);
$fcManager->setShowCommon($showCommon);
$fcManager->setLang($lang);

$sciArr = array();
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Flash Cards</title>
	<link href="../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<link href="../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <?php include_once(__DIR__ . '/../config/googleanalytics.php'); ?>
    <script src="../js/all.min.js" type="text/javascript"></script>
	<script type="text/javascript">
        const imageArr = [];
        const sciNameArr = [];
        let toBeIdentified = [];
        let activeIndex = 0;
        let activeImageArr = [];
        let activeImageIndex = 0;
        let totalCorrect = 0;
        let totalTried = 0;
        let firstTry = true;

        function init(){
			<?php 
				$imagesArr = $fcManager->getFlashcardImages();
				if($imagesArr){
					foreach($imagesArr as $imgArr){
						if(array_key_exists('url',$imgArr)){
							$scinameStr = $imgArr['sciname'];
							if($showCommon && array_key_exists('vern',$imgArr)){
								$scinameStr .= ' ('.$imgArr['vern'].')';
							}
							$sciArr[$imgArr['tid']] = $scinameStr;
							echo 'sciNameArr.push('.$imgArr['tid'].');'."\n";
							echo 'imageArr['.$imgArr['tid'].'] = new Array("'.implode('","',$imgArr['url']).'");'."\n";
						}
					}
				}
			?>
			reset();
		}

		function reset(){
			toBeIdentified = [];
			if(sciNameArr.length === 0){
				alert("Sorry, there are no images for the species list you have defined");
			}
			else{
				toBeIdentified = sciNameArr.slice();
				document.getElementById("numtotal").innerHTML = sciNameArr.length.toString();
				document.getElementById("numcomplete").innerHTML = '0';
				document.getElementById("numcorrect").innerHTML = '0';
				insertNewImage();
			}
		}

		function insertNewImage(){
			activeIndex = toBeIdentified.shift();
			activeImageArr = imageArr[activeIndex];
			document.getElementById("activeimage").src = activeImageArr[0];
			document.getElementById("imageanchor").href = activeImageArr[0];
			activeImageIndex = 0;
			document.getElementById("imageindex").innerHTML = '1';
			document.getElementById("imagecount").innerHTML = activeImageArr.length.toString();
		}

		function nextImage(){
			activeImageIndex++;
			if(activeImageIndex >= activeImageArr.length){
				activeImageIndex = 0;
			}
			document.getElementById("activeimage").src = activeImageArr[activeImageIndex];
			document.getElementById("imageanchor").href = activeImageArr[activeImageIndex];
			document.getElementById("imageindex").innerHTML = (activeImageIndex + 1).toString();
			document.getElementById("imagecount").innerHTML = activeImageArr.length.toString();
			document.getElementById("scinameselect").options[0].selected = '1';
		}

		function checkId(idSelect){
            const idIndexSelected = idSelect.value;
            if(idIndexSelected > 0){
				totalTried++;
				if(idIndexSelected == activeIndex){
					alert("Correct! Try another");
					document.getElementById("numcomplete").innerHTML = (sciNameArr.length - toBeIdentified.length).toString();
					if(firstTry){
						totalCorrect++;
						document.getElementById("numcorrect").innerHTML = totalCorrect.toString();
					}
					firstTry = true;
					if(toBeIdentified.length > 0){
						insertNewImage();
						document.getElementById("scinameselect").value = '-1';
					}
					else{
						alert("Nothing left to identify. Hit reset to start again.");
					}
				}
				else{
					alert("Sorry, incorrect. Try Again.");
					firstTry = false;
				}
			}
		}

		function tellMe(){
            let wWidth = 900;
            if(document.getElementById('maintable').offsetWidth){
				wWidth = document.getElementById('maintable').offsetWidth*1.05;
			}
			else if(document.body.offsetWidth){
				wWidth = document.body.offsetWidth*0.9;
			}
            const newWindow = window.open("../taxa/index.php?taxon=" + activeIndex, "activetaxon", 'scrollbars=1,toolbar=1,resizable=1,width=' + (wWidth) + ',height=600,left=20,top=20');
            if (newWindow.opener == null) {
                newWindow.opener = self;
            }
			firstTry = false;
		}
	</script>
</head>

<body onload="init();">
<?php
	include(__DIR__ . '/../header.php');
	echo '<div class="navpath">';
	echo '<a href="../index.php">Home</a> &gt;&gt; ';
echo '<a href="../checklists/checklist.php?cl='.$clid.'">';
echo $fcManager->getClName();
echo '</a> &gt;&gt; ';
	echo ' <b>Flashcard Game</b>';
	echo '</div>';
	?>
	<div id='innertext'>
		<div style="width:420px;margin-left:auto;margin-right:auto;">
			<div style="width:420px;height:420px;text-align:center;">
				<div>
					<a id="imageanchor" href="">
						<img id="activeimage" src="" style="height:97%;max-width:450px" />
					</a>
				</div>
			</div>
			<div style="width:450px;text-align:center;">
				<div style="width:100%;">
					<div style="float:left;cursor:pointer;text-align:center;" onclick="insertNewImage()">
						<i style="height:15px;width:15px;" class="fas fa-step-forward"></i>
					</div>
					<div id="rightarrow" style="float:right;cursor:pointer;text-align:center;" onclick="nextImage()">
						<i style="height:15px;width:15px;" class="far fa-arrow-alt-circle-right"></i>
					</div>
					<div style="width:200px;margin-left:auto;margin-right:auto;">
						Image <span id="imageindex">1</span> of <span id="imagecount">?</span>
					</div>
				</div>
				<div style="clear:both;margin-top:10px;">
					<select id="scinameselect" onchange="checkId(this)">
						<option value="0">Name of Above Organism</option>
						<option value="0">-------------------------</option>
						<?php 
						asort($sciArr);
						foreach($sciArr as $t => $s){
							echo "<option value='".$t."'>".$s. '</option>';
						}
					
						?>
					</select>
				</div>
				<div style="clear:both;margin-top:10px;">
					<div>
						<b><span id="numcomplete">0</span></b> out of <b><span id="numtotal">0</span></b> Species Identified
					</div>
					<div>
						<b><span id="numcorrect">0</span></b> Identified Correctly on First Try
					</div>
				</div>
				<div style="cursor:pointer;margin-top:10px;color:green;" onclick="tellMe()"><b>Tell Me What It Is!</b></div>
				<div style="margin-left:auto;margin-right:auto;margin-top:10px;width:300px;">
					<form id="taxonfilterform" name="taxonfilterform" action="flashcards.php" method="post">
						<fieldset>
							<legend>Options</legend>
							<input type="hidden" name="clid" value="<?php echo $clid; ?>" />
							<input type="hidden" name="lang" value="<?php echo $lang; ?>" />
							<div>
								<select name="taxonfilter" onchange="document.getElementById('taxonfilterform').submit();">
									<option value="0">Filter Quiz by Taxonomic Group</option>
									<?php 
										$fcManager->echoFlashcardTaxonFilterList();
									?>
								</select>
							</div>
							<div style='margin-top:3px;'>
								<?php 
									if($GLOBALS['DISPLAY_COMMON_NAMES']){
										echo '<input id="showcommon" name="showcommon" type="checkbox" value="1" '.($showCommon? 'checked' : '').' onchange="document.getElementById(\'taxonfilterform\').submit();"/> Display Common Names'."\n";
									}
								?>
							</div>
						</fieldset>
					</form>
				</div>
				<div style="cursor:pointer;color:red;" onclick="reset()"><b>Reset Game</b></div>
			</div>
		</div>
	</div>
	<?php
	include(__DIR__ . '/../footer.php');
	?>
</body>
</html>
