<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceSupport.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$targetId = (int)$_REQUEST['targetid'];
$collid = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
$action = array_key_exists('action',$_POST)?htmlspecialchars($_POST['action']):'';
$catalogNumber = array_key_exists('catalognumber',$_POST)?htmlspecialchars($_POST['catalognumber']):'';
$otherCatalogNumbers = array_key_exists('othercatalognumbers',$_POST)?htmlspecialchars($_POST['othercatalognumbers']):'';
$recordedBy = array_key_exists('recordedby',$_POST)?htmlspecialchars($_POST['recordedby']):'';
$recordNumber = array_key_exists('recordnumber',$_POST)?htmlspecialchars($_POST['recordnumber']):'';

$collEditorArr = array();
if(array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS'])){
	$collEditorArr = $GLOBALS['USER_RIGHTS']['CollAdmin'];
}
if(array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS'])){
	$collEditorArr = array_unique(array_merge($collEditorArr,$GLOBALS['USER_RIGHTS']['CollEditor']));
}

$occManager = new OccurrenceSupport();
$collArr = $occManager->getCollectionArr($GLOBALS['IS_ADMIN']?'all':$collEditorArr);
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Occurrence Search Tool</title>
    <meta name="description" content="Search tool for collection occurrence records in the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css?ver=20221204" rel="stylesheet" type="text/css"/>
	<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
	<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
	<script type="text/javascript">
	    function updateParentForm(occId) {
	        opener.document.getElementById("<?php echo $targetId;?>").value = occId;
            if(opener.document.getElementById("<?php echo $targetId;?>").hasAttribute("onchange")) {
                opener.document.getElementById("<?php echo $targetId;?>").onchange();
            }
	        self.close();
	        return false;
	    }

	    function verifyOccurSearchForm(f){
			if(!f.collid.value){
				alert("You must select target collection");
				return false;
			}
			if(!f.catalognumber.value && !f.othercatalognumbers.value && !f.recordedby.value && !f.recordnumber.value){
				alert("You must enter at least one search term");
				return false;
			}
			return true;
	    }

	    function linkToNewOccurrence(f){
		    if(!f.collid.value){
				alert("You must select target collection");
				return false;
		    }
		    else{
				$.ajax({
					type: "POST",
					url: "<?php echo $GLOBALS['CLIENT_ROOT']; ?>/api/occurrences/occurAddData.php",
					dataType: "json",
					data: { collid: f.collid.value }
				}).done(function( retObj ) {
					if(retObj.status === "true"){
						updateParentForm(retObj.occid);
					}
					else{
						alert("Unable to create new record due to error ("+retObj.error+"). Contact portal administrator");
					}
				});
		    }
		}
    </script>
</head>
<body style="background-color: white;">
	<div id="main-container">
		<?php 
		if($GLOBALS['IS_ADMIN'] || $collEditorArr){
			?>
			<form name="occform" action="occurrencesearch.php" method="post" onsubmit="return verifyOccurSearchForm(this)" >
				<fieldset style="width:650px;">
					<legend><b>Voucher Search Pane</b></legend>
					<div style="clear:both;padding:2px;">
						<div style="float:left;width:130px;">Target Collection:</div>
						<div style="float:left;">
							<select name="collid">
								<option value="">Select Collection</option>
								<option value="">--------------------------------</option>
								<?php
								foreach($collArr as $id => $collName){
									echo '<option value="'.$id.'" '.($id === $collid?'SELECTED':'').'>'.$collName.'</option>';
								}  
								?>
							</select>
						</div>
					</div>
					<div style="clear:both;padding:2px;">
						<div style="float:left;width:130px;">Catalog #:</div>
						<div style="float:left;"><input name="catalognumber" type="text" /></div>
					</div>
					<div style="clear:both;padding:2px;">
						<div style="float:left;width:130px;">Other Catalog #:</div>
						<div style="float:left;"><input name="othercatalognumbers" type="text" /></div>
					</div>
					<div style="clear:both;padding:2px;">
						<div style="float:left;width:130px;">Collector Last Name:</div>
						<div style="float:left;"><input name="recordedby" type="text" /></div>
					</div>
					<div style="clear:both;padding:2px;">
						<div style="float:left;width:130px;">Collector Number:</div>
						<div style="float:left;"><input name="recordnumber" type="text" /></div>
					</div>
					<div style="clear:both;padding:2px;">
						<input name="action" type="submit" value="Search Occurrences" />
						<input type="hidden" name="targetid" value="<?php echo $targetId;?>" />
					</div>
				</fieldset>
			</form>
			<?php
			if($action){ 
				if($occArr = $occManager->getOccurrenceList($collid, $catalogNumber, $otherCatalogNumbers, $recordedBy, $recordNumber)){
					echo '<div style="margin:30px 10px;">';
					foreach($occArr as $occid => $vArr){
						?>
						<div style="margin:10px;">
							<?php echo '<b>OccId ' .$occid. ':</b> ' .$vArr['recordedby']. ' [' .($vArr['recordnumber']?:$vArr['eventdate']). ']; ' .$vArr['locality'];?>
							<div style="margin-left:10px;cursor:pointer;color:blue;" onclick="updateParentForm('<?php echo $occid;?>')">
								Select Occurrence Record
							</div>
						</div>
						<hr />
						<?php 
					}
					echo '</div>';
				}
				else{
					?>
					<div style="margin:30px 10px;">
						<b>No records were returned. Please modify your search and try again.</b> 
					</div>
					<?php 
				}
			}
			?>
			<form name="occform" action="occurrencesearch.php" method="post" onsubmit="return false" >
				<fieldset style="width:650px;padding:20px">
					<legend><b>Link to New Occurrence Record</b></legend>
					<select name="collid">
						<option value="">Select Collection</option>
						<option value="">--------------------------------</option>
						<?php
						foreach($collArr as $id => $collName){
							echo '<option value="'.$id.'" '.($id === $collid?'SELECTED':'').'>'.$collName.'</option>';
						}  
						?>
					</select>
					<button type="button" onclick="linkToNewOccurrence(this.form)">Create New Occurrence</button>
				</fieldset>
			</form>
			<?php
		}
		else{
			?>
			<div style="margin:30px 10px;">
				<b>You are not authorized to link to any collections</b> 
			</div>
			<?php 
		} 
		?> 
	</div>
    <?php
    include_once(__DIR__ . '/../../config/footer-includes.php');
    ?>
</body>
</html>
