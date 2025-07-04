<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/TPEditorManager.php');
include_once(__DIR__ . '/../../classes/TPDescEditorManager.php');
include_once(__DIR__ . '/../../classes/TPImageEditorManager.php');
include_once(__DIR__ . '/../../classes/TaxonomyUtilities.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$tid = array_key_exists('tid',$_REQUEST)?(int)$_REQUEST['tid']:0;
$taxon = array_key_exists('taxon',$_REQUEST)?$_REQUEST['taxon']: '';
$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']: '';
$tabIndex = array_key_exists('tabindex',$_REQUEST)?(int)$_REQUEST['tabindex']:0;

$tImageEditor = new TPImageEditorManager();
$tDescEditor = new TPDescEditorManager();
$tEditor = new TPEditorManager();
$taxUtilities = new TaxonomyUtilities();

$tid = $tEditor->setTid($tid?:$taxon);

$statusStr = '';
$editable = false;
if($GLOBALS['IS_ADMIN'] || array_key_exists('TaxonProfile',$GLOBALS['USER_RIGHTS'])){
	$editable = true;
}

if($editable && $action){
	if($action === 'Submit Common Name Edits'){
 		$editVernArr = array();
		if($_REQUEST['vernacularname']) {
            $editVernArr['vernacularname'] = str_replace('"', '-', $_REQUEST['vernacularname']);
        }
		if($_REQUEST['language']) {
            $editVernArr['language'] = $_REQUEST['language'];
        }
		$editVernArr['notes'] = str_replace('"', '-',$_REQUEST['notes']);
		$editVernArr['source'] = $_REQUEST['source'];
		if($_REQUEST['sortsequence']) {
            $editVernArr['sortsequence'] = $_REQUEST['sortsequence'];
        }
		if(!$taxUtilities->editVernacular($editVernArr,(int)$_REQUEST['vid'])){
            $statusStr = 'ERROR editing taxon.';
        }
	}
	elseif($action === 'Add Common Name'){
		$addVernArr = array();
		$addVernArr['vernacularname'] = str_replace('"', '-',$_REQUEST['vern']);
		if($_REQUEST['language']) {
            $addVernArr['language'] = $_REQUEST['language'];
        }
		if($_REQUEST['notes']) {
            $addVernArr['notes'] = str_replace('"', '-', $_REQUEST['notes']);
        }
		if($_REQUEST['source']) {
            $addVernArr['source'] = $_REQUEST['source'];
        }
		if($_REQUEST['sortsequence']) {
            $addVernArr['sortsequence'] = $_REQUEST['sortsequence'];
        }
		$addVernArr['username'] = $GLOBALS['PARAMS_ARR']['un'];
		$statusStr = $tEditor->addVernacular($addVernArr);
	}
	elseif($action === 'Delete Common Name'){
		$delVern = $_REQUEST['delvern'];
		$statusStr = $tEditor->deleteVernacular($delVern);
	}
	elseif($action === 'Add Description Block'){
		$statusStr = $tDescEditor->addDescriptionBlock();
	}
	elseif($action === 'Edit Description Block'){
		$statusStr = $tDescEditor->editDescriptionBlock();
	}
	elseif($action === 'Delete Description Block'){
		$statusStr = $tDescEditor->deleteDescriptionBlock();
	}
	elseif($action === 'remap'){
		$statusStr = $tDescEditor->remapDescriptionBlock($_GET['tdbid']);
	}
	elseif($action === 'Add Statement'){
		$statusStr = $tDescEditor->addStatement($_POST);
	}
	elseif($action === 'Edit Statement'){
		$statusStr = $tDescEditor->editStatement($_POST);
	}
	elseif($action === 'Delete Statement'){
		$statusStr = $tDescEditor->deleteStatement($_POST['tdsid']);
	}
	elseif($action === 'Submit Image Sort Edits'){
		$imgSortArr = array();
		foreach($_REQUEST as $sortKey => $sortValue){
			if($sortValue && strncmp($sortKey, 'imgid-', 6) === 0){
				$index = substr($sortKey,6);
                if(is_string($index) || is_int($index)){
                    $imgSortArr[$index]  = $sortValue;
                }
			}
		}
		$statusStr = $tImageEditor->editImageSort($imgSortArr);
	}
	elseif($action === 'Upload Image'){
		if($tImageEditor->loadImage($_POST)){
			$statusStr = 'Image uploaded successful';
		}
		if($tEditor->getErrorStr()){
			$statusStr .= '<br/>'.$tEditor->getErrorStr();
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
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Taxon Profile Editor</title>
    <meta name="description" content="Taxon profile editor for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css?ver=20221204" rel="stylesheet" type="text/css"/>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
	<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/tiny_mce/tiny_mce.js" type="text/javascript"></script>
	<script type="text/javascript">
        tinyMCE.init({
            mode : "textareas",
            theme_advanced_buttons1 : "bold,italic,underline,charmap,hr,outdent,indent,link,unlink,code",
            theme_advanced_buttons2 : "",
            theme_advanced_buttons3 : "",
            valid_elements: "*[*]"
        });

        document.addEventListener("DOMContentLoaded", function() {
			$("#sninput").autocomplete({
				source: function( request, response ) {
					$.getJSON( "<?php echo $GLOBALS['CLIENT_ROOT']; ?>/api/taxa/autofillsciname.php", { "term": request.term, "hideauth": 1 }, response );
				}
			},{ minLength: 3, autoFocus: true });

			$('#tabs').tabs({
				active: <?php echo $tabIndex; ?>
			});

		});

		function checkGetTidForm(f){
			if(f.taxon.value === ""){
				alert("Please enter a scientific name.");
				return false;
			}
			return true;
		}

		function submitAddImageForm(){
            const fileBox = document.getElementById("imgfile");
            const file = fileBox.files[0];
            if(file.size > <?php echo ($GLOBALS['MAX_UPLOAD_FILESIZE'] * 1000 * 1000); ?>){
				alert("The image you are trying to upload is too big. The maximum file size allowed is <?php echo $GLOBALS['MAX_UPLOAD_FILESIZE']; ?>MB");
				return false;
			}
		}

		function openOccurrenceSearch(target) {
            const occWindow = open("<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/misc/occurrencesearch.php?targetid=" + target, "occsearch", "resizable=1,scrollbars=1,width=700,height=500,left=20,top=20");
            if (occWindow.opener == null) {
                occWindow.opener = self;
            }
		}
	</script>
	<style>
		input{margin:3px;}
	</style>
</head>
<body>
	<?php
	include(__DIR__ . '/../../header.php');
	?>
	<div id="mainContainer" style="padding: 10px 15px 15px;">
		<?php
		if($tEditor->getTid()){
			if($editable){
                echo '<div style="width:98%;display:flex;justify-content:space-between;align-items:center;">';
                echo '<div>';
                if($tEditor->getSubmittedTid()){
                    echo "<div style='margin-top:5px;margin-left:10px;font-weight:bold;'>Redirected from: <i>".$tEditor->getSubmittedSciName(). '</i></div>';
                }
                echo "<div style='margin-top:15px;margin-left:10px;'><a href='../index.php?taxon=".$tEditor->getTid()."' style='color:#990000;text-decoration:none;'><b><i>".$tEditor->getSciName(). '</i></b></a> ' .$tEditor->getAuthor();
                if($tEditor->getRankId() > 140) {
                    echo "&nbsp;<a href='tpeditor.php?tid=" . $tEditor->getParentTid() . "'><i style='height:15px;width:15px;' title='Go to Parent' class='fas fa-level-up-alt'></i></a>";
                }
                echo "</div>";
                echo "<div id='family' style='margin-left:20px;margin-top:0.25em;'><b>Family:</b> ".$tEditor->getFamily()."</div>";
                echo '</div>';
                if($GLOBALS['IS_ADMIN'] || array_key_exists('Taxonomy',$GLOBALS['USER_RIGHTS'])){
                    echo "<a href='../taxonomy/taxonomyeditor.php?tid=" . $tid . "'><strong>Edit taxonomic placement</strong></a>";
                }
                echo '</div>';
                if($statusStr){
                    echo '<h3 style="color:'.(stripos($statusStr,'error') !== false?'red':'green') .';">'.$statusStr.'<h3>';
                }
				?>
				<div id="tabs" style="margin:10px;">
					<ul>
						<li><a href="#commontab"><span>Synonyms / Vernaculars</span></a></li>
				        <li><a href="tpimageeditor.php?tid=<?php echo $tEditor->getTid(); ?>"><span>Images</span></a></li>
				        <li><a href="tpimageeditor.php?tid=<?php echo $tEditor->getTid().'&cat=imagequicksort'; ?>"><span>Image Sort</span></a></li>
				        <li><a href="tpimageeditor.php?tid=<?php echo $tEditor->getTid().'&cat=imageadd'; ?>"><span>Add Image</span></a></li>
				        <li><a href="tpdesceditor.php?tid=<?php echo $tEditor->getTid().'&action='.$action; ?>"><span>Descriptions</span></a></li>
				    </ul>
					<div id="commontab">
						<?php
						$vernList = $tEditor->getVernaculars();
						?>
						<div>
							<div style="margin:10px 0;">
								<b><?php echo ($vernList?'Common Names':'No common in system'); ?></b>
								<span onclick="toggle('addvern');" title="Add a New Common Name">
									<i style="height:15px;width:15px;color:green;color:green;" class="fas fa-plus"></i>
								</span>
							</div>
							<div id="addvern" class="addvern" style="display:<?php echo ($vernList?'none':'block'); ?>;">
								<form name="addvernform" action="tpeditor.php" method="post" >
									<fieldset style="width:250px;margin:5px 0 0 20px;">
										<legend><b>New Common Name</b></legend>
										<div>
											Common Name:
											<input name="vern" style="margin-top:5px;border:inset;" type="text" />
										</div>
					    				<div>
					    					Language:
					    					<input name="language" style="margin-top:5px;border:inset;" type="text" />
					    				</div>
										<div>
											Notes:
											<input name="notes" style="margin-top:5px;border:inset;" type="text" />
										</div>
										<div>
											Source:
											<input name="source" style="margin-top:5px;border:inset;" type="text" />
										</div>
										<div>
											Sort Sequence:
											<input name="sortsequence" style="margin-top:5px;border:inset;width:40px" type="text" />
										</div>
										<div>
											<input type="hidden" name="tid" value="<?php echo $tEditor->getTid(); ?>" />
											<input id="vernsadd" name="action" style="margin-top:5px;" type="submit" value="Add Common Name" />
										</div>
									</fieldset>
								</form>
							</div>
							<?php
							foreach($vernList as $lang => $vernsList){
								?>
								<div style="width:250px;margin:5px 0 0 15px;">
									<fieldset>
						    			<legend><b><?php echo $lang; ?></b></legend>
						    			<?php
										foreach($vernsList as $vernArr){
											?>
											<div style="margin-left:10px;">
												<b><?php echo $vernArr['vernacularname']; ?></b>
												<span onclick="toggle('vid-<?php echo $vernArr['vid']; ?>');" title="Edit Common Name">
													<i style="height:15px;width:15px;" class="far fa-edit"></i>
												</span>
											</div>
											<form name="updatevern" action="tpeditor.php" method="post" style="margin-left:20px;">
												<div class='vid-<?php echo $vernArr['vid']; ?>' style='display:none;'>
													<input id='vernacularname' name='vernacularname' style='margin:2px 0 5px 15px;border:inset;' type='text' value='<?php echo $vernArr['vernacularname']; ?>' />
												</div>
												<div>
													Language: <?php echo $vernArr['language']; ?>
												</div>
												<div class='vid-<?php echo $vernArr['vid']; ?>' style='display:none;'>
													<input id='language' name='language' style='margin:2px 0 5px 15px;border:inset;' type='text' value='<?php echo $vernArr['language']; ?>' />
												</div>
												<div>
													Notes: <?php echo $vernArr['notes']; ?>
												</div>
												<div class='vid-<?php echo $vernArr['vid']; ?>' style='display:none;'>
													<input id='notes' name='notes' style='margin:2px 0 5px 15px;border:inset;' type='text' value='<?php echo $vernArr['notes'];?>' />
												</div>
												<div style=''>Source: <?php echo $vernArr['source']; ?></div>
												<div class='vid-<?php echo $vernArr['vid']; ?>' style='display:none;'>
													<input id='source' name='source' style='margin:2px 0 5px 15px;border:inset;' type='text' value='<?php echo $vernArr['source']; ?>' />
												</div>
												<div style=''>Sort Sequence: <?php echo $vernArr['sortsequence'];?></div>
												<div class='vid-<?php echo $vernArr['vid']; ?>' style='display:none;'>
													<input id='sortsequence' name='sortsequence' style='margin:2px 0 5px 15px;border:inset;width:40px;' type='text' value='<?php echo $vernArr['sortsequence']; ?>' />
												</div>
												<input type='hidden' name='vid' value='<?php echo $vernArr['vid']; ?>' />
												<input type='hidden' name='tid' value='<?php echo $tEditor->getTid();?>' />
												<div class='vid-<?php echo $vernArr['vid'];?>' style='display:none;'>
													<input id='vernssubmit' name='action' type='submit' value='Submit Common Name Edits' />
												</div>
											</form>
											<div class='vid-<?php echo $vernArr['vid']; ?>' style='display:none;margin:15px;'>
												<form id='delvern' name='delvern' action='tpeditor.php' method='post' onsubmit="return window.confirm('Are you sure you want to delete this Common Name?')">
													<input type='hidden' name='delvern' value='<?php echo $vernArr['vid']; ?>' />
													<input type='hidden' name='tid' value='<?php echo $tEditor->getTid(); ?>' />
													<input name='action' type='hidden' value='Delete Common Name' />
													<button style="margin:0;padding:2px;" type="submit">
                                                        <i style="height:15px;width:15px;" class="far fa-trash-alt"></i> Delete Common Name
                                                    </button>
												</form>
											</div>
											<?php
										}
										?>
									</fieldset>
								</div>
								<?php
							}
							?>
						</div>
						<div style="margin:30px 0;"><hr/></div>
						<fieldset style='padding:10px;margin:30px 0;width:400px;'>
					    	<legend><b>Synonyms</b></legend>
							<?php
							if($synonymArr = $tEditor->getSynonym()){
								?>
								<div style="font-weight:bold;margin-left:15px;">
									<ul>
										<?php
										foreach($synonymArr as $tidKey => $valueArr){
											 echo '<li>'.$valueArr['sciname'].'</li>';
										}
										?>
									</ul>
								</div>
								<?php
							}
							else{
								echo '<div style="margin:20px 0;"><b>No synonym links</b></div>';
							}
							?>
							<div style="margin:10px;">
								Synonym management is done in the <a href="../taxonomy/taxonomyeditor.php?tid=<?php echo $tid; ?>">Taxonomy Editor</a>.
							</div>
						</fieldset>
					</div>
				</div>
				<?php
			}
			else{
				?>
				<div style="margin:30px;">
					<h2>You must be logged in and authorized to taxon data.</h2>
					<h3>
						<?php
							echo "Click <a href='".$GLOBALS['CLIENT_ROOT']. '/profile/index.php?tid=' .$tEditor->getTid(). '&refurl=' .$GLOBALS['CLIENT_ROOT']."/taxa/profile/tpeditor.php'>here</a> to login";
						?>
					</h3>
				</div>
				<?php
			}
		}
		else{
			?>
			<div style="margin:20px;">
				<div style="font-weight:bold;">
				<?php
				if($taxon){
					echo '<i>' .ucfirst($taxon). '</i> not found in system. Check to see if spelled correctly and if so, add to system.';
				}
				else{
					echo 'Enter the scientific name you wish to edit:';
				}
				?>
				</div>
				<form name="gettidform" action="tpeditor.php" method="post" onsubmit="return checkGetTidForm(this);">
					<input id="sninput" name="taxon" value="<?php echo $taxon; ?>" size="40" />
					<input type="hidden" name="tabindex" value="<?php echo $tabIndex; ?>" />
					<input type="submit" name="action" value="Edit Taxon" />
				</form>
			</div>
			<?php
		}
		?>
	</div>
	<?php
    include_once(__DIR__ . '/../../config/footer-includes.php');
    include(__DIR__ . '/../../footer.php');
	?>
</body>
</html>
