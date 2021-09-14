<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/OccurrenceLabel.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: SAMEORIGIN');

$collid = (int)$_POST['collid'];
$lHeader = $_POST['lheading'];
$lFooter = $_POST['lfooter'];
$detIdArr = $_POST['detid'];
$speciesAuthors = ((array_key_exists('speciesauthors',$_POST) && $_POST['speciesauthors'])?1:0);
$clearQueue = ((array_key_exists('clearqueue',$_POST) && $_POST['clearqueue'])?1:0);
$action = array_key_exists('submitaction',$_POST)?$_POST['submitaction']:'';
$rowsPerPage = 3;

$labelManager = new OccurrenceLabel();
$labelManager->setCollid($collid);

$isEditor = 0;
if($GLOBALS['SYMB_UID']){
	if($GLOBALS['IS_ADMIN'] || (array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true)) || (array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollEditor'], true))){
		$isEditor = 1;
	}
}
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
	<head>
		<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Default Annotations</title>
		<style type="text/css">
			body {font-family:arial,sans-serif;}
			table.labels {page-break-before:auto;page-break-inside:avoid;border-spacing:5px;}
			table.labels td {width:<?php echo ($rowsPerPage === 1?'600px':(100/$rowsPerPage).'%'); ?>;border:1px solid black;padding:8px;}
			p.printbreak {page-break-after:always;}
			.lheader {width:100%;margin-bottom:5px;text-align:center;font:bold 9pt arial,sans-serif;}
			.scientificnamediv {clear:both;font-size:10pt;}
			.identifiedbydiv {float:left;font-size:8pt;margin-top:5px;}
			.dateidentifieddiv {float:left;font-size:8pt;}
			.identificationreferences {clear:both;font-size:8pt;margin-top:5px;}
			.identificationremarks {clear:both;font-size:8pt;margin-top:5px;}
			.lfooter {clear:both;width:100%;text-align:center;font:bold 9pt arial,sans-serif;margin-top:18px;}
		</style>
	</head>
	<body style="background-color:#ffffff;">
		<div>
			<?php 
			if($isEditor && $action) {
                $labelArr = $labelManager->getAnnoArray($_POST['detid'], $speciesAuthors);
                if($clearQueue){
                    $labelManager->clearAnnoQueue($_POST['detid']);
                }
                $labelCnt = 0;
                foreach($labelArr as $occid => $occArr){
                    $headerStr = trim($lHeader);
                    $footerStr = trim($lFooter);

                    $dupCnt = $_POST['q-'.$occid];
                    for($i = 0;$i < $dupCnt;$i++){
                        $labelCnt++;
                        if($rowsPerPage === 1 || $labelCnt%$rowsPerPage === 1) {
                            echo '<table class="labels"><tr>' . "\n";
                        }
                        ?>
                        <td style="vertical-align:top;">
                            <?php
                            if($headerStr){
                                ?>
                                <div class="lheader">
                                    <?php echo $headerStr; ?>
                                </div>
                                <?php
                            }
                            ?>
                            <div class="scientificnamediv">
                                <?php
                                if($occArr['identificationqualifier']) {
                                    echo '<span class="identificationqualifier">' . $occArr['identificationqualifier'] . '</span> ';
                                }
                                $scinameStr = $occArr['sciname'];
                                $parentAuthor = (array_key_exists('parentauthor',$occArr)?' '.$occArr['parentauthor']:'');
                                $scinameStr = str_replace(array(' sp. ', ' subsp. ', ' ssp. ', ' var. ', ' variety ', ' Variety ', ' v. ', ' f. ', ' cf. ', ' aff. '), array('</i></b>' . $parentAuthor . ' <b>sp.</b>', '</i></b>' . $parentAuthor . ' <b>subsp. <i>', '</i></b>' . $parentAuthor . ' <b>ssp. <i>', '</i></b>' . $parentAuthor . ' <b>var. <i>', '</i></b>' . $parentAuthor . ' <b>var. <i>', '</i></b>' . $parentAuthor . ' <b>var. <i>', '</i></b>' . $parentAuthor . ' <b>var. <i>', '</i></b>' . $parentAuthor . ' <b>f. <i>', '</i></b>' . $parentAuthor . ' <b>cf. <i>', '</i></b>' . $parentAuthor . ' <b>aff. <i>'), $scinameStr);
                                ?>
                                <span class="sciname">
                                    <b><i><?php echo $scinameStr; ?></i></b>
                                </span>
                                <span class="scientificnameauthorship"><?php echo $occArr['scientificnameauthorship']; ?></span>
                            </div>
                            <?php
                            if($occArr['identificationremarks']){
                                ?>
                                <div class="identificationremarks"><?php echo $occArr['identificationremarks']; ?></div>
                                <?php
                            }
                            if($occArr['identificationreferences']){
                                ?>
                                <div class="identificationreferences"><?php echo $occArr['identificationreferences']; ?></div>
                                <?php
                            }
                            if($occArr['identifiedby']){
                                ?>
                                <div class="identifiedbydiv">
                                    Determiner: <?php echo $occArr['identifiedby']; ?>
                                </div>
                                <?php
                            }
                            if($occArr['dateidentified']){
                                ?>
                                <br />
                                <div class="dateidentifieddiv">
                                    Date: <?php echo $occArr['dateidentified']; ?>
                                </div>
                                <?php
                            }
                            if($footerStr){
                                ?>
                                <div class="lfooter">
                                    <?php echo $footerStr; ?>
                                </div>
                                <?php
                            }
                            ?>
                        </td>
                        <?php
                        if($labelCnt%$rowsPerPage === 0){
                            echo '</tr></table>'."\n";
                        }
                    }
                }
                if($labelCnt%$rowsPerPage){
                    $remaining = $rowsPerPage-($labelCnt%$rowsPerPage);
                    for($i = 0;$i < $remaining;$i++){
                        echo '<td></td>';
                    }
                    echo '</tr></table>'."\n";
                }
            }
			?>
		</div>
	</body>
</html>
