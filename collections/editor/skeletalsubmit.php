<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceSkeletal.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true));
}

$collid  = (int)$_REQUEST['collid'];
$action = array_key_exists('formaction',$_REQUEST)?htmlspecialchars($_REQUEST['formaction']): '';

$skeletalManager = new OccurrenceSkeletal();

$collMap = array();

if($collid){
	$skeletalManager->setCollid($collid);
	$collMap = $skeletalManager->getCollectionMap();
}

$statusStr = '';
$isEditor = 0;
if($collid){
	if($GLOBALS['IS_ADMIN']){
		$isEditor = 1;
	}
	elseif(array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true)){
		$isEditor = 1;
	}
	elseif(array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollEditor'], true)){
		$isEditor = 1;
	}
}
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Create Skeletal Record</title>
	<link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<link href="../../css/external/jquery-ui.css?ver=20220720" type="text/css" rel="stylesheet" />
    <script src="../../js/external/all.min.js" type="text/javascript"></script>
	<script src="../../js/external/jquery.js" type="text/javascript"></script>
	<script src="../../js/external/jquery-ui.js" type="text/javascript"></script>
	<script src="../../js/collections.occurskeletalsubmit.js?ver=20221025" type="text/javascript"></script>
	<script src="../../js/shared.js?ver=20220809" type="text/javascript"></script>
</head>
<body>
	<?php
	include(__DIR__ . '/../../header.php');
	?>
	<div class='navpath'>
		<a href="../../index.php">Home</a> &gt;&gt;
		<a href="../misc/collprofiles.php?collid=<?php echo $collid; ?>&emode=1">Collection Control Panel</a> &gt;&gt;
		<b>Create Skeletal Record</b>
	</div>
	<div id="innertext">
		<div style="float:right;"><a href="#" onclick="toggle('descriptiondiv')"><b>Display Instructions</b></a></div>
		<h1><?php echo $collMap['collectionname']; ?></h1>
		<?php 
		if($statusStr){
			echo '<div style="margin:15px;color:red;">'.$statusStr.'</div>';
		}
		if($isEditor){
			?>
			<fieldset style="padding:0 15px 15px 15px;position:relative;">
				<legend>
					<b>Skeletal Data</b> 
					<a id="optionimgspan" href="#" onclick="showOptions()"><i style="height:15px;width:15px;" title="Display Options" class="fas fa-list"></i></a>
					<a id="hidespan" href="#" style="display:none;" onclick="hideOptions()">Hide</a>
					<a href="#" onclick="toggle('descriptiondiv')"><i style="height:15px;width:15px;color:green;" title="Description of Tool" class="fas fa-info-circle"></i></a>
				</legend>
				<div id="descriptiondiv" style="display:none;margin:10px;width:80%">
					<div style="margin-bottom:5px">
						This page is typically used to enter skeletal records into the system during the imaging process. Since collections are 
						commonly organized by scientific name, country, and state, it takes little extra effort for imaging teams to 
						collect this information while they are imaging specimens. The imaging team enters the basic collection 
						information shared by the batch of specimens being processed, and then each time they scan a barcode into the catalog 
						number field, a record is added to the system primed with the catalog number and skeletal data. 
					</div>
					<div style="margin-bottom:5px">
						More complete data can be entered by clicking on the catalog number, but the recommended workflow is to process the full label 
						data directly from the image of the specimen label at a later stage. An image can also be uploaded by clicking on the image 
						symbol to the right of the catalog number, but images typically need to be adjusted before they are ready for upload (e.g. resized, light balanced). 
						Furthermore, projects that store their images on remote image servers will 
						typically require separate workflows for batch processing images. Contact your project / portal manager to find out 
						the preferred way to load specimen images.
					</div>
					<div>
						Click the Display Option symbol located above scientific name to adjust field display and preferred action when a record 
						already exists for a catalog number. By default, a new record will not be created if the catalog number already exists. 
						However, a secondary option is available that will append skeletal data into empty fields of existing records. 
						Skeletal data will not copy over existing field values.
					</div>
 				</div>
				<form id="defaultform" name="defaultform" action="skeletalsubmit.php" method="post" autocomplete="off" onsubmit="return submitDefaultForm()">
					<div id="optiondiv" style="display:none;position:absolute;background-color:white;">
						<fieldset>
							<legend><b>Options</b></legend>
							<div style="font-weight:bold">Field Display:</div>
							<input type="checkbox" onclick="toggle('authordiv')" CHECKED /> Author<br/> 
							<input type="checkbox" onclick="toggle('familydiv')" CHECKED /> Family<br/> 
							<input type="checkbox" onclick="toggle('localitysecuritydiv')" CHECKED /> Locality Security<br/> 
							<input type="checkbox" onclick="toggle('countrydiv')" /> Country<br/>
							<input type="checkbox" onclick="toggle('statediv')" CHECKED /> State / Province<br/>
							<input type="checkbox" onclick="toggle('countydiv')" CHECKED /> County / Parish<br/>
							<input type="checkbox" onclick="toggle('processingstatusdiv')" /> Processing Status<br/>
							<input type="checkbox" onclick="toggle('othercatalognumbersdiv')" /> Other Catalog Numbers<br/>
							<input type="checkbox" onclick="toggle('recordedbydiv')" /> Collector<br/>
							<input type="checkbox" onclick="toggle('recordnumberdiv')" /> Collector Number<br/>
							<input type="checkbox" onclick="toggle('eventdatediv')" /> Collection Date<br/>
							<input type="checkbox" onclick="toggle('languagediv')" /> Language<br/>
							<div style="font-weight:bold">Catalog Number Match Action:</div>
							<input name="addaction" type="radio" value="1" checked /> Restrict entry if record exists <br/>
							<input name="addaction" type="radio" value="2" /> Append values to existing records
						</fieldset> 
					</div>
					<div style="position:absolute;background-color:white;top:10px;right:10px;">
						Session: <label id="minutes">00</label>:<label id="seconds">00</label><br/>
						Count: <label id="count">0</label><br/>
						Rate: <label id="rate">0</label> per hour
					</div>
					<div>
						<div style="">
							<div id="scinamediv" style="float:left"> 
								<b>Scientific Name:</b> 
								<input id="fsciname" name="sciname" type="text" value="" style="width:300px"/>
								<input id="ftidinterpreted" name="tidinterpreted" type="hidden" value="" />
							</div>
							<div id="authordiv" style="float:left"> 
								<input id="fscientificnameauthorship" name="scientificnameauthorship" type="text" value="" />
							</div>
							<?php
							if($GLOBALS['IS_ADMIN'] || isset($GLOBALS['USER_RIGHTS']['Taxonomy'])){ 
								?>
								<div style="float:left;padding:2px 3px;">
									<a href="../../taxa/taxonomy/index.php" target="_blank">
										<i style="height:15px;width:15px;color:green;" title="Add new name to taxonomic thesaurus" class="fas fa-plus"></i>
									</a>
								</div>
								<?php
							}
							?> 
							<div style="clear:both;">
								<div id="familydiv" style="float:left">
									<b>Family:</b> <input id="ffamily" name="family" type="text" tabindex="0" value="" />
								</div>
								<div id="localitysecuritydiv" style="float:left">
									<input id="flocalitysecurity" name="localitysecurity" type="checkbox" tabindex="0" value="1" />
									Protect locality details from general public
								</div>
							</div>
						</div>
						<div style="clear:both;padding-top:5px"> 
							<div id="countrydiv" style="display:none;float:left;margin:3px 3px 3px 0;">
								<b>Country:</b><br/> 
								<input id="fcountry" name="country" type="text" value="" autocomplete="off" />
							</div> 
							<div id="statediv" style="float:left;margin:3px 3px 3px 0;">
								<b>State/Province:</b><br/>
								<input id="fstateprovince" name="stateprovince" type="text" value="" autocomplete="off" onchange="localitySecurityCheck()" />
							</div> 
							<div id="countydiv" style="float:left;margin:3px 3px 3px 0;">
								<b>County/Parish:</b><br/>
								<input id="fcounty" name="county" type="text" autocomplete="off" value="" />
							</div> 
							<div id="processingstatusdiv" style="display:none;float:left;margin:3px 3px 3px 0;">
								<b>Processing Status:</b><br/>
								<select id="fprocessingstatus" name="processingstatus">
									<option>unprocessed</option>
									<option>stage 1</option>
									<option>stage 2</option>
									<option>stage 3</option>
									<option>expert required</option>
									<option>pending review-nfn</option>
									<option>pending review</option>
									<option>reviewed</option>
									<option>closed</option>
								</select>
							</div> 
						</div>
						<div style="clear:both;padding-top:5px">
							<div id="recordedbydiv" style="display:none;float:left;margin:3px 3px 3px 0;">
								<b>Collector:</b><br/> 
								<input id="frecordedby" name="recordedby" type="text" value="" />
							</div> 
							<div id="recordnumberdiv" style="display:none;float:left;margin:3px 3px 3px 0;">
								<b>Collector Number:</b><br/> 
								<input id="frecordnumber" name="recordnumber" type="text" value="" />
							</div> 
							<div id="eventdatediv" style="display:none;float:left;margin:3px 3px 3px 0;">
								<b>Date:</b><br/> 
								<input id="feventdate" name="eventdate" type="text" value="" onchange="eventDateChanged(this)" />
							</div> 
							<div id="languagediv" style="display:none;float:left;margin:3px 3px 3px 0;">
								<b>Language:</b><br/> 
								<select id="flanguage" name="language">
									<?php 
									$langArr = $skeletalManager->getLanguageArr();
									foreach($langArr as $code => $langStr){
										echo '<option value="'.$code.'" '.($code === 'en'?'selected':'').'>'.$langStr.'</option>';
									}
									?>
								</select>
							</div> 
						</div> 
						<div style="clear:both;padding:15px;">
							<div style="float:right;margin:16px 30px 0 0;">
								<input name="clearform" type="reset" value="Clear Form" style="margin-right:40px" />
							</div>
							<div style="float:left;">
								<b>Catalog Number:</b><br/>
								<input id="fcatalognumber" name="catalognumber" type="text" style="border-color:green;" />
							</div>
							<div id="othercatalognumbersdiv" style="display:none;float:left;margin:3px;">
								<b>Other Catalog Numbers:</b><br/> 
								<input id="fothercatalognumbers" name="othercatalognumbers" type="text" value="" />
							</div>
							<div style="float:left;margin:16px 3px 3px 3px;">
								<input id="fcollid" name="collid" type="hidden" value="<?php echo $collid; ?>" />
								<input name="recordsubmit" type="submit" value="Add Record" />
							</div>
						</div> 
					</div>
				</form>
			</fieldset>
			<fieldset style="padding:15px;">
				<legend><b>Records</b></legend>
				<div id="occurlistdiv"></div>
			</fieldset>
			<?php 
		}
		else if($collid){
            echo 'You are not authorized to acces this page.<br/>';
            echo 'Contact an administrator to obtain the necessary permissions.</b> ';
        }
        else{
            echo 'ERROR: collection identifier not set';
        }
		?>
	</div>
<?php 	
	include(__DIR__ . '/../../footer.php');
?>
</body>
</html>
