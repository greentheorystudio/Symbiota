<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/OccurrenceManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$queryId = array_key_exists('queryId',$_REQUEST)?(int)$_REQUEST['queryId']:0;
$catId = array_key_exists('catid',$_REQUEST)?(int)$_REQUEST['catid']:0;

if(!$catId && isset($GLOBALS['DEFAULTCATID']) && $GLOBALS['DEFAULTCATID']) {
    $catId = (int)$GLOBALS['DEFAULTCATID'];
}

$collManager = new OccurrenceManager();

$collList = $collManager->getFullCollectionList($catId);
$specArr = ($collList['spec'] ?? null);
$obsArr = ($collList['obs'] ?? null);
$otherCatArr = $collManager->getOccurVoucherProjects();
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
	<head>
		<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Collections Search</title>
		<link href="../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
		<link href="../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
		<link href="../css/jquery-ui.css" type="text/css" rel="stylesheet" />
		<script src="../js/jquery.js" type="text/javascript"></script>
		<script src="../js/jquery-ui.js" type="text/javascript"></script>
		<script src="../js/symb/shared.js?ver=20210621" type="text/javascript"></script>
        <script src="../js/symb/search.term.manager.js?ver=20210810" type="text/javascript"></script>
        <?php include_once(__DIR__ . '/../config/googleanalytics.php'); ?>
        <script type="text/javascript">
            const SOLRMODE = '<?php echo $GLOBALS['SOLR_MODE']; ?>';

            $('html').hide();
            $(document).ready(function() {
                initializeSearchStorage(<?php echo $queryId; ?>);
                setCollectionForms();
                $("#tabs").tabs();
                $('html').show();
            });

            function verifyCollForm(f){
                let formVerified = false;
                f.queryId.value = document.getElementById('queryId').value;
                for(let h=0; h<f.length; h++){
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
                    for(let i=0; i<f.length; i++){
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
                }
                return formVerified;
            }

            function verifyOtherCatForm(f){
                const pidElems = document.getElementsByName("pid[]");
                f.queryId.value = document.getElementById('queryId').value;
                for(let i = 0; i < pidElems.length; i++){
                    const pidElem = pidElems[i];
                    if(pidElem.checked) return true;
                }
                const clidElems = document.getElementsByName("clid[]");
                for(let i = 0; i < clidElems.length; i++){
                    const clidElem = clidElems[i];
                    if(clidElem.checked) return true;
                }
                alert("Please choose at least one search region!");
                return false;
            }
        </script>
	</head>
	<body>
	
	<?php
	include(__DIR__ . '/../header.php');
    echo '<div class="navpath">';
    echo '<a href="../index.php">Home</a> &gt;&gt; ';
    echo '<b>Collections</b>';
    echo '</div>';
	?>
	<div id="innertext">
        <div id="tabs" style="margin:0;">
			<ul>
				<?php 
				if($specArr && $obsArr) {
                    echo '<li><a href="#specobsdiv">Specimens &amp; Observations</a></li>';
                }
				if($specArr) {
                    echo '<li><a href="#specimendiv">Specimens</a></li>';
                }
				if($obsArr) {
                    echo '<li><a href="#observationdiv">Observations</a></li>';
                }
				if($otherCatArr) {
                    echo '<li><a href="#otherdiv">Federal Units</a></li>';
                }
				?>
			</ul>
			<?php 
			if($specArr && $obsArr){
				?>
				<div id="specobsdiv">
					<form name="collform1" id="collform1" action="harvestparams.php" method="post" onsubmit="return verifyCollForm(this);">
						<div style="margin:0 0 10px 20px;">
							<input id="dballcb" name="db[]" class="specobs" value='all' type="checkbox" onclick="selectAll(this);" checked />
                            Select/Deselect All
						</div>
						<?php 
						$collManager->outputFullCollArr($specArr, true);
                        echo '<hr style="clear:both;margin:20px 0;"/>';
						$collManager->outputFullCollArr($obsArr, true);
						?>
						<div style="clear:both;">&nbsp;</div>
                        <input type="hidden" name="queryId" value='' />
					</form>
				</div>
			<?php 
			}
			if($specArr){
				?>
				<div id="specimendiv">
					<form name="collform2" id="collform2" action="harvestparams.php" method="post" onsubmit="return verifyCollForm(this);">
						<div style="margin:0 0 10px 20px;">
							<input id="dballspeccb" name="db[]" class="spec" value='allspec' type="checkbox" onclick="selectAll(this);" checked />
                            Select/Deselect All
						</div>
						<?php
						$collManager->outputFullCollArr($specArr, true);
						?>
						<div style="clear:both;">&nbsp;</div>
                        <input type="hidden" name="queryId" value='' />
					</form>
				</div>
				<?php 
			}
			if($obsArr){
				?>
				<div id="observationdiv">
					<form name="collform3" id="collform3" action="harvestparams.php" method="post" onsubmit="return verifyCollForm(this);">
						<div style="margin:0 0 10px 20px;">
							<input id="dballobscb" name="db[]" class="obs" value='allobs' type="checkbox" onclick="selectAll(this);" checked />
                            Select/Deselect All
						</div>
						<?php
						$collManager->outputFullCollArr($obsArr, true);
						?>
						<div style="clear:both;">&nbsp;</div>
                        <input type="hidden" name="queryId" value='' />
					</form>
				</div>
				<?php 
			} 
			if($otherCatArr && isset($otherCatArr['titles'])){
				$catTitleArr = $otherCatArr['titles']['cat'];
				asort($catTitleArr);
				?>
				<div id="otherdiv">
					<form id="othercatform" action="harvestparams.php" method="post" onsubmit="return verifyOtherCatForm(this);">
						<?php
						foreach($catTitleArr as $catPid => $catTitle){
							?>
							<fieldset style="margin:10px;padding:10px;">
								<legend style="font-weight:bold;"><?php echo $catTitle; ?></legend>
								<div style="margin:0 15px;float:right;">
									<input type="submit" class="nextbtn searchcollnextbtn" value="Next" />
								</div>
								<?php
								$projTitleArr = $otherCatArr['titles'][$catPid]['proj'];
								asort($projTitleArr);
								foreach($projTitleArr as $pid => $projTitle){
									?>
									<div>
										<a href="#" onclick="togglePid('<?php echo $pid; ?>');return false;"><img id="plus-pid-<?php echo $pid; ?>" src="../images/plus_sm.png" /><img id="minus-pid-<?php echo $pid; ?>" src="../images/minus_sm.png" style="display:none;" /></a>
										<input id="pid-<?php echo $pid; ?>-Input" name="pid[]" type="checkbox" value="<?php echo $pid; ?>" onchange="selectAllPid(this);" />
										<b><?php echo $projTitle; ?></b>
									</div>
									<div id="pid-<?php echo $pid; ?>" style="margin:10px 15px;display:none;">
										<?php 
										$clArr = $otherCatArr[$pid];
										asort($clArr);
										foreach($clArr as $clid => $clidName){
											?>
											<div>
												<input name="clid[]" class="pid-<?php echo $pid; ?>" type="checkbox" onchange="processCollectionParamChange(this.form);" onclick="processProjCheckboxes('<?php echo $pid; ?>')" value="<?php echo $clid; ?>" />
												<?php echo $clidName; ?>
											</div>
											<?php
										} 
										?>
									</div>
									<?php
								} 
								?>
							</fieldset>
							<?php 
						}
						?>
                        <input type="hidden" name="queryId" value='' />
					</form>
				</div>
				<?php 
			}
			?>
		</div>
        <input type="hidden" id="queryId" value='<?php echo $queryId; ?>' />
	</div>
	<?php
	include(__DIR__ . '/../footer.php');
	?>
	</body>
</html>
