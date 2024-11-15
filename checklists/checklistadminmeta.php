<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/ChecklistAdmin.php');

$clid = array_key_exists('clid',$_REQUEST)?(int)$_REQUEST['clid']:0;
$pid = array_key_exists('pid',$_REQUEST)?htmlspecialchars($_REQUEST['pid']): '';

$clManager = new ChecklistAdmin();
$clManager->setClid($clid);

$isEditor = 0;

$clArray = $clManager->getMetaData();
$defaultArr = array();
if(isset($clArray['defaultsettings']) && $clArray['defaultsettings']){
	$defaultArr = json_decode($clArray['defaultsettings'], true);
}
?>
<script type="text/javascript">
    tinyMCE.init({
        mode : "textareas",
        theme_advanced_buttons1 : "bold,italic,underline,charmap,hr,outdent,indent,link,unlink,code",
        theme_advanced_buttons2 : "",
        theme_advanced_buttons3 : ""
    });

    function validateChecklistForm(f){
		if(f.name.value === ""){
			alert("Checklist name field must have a value");
			return false;
		}
		if(f.latcentroid.value !== ""){
			if(f.longcentroid.value === ""){
				alert("If latitude has a value, longitude must also have a value");
				return false;
			}
			if(isNaN(f.latcentroid.value)){
				alert("Latitude must be strictly numeric (decimal format: e.g. 34.2343)");
				return false;
			}
			if(Math.abs(f.latcentroid.value) > 90){
				alert("Latitude values can not be greater than 90 or less than -90.");
				return false;
			}
		}
		if(f.longcentroid.value !== ""){
			if(f.latcentroid.value === ""){
				alert("If longitude has a value, latitude must also have a value");
				return false;
			}
			if(isNaN(f.longcentroid.value)){
				alert("Longitude must be strictly numeric (decimal format: e.g. -112.2343)");
				return false;
			}
			if(Math.abs(f.longcentroid.value) > 180){
				alert("Longitude values can not be greater than 180 or less than -180.");
				return false;
			}
		}
		if(isNaN(f.pointradiusmeters.value)){
			alert("Point radius must be a numeric value only");
			return false;
		}
		if(f.type){
			if(f.type.value === "rarespp" && f.locality.value === ""){
				alert("Rare species checklists must have a state value entered into the locality field");
				return false;
			}
		}
		return true;
	}

	function openSpatialInputWindow(type) {
        let mapWindow = open("../spatial/index.php?windowtype=" + type,"input","resizable=0,width=900,height=700,left=100,top=20");
        if (mapWindow.opener == null) {
            mapWindow.opener = self;
        }
        mapWindow.addEventListener('blur', function(){
            mapWindow.close();
            mapWindow = null;
        });
    }

    function processFootprintWktChange() {
        const wktValue = document.getElementById('footprintWKT').value;
        if(!wktValue && wktValue ===''){
            document.getElementById("polyDefDiv").style.display = "none";
            document.getElementById("polyNotDefDiv").style.display = "block";
        }
        else{
            document.getElementById("polyDefDiv").style.display = "block";
            document.getElementById("polyNotDefDiv").style.display = "none";
        }
    }
</script>
<?php
if(!$clid){
	?>
	<div style="width: 100%;display: flex;justify-content: flex-end;">
		<a href="#" onclick="toggle('checklistDiv')" title="Create a New Checklist"><i style="height:15px;width:15px;color:green;" class="fas fa-plus"></i></a>
	</div>
	<?php
}
?>
<div id="checklistDiv" style="display:<?php echo ($clid?'block':'none'); ?>;">
	<form id="checklisteditform" action="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/checklists/checklistadmin.php" method="post" name="editclmatadata" onsubmit="return validateChecklistForm(this)">
		<fieldset style="margin:15px;padding:10px;">
			<legend><b><?php echo ($clid?'Edit Checklist Details':'Create New Checklist'); ?></b></legend>
			<div>
				<b>Checklist Name</b><br/>
				<input type="text" name="name" style="width:95%" value="<?php echo $clManager->getClName();?>" />
			</div>
			<div>
				<b>Authors</b><br/>
				<input type="text" name="authors" style="width:95%" value="<?php echo ($clArray?$clArray['authors']:''); ?>" />
			</div>
			<?php
			if(isset($GLOBALS['USER_RIGHTS']['RareSppAdmin']) || $GLOBALS['IS_ADMIN']){
				?>
				<div>
					<b>Checklist Type</b><br/>
					<select name="type">
						<option value="static">General Checklist</option>
						<option value="rarespp" <?php echo ($clArray && $clArray['type'] === 'rarespp'?'SELECTED':'') ?>>Rare, threatened, protected species list</option>
					</select>
				</div>
			<?php
			}
			?>
			<div>
				<b>Locality</b><br/>
				<input type="text" name="locality" style="width:95%" value="<?php echo ($clArray?$clArray['locality']:''); ?>" />
			</div>
			<div>
				<b>Citation</b><br/>
				<input type="text" name="publication" style="width:95%" value="<?php echo ($clArray?$clArray['publication']:''); ?>" />
			</div>
			<div>
				<b>Abstract:</b><br/>
				<textarea name="abstract" id="abstractblock" style="width:95%" rows="3"><?php echo ($clArray?$clArray['abstract']:''); ?></textarea>
			</div>
			<div>
				<b>Notes</b><br/>
				<input type="text" name="notes" style="width:95%" value="<?php echo ($clArray?$clArray['notes']:''); ?>" />
			</div>
			<div>
				<b>More Inclusive Reference Checklist:</b><br/>
				<select name="parentclid">
					<option value="">None Selected</option>
					<option value="">----------------------------------</option>
					<?php
					$refClArr = $clManager->getReferenceChecklists();
					foreach($refClArr as $id => $name){
						echo '<option value="'.$id.'" '.($clArray && $id === $clArray['parentclid']?'SELECTED':'').'>'.$name.'</option>';
					}
					?>
				</select>
			</div>
			<div style="width:100%;">
				<div style="float:left;">
					<b>Latitude</b><br/>
					<input id="decimallatitude" type="text" name="latcentroid" style="width:110px;" value="<?php echo ($clArray?$clArray['latcentroid']:''); ?>" />
				</div>
				<div style="float:left;margin-left:15px;">
					<b>Longitude</b><br/>
					<input id="decimallongitude" type="text" name="longcentroid" style="width:110px;" value="<?php echo ($clArray?$clArray['longcentroid']:''); ?>" />
				</div>
				<div style="float:left;margin:25px 3px;">
					<a href="#" onclick="openSpatialInputWindow('input-point,radius');"><i style='width:15px;height:15px;' class="fas fa-globe"></i></a>
				</div>
				<div style="float:left;margin-left:15px;">
					<b>Point Radius (meters)</b><br/>
					<input type="text" id="pointradiusmeters" name="pointradiusmeters" style="width:110px;" value="<?php echo ($clArray?$clArray['pointradiusmeters']:''); ?>" />
				</div>
				<div style="float:left;margin:8px 0 0 25px;">
					<fieldset style="width:275px;padding:10px">
						<legend><b>Polygon Footprint</b></legend>
						<div style="float:right;margin:10px;">
							<a href="#" onclick="openSpatialInputWindow('input-polygon,wkt');" title="Create/Edit Polygon"><i style='width:15px;height:15px;' class="fas fa-globe"></i></a>
						</div>
						<div id="polyDefDiv" style="display:<?php echo ($clArray && $clArray['hasfootprintwkt']?'block':'none'); ?>;">
                            Polygon footprint defined<br/>Click globe to view/edit
						</div>
						<div id="polyNotDefDiv" style="display:<?php echo ($clArray && $clArray['hasfootprintwkt']?'none':'block'); ?>;">
                            Polygon footprint not defined<br/>Click globe to create polygon
						</div>
						<input type="hidden" id="footprintWKT" name="footprintwkt" onchange="processFootprintWktChange();" value="<?php echo ($clArray?$clArray['footprintwkt']:''); ?>" />
					</fieldset>
				</div>
			</div>
			<div style="clear:both;margin-top:5px;">
				<fieldset style="width:300px;">
					<legend><b>Default Display Settings</b></legend>
                    <div>
                        <input name='thesfilter' id='thesfilter' type='checkbox' value='1' <?php echo (($defaultArr && $defaultArr['thesfilter'])? 'checked' : ''); ?> />
                        Filter Through Thesaurus
                    </div>
                    <div>
                        <input name='showsynonyms' id='showsynonyms' type='checkbox' value='1' <?php echo (($defaultArr && $defaultArr['showsynonyms'])? 'checked' : ''); ?> />
                        Display Synonyms
                    </div>
                    <div>
						<input name='ddetails' id='ddetails' type='checkbox' value='1' <?php echo (($defaultArr && $defaultArr['ddetails'])? 'checked' : ''); ?> />
                        Show Details
					</div>
                    <div>
                        <input id='dcommon' name='dcommon' type='checkbox' value='1' <?php echo (($defaultArr && $defaultArr['dcommon'])? 'checked' : ''); ?> />
                        Display Common Names
                    </div>
					<div>
						<input name='dimages' id='dimages' type='checkbox' value='1' <?php echo (($defaultArr && $defaultArr['dimages'])? 'checked' : ''); ?> onclick="showImagesDefaultChecked(this.form);" />
                        Display as Images
					</div>
                    <?php
                    $text = '';
                    if($defaultArr && $defaultArr['dimages']) {
                        $text = 'disabled';
                    }
                    elseif($defaultArr && ($defaultArr['dvouchers'] || $defaultArr['dauthors'])) {
                        $text = 'disabled';
                    }
                    ?>
					<div>
						<input name='dvouchers' id='dvouchers' type='checkbox' value='1' <?php echo $text; ?>/>
                        Show Notes &amp; Vouchers
					</div>
					<div>
						<input name='dauthors' id='dauthors' type='checkbox' value='1' <?php echo $text; ?>/>
                        Dislay Taxon Authors
					</div>
					<div>
						<input name='dalpha' id='dalpha' type='checkbox' value='1' <?php echo ($defaultArr && $defaultArr['dalpha']? 'checked' : ''); ?> />
                        Display Taxa Alphabetically
					</div>
					<div>
						<?php
						$activateKey = (array_key_exists('KEY_MOD_IS_ACTIVE',$GLOBALS) && $GLOBALS['KEY_MOD_IS_ACTIVE']);
						if(array_key_exists('activatekey', $defaultArr)){
							$activateKey = $defaultArr['activatekey'];
						}
						?>
						<input name='activatekey' type='checkbox' value='1' <?php echo ($activateKey? 'checked' : ''); ?> />
                        Activate Identification Key
					</div>
				</fieldset>
			</div>
			<?php
            if($GLOBALS['PUBLIC_CHECKLIST']){
                ?>
                <div style="clear:both;margin-top:15px;">
                    <b>Access</b><br/>
                    <select name="access">
                        <option value="private">Private</option>
                        <option value="public" <?php echo (($clArray && $clArray['access'] === 'public') ? 'selected' : ''); ?>>Public</option>
                    </select>
                </div>
                <?php
            }
            ?>
			<div style="clear:both;float:left;margin-top:15px;">
				<?php
				if($clid){
					?>
					<input type='submit' name='submit' value='Save Edits' />
					<input type="hidden" name="submitaction" value="SubmitEdit" />
					<?php
				}
				else{
					?>
					<input type='submit' name='submit' value='Create Checklist' />
					<input type="hidden" name="submitaction" value="SubmitAdd" />
					<?php
				}
				?>
			</div>
			<input type="hidden" name="tabindex" value="1" />
			<input type='hidden' name='clid' value='<?php echo $clid; ?>' />
			<input type="hidden" name="pid" value="<?php echo $pid; ?>" />
		</fieldset>
	</form>
</div>
