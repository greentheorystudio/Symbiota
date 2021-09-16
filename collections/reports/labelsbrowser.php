<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/OccurrenceLabel.php');

$collid = (int)$_POST['collid'];
$hPrefix = $_POST['hprefix'];
$hMid = (int)$_POST['hmid'];
$hSuffix = $_POST['hsuffix'];
$lFooter = $_POST['lfooter'];
$columnCount = $_POST['labeltype'];
$labelformatindex = ($_POST['labelformatindex'] ?? '');
$showcatalognumbers = ((array_key_exists('catalognumbers',$_POST) && $_POST['catalognumbers'])?1:0);
$useBarcode = array_key_exists('bc',$_POST)?(int)$_POST['bc']:0;
$barcodeOnly = array_key_exists('bconly',$_POST)?(int)$_POST['bconly']:0;
$includeSpeciesAuthor = ((array_key_exists('speciesauthors',$_POST) && $_POST['speciesauthors'])?1:0);
$outputType = array_key_exists('outputtype',$_POST)?$_POST['outputtype']:'html';
$action = array_key_exists('submitaction',$_POST)?$_POST['submitaction']:'';

$hPrefix = strip_tags($hPrefix, '<br><b><u><i>');
$hSuffix = strip_tags($hSuffix, '<br><b><u><i>');
$lFooter = strip_tags($lFooter, '<br><b><u><i>');
$labelCat = $labelformatindex[0];
if($labelCat === 'g') {
    $labelCat = 'global';
}
elseif($labelCat === 'c') {
    $labelCat = 'coll';
}
elseif($labelCat === 'u') {
    $labelCat = 'user';
}
else {
    $labelCat = '';
}
$labelIndex = substr($labelformatindex,2);
if(!is_numeric($labelIndex)) {
    $labelIndex = '';
}
if(!is_numeric($columnCount) && $columnCount !== 'packet') {
    $columnCount = 2;
}

$labelManager = new OccurrenceLabel();
$labelManager->setCollid($collid);

if($outputType === 'word'){
	header('Content-Type: application/vnd.ms-word; charset=' .$GLOBALS['CHARSET']);
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('content-disposition: attachment;filename=labels.doc');
}
elseif($action === 'Export to CSV'){
	$labelManager->exportLabelCsvFile($_POST);
}
else{
	header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
}

$targetLabelFormatArr = $labelManager->getLabelFormatByID($labelCat,$labelIndex);

$isEditor = 0;
if($GLOBALS['SYMB_UID']){
	if($GLOBALS['IS_ADMIN']) {
        $isEditor = 1;
    }
	elseif(array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true)) {
        $isEditor = 1;
    }
	elseif(array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollEditor'], true)) {
        $isEditor = 1;
    }
}
?>
<html>
	<head>
		<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Labels</title>
		<style type="text/css">
			.row { display: flex; flex-wrap: nowrap; margin-left: auto; margin-right: auto;}
			.label { page-break-before: auto; page-break-inside: avoid; }
			<?php
			if($columnCount === 'packet'){
				?>
				.foldMarks1 { clear:both;padding-top:285px; }
				.foldMarks1 span { margin-left:77px; margin-right:80px; }
				.foldMarks2 { clear:both;padding-top:355px;padding-bottom:10px; }
				.foldMarks2 span { margin-left:77px; margin-right:80px; }
				.label {
					margin-left: auto;
					margin-right: auto;
					width: 500px;
					page-break-before:auto;
					page-break-inside:avoid;
				}
				.label {
					width:500px;
					margin:50px;
					padding:10px 50px;
					font-size: 80%;
				}
				.family { display:none }
				<?php
			}
			elseif((int)$columnCount !== 1){
				?>
				.label { width:<?php echo (floor(90/$columnCount)-floor($columnCount/4));?>%;padding:10pt; }
				<?php
			}
			?>
			/* Move to custom? Move to packets? */
			/* .cnBarcodeDiv { clear:both; padding-top:15px; }
			.catalogNumber { clear:both; text-align:center; }
			.otherCatalogNumbers { clear:both; text-align:center; }*/
			.label-header { clear:both; text-align: center }
			.label-footer { clear:both; text-align: center; font-weight: bold; font-size: 12pt; }
			@media print { .controls { display: none; } }
		</style>
    </head>
	<body style="background-color:#ffffff;">
		<?php
		echo '<div class="body'.(isset($targetLabelFormatArr['pageSize'])?' '.$targetLabelFormatArr['pageSize']:'').'">'  ;
		if($targetLabelFormatArr && $isEditor){
            $labelArr = $labelManager->getLabelArray($_POST['occid'], $includeSpeciesAuthor);
			$labelCnt = 0;
			$rowCnt = 0;
			foreach($labelArr as $occid => $occArr){
				if($barcodeOnly){
					if($occArr['catalognumber']){
						?>
						<div class="barcodeonly">
							<img src="getBarcode.php?bcheight=40&bctext=<?php echo $occArr['catalognumber']; ?>" />
						</div>
						<?php
						$labelCnt++;
					}
				}
				else{
					$midStr = '';
					if($hMid === 1) {
                        $midStr = $occArr['country'];
                    }
					elseif($hMid === 2) {
                        $midStr = $occArr['stateprovince'];
                    }
					elseif($hMid === 3) {
                        $midStr = $occArr['county'];
                    }
					elseif($hMid === 4) {
                        $midStr = $occArr['family'];
                    }
					$headerStr = '';
					if($hPrefix || $midStr || $hSuffix){
						$headerStrArr = array();
						$headerStrArr[] = $hPrefix;
						$headerStrArr[] = trim($midStr);
						$headerStrArr[] = $hSuffix;
						$headerStr = implode('',$headerStrArr);
					}

					$dupCnt = $_POST['q-'.$occid];
					for($i = 0;$i < $dupCnt;$i++){
						$labelCnt++;
						if($columnCount === 'packet'){
							echo '<div class="page"><div class="foldMarks1"><span style="float:left;">+</span><span style="float:right;">+</span></div>';
							echo '<div class="foldMarks2"><span style="float:left;">+</span><span style="float:right;">+</span></div>';
						}
						elseif($labelCnt%$columnCount === 1){
							if($labelCnt > 1) {
                                echo '</div>';
                            }
							echo '<div class="row">';
							$rowCnt++;
						}
						echo '<div class="label'.(isset($targetLabelFormatArr['labelDiv']['className'])?' '.$targetLabelFormatArr['labelDiv']['className']:'').'">';
						$attrStr = 'class="label-header';
						if(isset($targetLabelFormatArr['labelHeader']['className'])) {
                            $attrStr .= ' ' . $targetLabelFormatArr['labelHeader']['className'];
                        }
						$attrStr .= '"';
						if(isset($targetLabelFormatArr['labelHeader']['style']) && $targetLabelFormatArr['labelHeader']['style']) {
                            $attrStr .= ' style="' . $targetLabelFormatArr['labelHeader']['style'] . '"';
                        }
						echo '<div '.trim($attrStr).'>'.$headerStr.'</div>';
						echo $labelManager->getLabelBlock($targetLabelFormatArr['labelBlocks'],$occArr);
						if($occArr['catalognumber']){
                            if($useBarcode){
                                ?>
                                <div class="cn-barcode">
                                    <img src="getBarcode.php?bcheight=40&bctext=<?php echo $occArr['catalognumber']; ?>" />
                                </div>
                                <?php
                            }
                            elseif($showcatalognumbers){
                                ?>
                                <div class="catalog-number">
                                    <?php echo $occArr['catalognumber']; ?>
                                </div>
                                <?php
                            }
                        }
                        if($occArr['othercatalognumbers']){
                            ?>
                            <div class="other-catalog-numbers">
                                <?php echo $occArr['othercatalognumbers']; ?>
                            </div>
                            <?php
                        }
						if($lFooter) {
                            echo '<div class="label-footer" ' . (isset($targetLabelFormatArr['labelFooter']['style']) ? 'style="' . $targetLabelFormatArr['labelFooter']['style'] . '"' : '') . '>' . $lFooter . '</div>';
                        }
						if($columnCount === 'packet'){
                          echo '</div>';
                        }
						echo '</div>';
					}
				}
			}
			echo '</div>'; //Closing row
			if(!$labelCnt) {
                echo '<div style="font-weight:bold;text-size: 120%">No records were retrieved. Perhaps the quantity values were all set to 0?</div>';
            }
		}
		else{
			echo '<div style="font-weight:bold;text-size: 120%">';
			if($targetLabelFormatArr) {
                echo 'ERROR: Unable to parse JSON that defines the label format profile ';
            }
			echo '</div>';
		}
		echo '</div>';
		?>
	</body>
    <script type="text/javascript">
        let labelPage = document.querySelector('.body');

        let controls = document.createElement('div');
        controls.classList.add('controls');
        controls.style.width = '980px';
        controls.style.margin = '0 auto';
        controls.style.paddingBottom = '30px';

        let editBtn = document.createElement('button');
        editBtn.innerText = 'Edit Labels Content';
        editBtn.id = 'edit';
        editBtn.style.fontWeight = 'bold';
        editBtn.onclick = toggleEdits;

        let printBtn = document.createElement('button');
        printBtn.innerText = 'Print/Save PDF';
        printBtn.id = 'print';
        printBtn.style.marginLeft = '30px';
        printBtn.style.fontWeight = 'bold';
        printBtn.onclick = function(){
            window.print();
        };

        controls.appendChild(editBtn);
        controls.appendChild(printBtn);
        document.body.prepend(controls);

        function toggleEdits() {
            let isEditable = labelPage.contentEditable === 'true';
            if(isEditable){
                console.log(isEditable);
                labelPage.contentEditable = 'false';
                document.querySelector('#edit').innerText = 'Edit Labels Text';
                labelPage.style.border = 'none';
            }
            else{
                console.log(isEditable);
                labelPage.contentEditable = 'true';
                document.querySelector('#edit').innerText = 'Save';
                labelPage.style.border = '2px solid #03fc88';
            }
        }
    </script>
</html>
