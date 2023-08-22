<?php
require_once(__DIR__ . '/../../config/symbbase.php');
require_once(__DIR__ . '/../../classes/OccurrenceAssociations.php');
require_once(__DIR__ . '/../../classes/Sanitizer.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ' . $GLOBALS['CLIENT_ROOT'] . '/profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true));
}

$collid = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
$formSubmit = array_key_exists('formsubmit',$_POST)?htmlspecialchars($_POST['formsubmit']):'';

if(!is_numeric($collid)) {
    $collid = 0;
}

$assocHandler = new OccurrenceAssociations();
$collmeta = array();
if($collid) {
    $collmeta = $assocHandler->getCollectionMetadata($collid);
}

$isEditor = 0; 
if($GLOBALS['SYMB_UID']){
	if($GLOBALS['IS_ADMIN']){
		$isEditor = 1;
	}
	elseif($collid){
		if(array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true)){
			$isEditor = 1;
		}
	}
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
    <title>Occurrence Association Batch Build</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css?ver=20221204" rel="stylesheet" type="text/css" />
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
    <script type="text/javascript">

    </script>
</head>
<body>
    <?php
    include(__DIR__ . '/../../header.php');
    ?>
    <div class="navpath">
        <a href="../../index.php">Home</a> &gt;&gt;
        <?php
        if($collid) {
            echo '<a href="collprofiles.php?collid=' . $collid . '&emode=1">Collection Management</a> &gt;&gt; ';
        }
        ?>
        <b>Occurrence Association Manager</b>
    </div>
    <div id="innertext">
        <?php
        if($isEditor){
            if($formSubmit === 'Parse Associated Taxa'){
                $assocHandler->parseAssociatedTaxa($collid);
            }
            ?>
            <fieldset style="margin:20px;padding:15px">
                <legend><b>Associated Taxa Parsing</b></legend>
                <form name="" action="assocmanagement.php" method="post">
                    <div>
                        <?php
                        $statArr = $assocHandler->getParsingStats($collid);
                        echo '<div style="margin:10px 0;font-weight:bold;">';
                        if($collmeta){
                            $code = '';
                            $label = $collmeta['collname'];
                            if($collmeta['instcode']){
                                $code .= $collmeta['instcode'];
                            }
                            if($collmeta['collcode']){
                                $code .= ($code?'-':'') . $collmeta['collcode'];
                            }
                            if($code){
                                $label .= ' (' . $collmeta['collname'] . ')';
                            }
                            echo $label;
                        }
                        else{
                            echo 'All Collections';
                        }
                        echo '</div>';
                        echo '<div style="margin:3px"><b>Number of parsed occurrences:</b> '.$statArr['parsed'].'</div>';
                        echo '<div style="margin:3px"><b>Number of unparsed occurrences:</b> '.$statArr['unparsed'].'</div>';
                        echo '<div style="margin:3px"><b>Number of non-indexed parsing terms:</b> '.$statArr['failed'].' (from '.$statArr['failedOccur'].' occurrence records)'.'</div>';
                        ?>
                    </div>
                    <div style="margin:20px;">
                        <input name="collid" type="hidden" value="<?php echo $collid; ?>" />
                        <input name="formsubmit" type="submit" value="Parse Associated Taxa" />
                    </div>
                </form>
            </fieldset>
            <?php
        }
        else{
            echo '<div style="font-weight:bold;">ERROR: permissions failure</div>';
        }
        ?>
    </div>
    <?php
    include(__DIR__ . '/../../footer.php');
    include_once(__DIR__ . '/../../config/footer-includes.php');
    ?>
</body>
</html>
