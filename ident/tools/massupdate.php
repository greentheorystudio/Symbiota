<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/KeyMassUpdate.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .SanitizerService::getCleanedRequestPath(true));
}

$clid = (int)$_REQUEST['clid'];
$taxonFilter = array_key_exists('tf',$_REQUEST)?$_REQUEST['tf']:'';
$generaOnly = array_key_exists('generaonly',$_POST)?(int)$_POST['generaonly']:0;
$cidValue = array_key_exists('cid',$_REQUEST)?(int)$_REQUEST['cid']:0;
$removeAttrs = array_key_exists('r',$_REQUEST)?$_REQUEST['r']: '';
$addAttrs = array_key_exists('a',$_REQUEST)?$_REQUEST['a']: '';
$langValue = array_key_exists('lang',$_REQUEST)?$_REQUEST['lang']: '';

$muManager = new KeyMassUpdate();
$muManager->setClid($clid);
if($cidValue) {
    $muManager->setCid($cidValue);
}

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || array_key_exists('KeyEditor',$GLOBALS['USER_RIGHTS']) || array_key_exists('KeyAdmin',$GLOBALS['USER_RIGHTS'])){
	$isEditor = true;
}

if($isEditor){
	if($removeAttrs || $addAttrs){
		$muManager->processAttributes($removeAttrs,$addAttrs);
	}
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Character Mass Updater</title>
    <meta name="description" content="Identification key character mass updater for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    <script>
        let addStr = ";";
        let removeStr = ";";

        function addAttr(target){
            const indexOfAdd = addStr.indexOf(";" + target + ";");
            if(indexOfAdd === -1){
				addStr += target + ";";
			}
			else{
				removeAttr(target);
			}
		}
		
		function removeAttr(target){
            const indexOfRemove = removeStr.indexOf(";" + target + ";");
            if(indexOfRemove === -1){
				removeStr += target + ";";
			}
			else{
				addAttr(target);
			}
		}
	
		function submitAttrs(){
            let newInput;
            const sform = document.submitform;
            let a;
            let r;
            let submitForm = false;

            if(addStr.length > 1){
                const addAttrs = addStr.split(";");
                for(a in addAttrs){
                    const addValue = addAttrs[a];
                    if(addValue.length > 1){
                        newInput = document.createElement("input");
                        newInput.setAttribute("type","hidden");
						newInput.setAttribute("name","a[]");
						newInput.setAttribute("value",addValue);
						sform.appendChild(newInput);
					}
				}
				submitForm = true;
			}
	
			if(removeStr.length > 1){
                const removeAttrs = removeStr.split(";");
                for(r in removeAttrs){
                    const removeValue = removeAttrs[r];
                    if(removeValue.length > 1){
                        newInput = document.createElement("input");
                        newInput.setAttribute("type","hidden");
						newInput.setAttribute("name","r[]");
						newInput.setAttribute("value",removeValue);
						sform.appendChild(newInput);
					}
				}
				submitForm = true;
			}
			if(submitForm){
				sform.submit();
			}
			else{
				alert("It doesn't appear that any edits have been made");
			}
		}
	</script>
</head>
<body>
<?php 
include(__DIR__ . '/../../header.php');
?>
<div id="breadcrumbs">
	<a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php">Home</a> &gt;&gt;
	<a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/checklists/checklist.php?clid=<?php echo $clid; ?>">
		<b>Open Checklist</b>
	</a> &gt;&gt;
	<a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/key.php?clid=<?php echo $clid; ?>">
		<b>Open Key</b>
	</a>
	<?php 
	if($cidValue){
		?>
		&gt;&gt;
		<a href='massupdate.php?clid=<?php echo $clid.'&tf='.$taxonFilter.'&lang='.$langValue; ?>'>
			<b>Return to Character List</b>
		</a>
		<?php 
	}
	?>
</div>
<div id="mainContainer" style="padding: 10px 15px 15px;">
	<?php
	if($clid && $isEditor){
		if($cidValue) {
			$inheritStr = "&nbsp;<span title='State has been inherited from parent taxon'><b>(i)</b></span>";
			?>
			<div><?php echo $inheritStr; ?> = character state is inherited as true from a parent taxon (genus, family, etc)</div>
		 	<table class="styledtable" style="font-family:Arial,serif;">
				<?php
				$muManager->echoTaxaList($taxonFilter,$generaOnly);
				?>
			</table>
			<form name="submitform" action="massupdate.php" method="post">
				<input type='hidden' name='tf' value='<?php echo $taxonFilter; ?>' />
				<input type='hidden' name='cid' value='<?php echo $cidValue; ?>' />
				<input type='hidden' name='clid' value='<?php echo $clid; ?>' />
				<input type='hidden' name='lang' value='<?php echo $langValue; ?>' />
			</form>
			<?php
	 	}
		else {
			?>
			<form id="filterform" action="massupdate.php" method="post" onsubmit="return verifyFilterForm(this)">
                <div style="margin: 10px 0;">Select character to edit</div>
                <div>
                    <select name="tf">
                        <option value="">All Taxa</option>
                        <option value="">--------------------------</option>
                        <?php
                        $selectList = $muManager->getTaxaQueryList();
                        foreach($selectList as $tid => $scinameValue){
                            echo '<option value="'.$tid.'" '.($tid === $taxonFilter? 'SELECTED' : '').'>'.$scinameValue. '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div style="margin: 10px 0;">
                    <input type="checkbox" name="generaonly" value="1" <?php echo ($generaOnly?'checked':''); ?> />
                    Exclude Species Rank
                </div>
                <?php
                $cList = $muManager->getCharList($taxonFilter);
                foreach($cList as $h => $charData){
                    echo "<div style='margin-top:1em;font-weight:bold;'>$h</div>\n";
                    ksort($charData);
                    foreach($charData as $cidKey => $charValue){
                        echo '<div> <input name="cid" type="radio" value="'.$cidKey.'" onclick="this.form.submit()">'.$charValue.'</div>'."\n";
                    }
                }
                ?>
                <input type='hidden' name='clid' value='<?php echo $clid; ?>' />
                <input type="hidden" name="lang" value="<?php echo $langValue; ?>" />
			</form>
			<?php
		}
	}
	else{  
		echo '<h1>You appear not to have necessary premissions to edit character data.</h1>';
	}
	?>
</div>
<?php
include_once(__DIR__ . '/../../config/footer-includes.php');
include(__DIR__ . '/../../footer.php');
?>
</body>
</html>

