<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/KeyMassUpdate.php');
header('Content-Type: text/html; charset=' .$CHARSET);

if(!$SYMB_UID) {
    header('Location: ../../profile/index.php?refurl=../ident/tools/massupdate.php?' . $_SERVER['QUERY_STRING']);
}

$clid = $_REQUEST['clid'];
$taxonFilter = array_key_exists('tf',$_REQUEST)?$_REQUEST['tf']:'';
$generaOnly = array_key_exists('generaonly',$_POST)?$_POST['generaonly']:0;
$cidValue = array_key_exists('cid',$_REQUEST)?$_REQUEST['cid']:'';
$removeAttrs = array_key_exists('r',$_REQUEST)?$_REQUEST['r']: '';
$addAttrs = array_key_exists('a',$_REQUEST)?$_REQUEST['a']: '';
$langValue = array_key_exists('lang',$_REQUEST)?$_REQUEST['lang']: '';

$muManager = new KeyMassUpdate();
$muManager->setClid($clid);
if($cidValue) {
    $muManager->setCid($cidValue);
}

$isEditor = false;
if($IS_ADMIN || array_key_exists('KeyEditor',$USER_RIGHTS) || array_key_exists('KeyAdmin',$USER_RIGHTS)){
	$isEditor = true;
}

if($isEditor){
	if($removeAttrs || $addAttrs){
		$muManager->processAttributes($removeAttrs,$addAttrs);
	}
}
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Character Mass Updater</title>
	<link href="../../css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
	<link href="../../css/main.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
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
<div class='navpath'>
	<a href="../../index.php">Home</a> &gt;&gt;
	<a href="../../checklists/checklist.php?cl=<?php echo $clid; ?>">
		<b>Open Checklist</b>
	</a> &gt;&gt;
	<a href="../key.php?cl=<?php echo $clid; ?>&taxon=All+Species">
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
<div id="innertext">
	<?php
	if($clid && $isEditor){
		if(!$cidValue){
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
                    <?php
                    count($selectList);
                    ?>
                </div>
                <div style="margin: 10px 0;">
                    <input type="checkbox" name="generaonly" value="1" <?php echo ($generaOnly?'checked':''); ?> />
                    Exclude Species Rank
                </div>
                <?php
                $cList = $muManager->getCharList($taxonFilter);
                foreach($cList as $h => $charData){
                    echo "<div style='margin-top:1em;font-size:125%;font-weight:bold;'>$h</div>\n";
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
		else{
			$inheritStr = "&nbsp;<span title='State has been inherited from parent taxon'><b>(i)</b></span>";
			?>
			<div><?php echo $inheritStr; ?> = character state is inherited as true from a parent taxon (genus, family, etc)</div>
		 	<table class="styledtable" style="font-family:Arial,serif;font-size:12px;">
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
	}
	else{  
		echo '<h1>You appear not to have necessary premissions to edit character data.</h1>';
	}
	?>
</div>
<?php  
include(__DIR__ . '/../../footer.php');
?>
</body>
</html>

