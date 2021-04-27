<?php
include_once(__DIR__ . '/config/symbini.php');
include_once(__DIR__ . '/classes/SiteMapManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
$submitAction = array_key_exists('submitaction',$_REQUEST)?$_REQUEST['submitaction']:'';

$smManager = new SiteMapManager();
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Site Map</title>
	<link href="css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<link href="css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<script type="text/javascript">
		function submitTaxaNoImgForm(f){
			if(f.clid.value !== ""){
				f.submit();
			}
			return false;
		}
	</script>
	<script type="text/javascript" src="js/symb/shared.js"></script>
</head>
<body>
	<?php
	include(__DIR__ . '/header.php');
	?>
	<div id="innertext">
		<h2>Site Map</h2>
		<div style="margin:10px;">
			<h3>Collections</h3>
			<ul>
				<li><a href="collections/index.php">Search Engine</a> - search collections</li>
				<li><a href="collections/misc/collprofiles.php">Collections</a> - list of collection participating in project</li>
				<li><a href="collections/misc/collstats.php">Collection Statistics</a></li>
				<li><a href="collections/exsiccati/index.php">Exsiccati Index</a></li>
				<li>Data Publishing</li>
				<li style="margin-left:15px"><a href="collections/datasets/rsshandler.php" target="_blank">RSS Feed for Natural History Collections and Observation Projects</a></li>
				<li style="margin-left:15px"><a href="collections/datasets/datapublisher.php">Darwin Core Archives (DwC-A)</a> - published datasets of selected collections</li>
				<?php
				if(file_exists('webservices/dwc/rss.xml')){
					echo '<li style="margin-left:15px;"><a href="webservices/dwc/rss.xml" target="_blank">DwC-A RSS Feed</a></li>';
				}
				?>
				<li><a href="collections/misc/rarespecies.php">Rare Species</a> - list of taxa where locality information is hidden due to rare/threatened/endangered status</li>

			</ul>

			<div style="margin-top:10px;"><h3>Image Library</h3></div>
			<ul>
				<li><a href="imagelib/index.php">Image Library</a></li>
				<li><a href="imagelib/search.php">Interactive Search Tool</a></li>
				<li><a href="imagelib/contributors.php">Image Contributors</a></li>
				<li><a href="misc/usagepolicy.php">Usage Policy and Copyright Information</a></li>
			</ul>

            <div style="margin-top:10px;"><h3>Taxonomy</h3></div>
			<ul>
				<li><a href="taxa/admin/taxonomydisplay.php">Taxonomic Tree Viewer</a></li>
				<li><a href="taxa/admin/taxonomydynamicdisplay.php">Taxonomy Explorer</a></li>
			</ul>

			<?php
			$clList = $smManager->getChecklistList((array_key_exists('ClAdmin',$GLOBALS['USER_RIGHTS'])?$GLOBALS['USER_RIGHTS']['ClAdmin']:0));
			$clAdmin = array();
			if($clList && isset($GLOBALS['USER_RIGHTS']['ClAdmin'])){
				$clAdmin = array_intersect_key($clList,array_flip($GLOBALS['USER_RIGHTS']['ClAdmin']));
			}
			$projList = $smManager->getProjectList();
			if($projList){
				echo '<div style="margin-top:10px;"><h2>Biotic Inventory Projects</h2></div><ul>';
				foreach($projList as $pid => $pArr){
					echo "<li><a href='projects/index.php?pid=".$pid."'>".$pArr['name']."</a></li>\n";
					echo '<ul><li>Manager: ' .$pArr['managers']."</li></ul>\n";
				}
				echo '</ul>';
			}
			?>

			<div style="margin-top:10px;"><h3>Dynamic Species Lists</h3></div>
			<ul>
				<li>
					<a href="checklists/dynamicmap.php?interface=checklist">
                        Checklist
					</a>
                    - dynamically build a checklist using georeferenced specimen records
				</li>
				<li>
					<a href="checklists/dynamicmap.php?interface=key">
                        Dynamic Key
					</a>
                    - dynamically build a key using georeferenced specimen records
				</li>
			</ul>

			<fieldset style="margin:30px 0 10px 10px;padding-left:25px;padding-right:15px;">
				<legend><b>Data Management Tools</b></legend>
				<?php
				if($GLOBALS['SYMB_UID']){
					if($GLOBALS['IS_ADMIN']){
						?>
						<h3>Administrative Functions (Super Admins only)</h3>
						<ul>
							<li>
								<a href="profile/usermanagement.php">User Permissions</a>
							</li>
							<li>
								<a href="profile/usertaxonomymanager.php">Taxonomic Interest User Permissions</a>
							</li>
							<li>
								<a href="collections/cleaning/taxonomycleaner.php">Global Taxonomic Name Cleaner</a>
							</li>
							<li>
								<a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/misc/collmetadata.php">
                                    Create a New Collection or Observation Profile
								</a>
							</li>
							<li>
								<a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/imagelib/admin/thumbnailbuilder.php">
                                    Thumbnail Builder Tool
								</a>
							</li>
							<li>
								<a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/admin/guidmapper.php">
                                    Collection GUID Mapper
								</a>
							</li>
						</ul>
						<?php
					}

					if($GLOBALS['KEY_MOD_IS_ACTIVE'] || array_key_exists('KeyAdmin',$GLOBALS['USER_RIGHTS'])){
						?>
						<h3>Identification Keys</h3>
						<?php
						if(!$GLOBALS['KEY_MOD_IS_ACTIVE'] && array_key_exists('KeyAdmin',$GLOBALS['USER_RIGHTS'])){
							?>
							<div style="color:red;margin-left:10px;">
                                Note: The Identification Key module is deactivated within this portal. However, you can override by activating idividual keys within the checklist administration page.
							</div>
							<?php
						}
						?>
						<ul>
							<?php
							if($GLOBALS['IS_ADMIN'] || array_key_exists('KeyAdmin',$GLOBALS['USER_RIGHTS'])){
								?>
								<li>
                                    You are authorized to access the <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/ident/admin/index.php">Characters and Character States Editor</a>
								</li>
								<?php
							}
							if($GLOBALS['IS_ADMIN'] || array_key_exists('KeyEditor',$GLOBALS['USER_RIGHTS']) || array_key_exists('KeyAdmin',$GLOBALS['USER_RIGHTS'])){
								?>
								<li>
                                    You are authorized to edit Identification Keys.
								</li>
                                <?php
                                if($clAdmin){
                                    echo '<li>';
                                    echo 'For coding characters in a table format, open the Mass-Update Editor for the following checklists. <br/>';
                                    echo '<ul>';
                                    foreach($clAdmin as $vClid => $name){
                                        echo "<li><a href='".$GLOBALS['CLIENT_ROOT']. '/ident/tools/massupdate.php?clid=' .$vClid."'>".$name. '</a></li>';
                                    }
                                    echo '</ul>';
                                    echo '</li>';
                                }
                            }
							else{
								?>
								<li>You are not authorized to edit Identification Keys</li>
								<?php
							}
							?>
						</ul>
						<?php
					}
					?>
					<h3>Images</h3>
					<div style="margin:10px;">
                        See the Symbiota documentation on
                        <a href="http://symbiota.org/docs/image-submission-2/">Image Submission</a>
                        for an overview of how images are managed within a Symbiota data portal. Field images without
                        detailed locality information can be uploaded using the Taxon Species Profile page.
                        Specimen images are loaded through the Specimen Editing page or through a batch upload process
                        established by a portal manager. Image Observations (Image Vouchers) with detailed locality information can be
                        uploaded using the link below. Note that you will need the necessary permission assignments to use this
                        feature.
                    </div>
					<ul>
						<?php
						if($GLOBALS['IS_ADMIN'] || array_key_exists('TaxonProfile',$GLOBALS['USER_RIGHTS'])){
							?>
							<li>
								<a href="taxa/admin/tpeditor.php?tabindex=1" target="_blank">
                                    Basic Field Image Submission
                                </a>
							</li>
							<?php
						}
						if($GLOBALS['IS_ADMIN'] || array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) || array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS'])){
							?>
							<li>
								<a href="collections/editor/observationsubmit.php">
                                    Image Observation Submission Module
								</a>
							</li>
							<?php
						}
						?>
					</ul>

                    <h3>References</h3>
                    <ul>
                        <li><a href="references/index.php">Manage References</a></li>
                    </ul>

					<h3>Biotic Inventory Projects</h3>
					<ul>
						<?php
						if($GLOBALS['IS_ADMIN']){
							echo '<li><a href="projects/index.php?newproj=1">Add a New Project</a></li>';
							if($projList){
								echo '<li><b>List of Current Projects</b> (click to edit)</li>';
								echo '<ul>';
								foreach($projList as $pid => $pArr){
									echo '<li><a href="'.$GLOBALS['CLIENT_ROOT'].'/projects/index.php?pid='.$pid.'&emode=1">'.$pArr['name'].'</a></li>';
								}
								echo '</ul>';
							}
							else{
								echo '<li>There are no projects in the system</li>';
							}
						}
						else{
							echo '<li>You are not authorized to edit any of the Projects</li>';
						}
						?>
					</ul>

					<h3>Taxon Profile Page</h3>
					<?php
					if($GLOBALS['IS_ADMIN'] || array_key_exists('TaxonProfile',$GLOBALS['USER_RIGHTS'])){
						?>
						<div style="margin:10px;">
                            The following Species Profile page editing features are also available to editors via an
                            editing link located in the upper right of each Species Profile page.
						</div>
						<ul>
							<li><a href="taxa/admin/tpeditor.php?taxon=">Synonyms / Common Names</a></li>
							<li><a href="taxa/admin/tpeditor.php?taxon=&tabindex=4">Text Descriptions</a></li>
							<li><a href="taxa/admin/tpeditor.php?taxon=&tabindex=1">Edit Images</a></li>
							<li style="margin-left:15px;"><a href="taxa/admin/tpeditor.php?taxon=&category=imagequicksort&tabindex=2">Edit Image Sorting Order</a></li>
							<li style="margin-left:15px;"><a href="taxa/admin/tpeditor.php?taxon=&category=imageadd&tabindex=3">Add a new image</a></li>
						</ul>
						<?php
					}
					else{
						?>
						<ul>
							<li>You are not yet authorized to edit the Taxon Profile</li>
						</ul>
						<?php
					}
					?>
					<h3>Taxonomy</h3>
					<ul>
						<?php
						if($GLOBALS['IS_ADMIN'] || array_key_exists('Taxonomy',$GLOBALS['USER_RIGHTS'])){
							?>
							<li>Edit Taxonomic Placement (use <a href="taxa/admin/taxonomydisplay.php">Taxonomic Tree Viewer)</a></li>
							<li><a href="taxa/admin/taxonomyloader.php">Add New Taxonomic Name</a></li>
							<li><a href="taxa/admin/batchloader.php">Batch Upload a Taxonomic Data File</a></li>
							<?php
							if($GLOBALS['IS_ADMIN'] || array_key_exists('Taxonomy',$GLOBALS['USER_RIGHTS'])){
								?>
								<li><a href="taxa/admin/eolmapper.php">Encyclopedia of Life Linkage Manager</a></li>
								<?php
							}
						}
						else{
							echo '<li>You are not authorized to edit taxonomy</li>';
						}
						?>
					</ul>

					<h3>Checklists</h3>
					<div style="margin:10px;">
                        Tools for managing Checklists are available from each checklist display page.
                        Editing symbols located in the upper right of the page will display
                        editing options for that checklist.
                        Below is a list of the checklists you are authorized to edit.
                    </div>
					<ul>
						<?php
						if($clAdmin){
							foreach($clAdmin as $k => $v){
								echo "<li><a href='".$GLOBALS['CLIENT_ROOT']. '/checklists/checklist.php?cl=' .$k."&emode=1'>$v</a></li>";
							}
						}
						else{
							echo '<li>You are not authorized to edit any of the Checklists</li>';
						}
						?>
					</ul>

					<?php
					if(isset($GLOBALS['ACTIVATE_EXSICCATI']) && $GLOBALS['ACTIVATE_EXSICCATI']){
						?>
						<h3>Exsiccati</h3>
						<div style="margin:10px;">
                            The Exsiccati module is activated for this portal.
                            The exsiccati index (listed below) can be browsed or searched by everyone.
                            However, to add or modify exsiccati titles or series,
                            the user must be an administrator for at least one collection.
                        </div>
						<ul>
							<li><a href="collections/exsiccati/index.php">Exsiccati Index</a></li>
						</ul>
						<?php
					}
					?>

					<h3>Collections</h3>
					<div style="margin:10px;">
                        Tools for managing data specific to a particular collection are available through the collection&#39;s profile page.
                        Clicking on a collection name in the list below will take you to this page for that given collection.
                        An additional method to reach this page is by clicking on the collection name within the specimen search engine.
                        The editing symbol located in the upper right of Collection Profile page will open
                        the editing pane and display a list of editing options.
					</div>
					<div style="margin:10px;">
						<div style="font-weight:bold;">
                            List of collections you have permissions to edit
						</div>
						<ul>
						<?php
						$smManager->setCollectionList();
						if($collList = $smManager->getCollArr()){
							foreach($collList as $k => $cArr){
								echo '<li>';
								echo '<a href="'.$GLOBALS['CLIENT_ROOT'].'/collections/misc/collprofiles.php?collid='.$k.'&emode=1">';
								echo $cArr['name'];
								echo '</a>';
								echo '</li>';
							}
						}
						else{
							echo '<li>You have no explicit editing permissions for a particular collections</li>';
						}
						?>
						</ul>
					</div>

					<h3>Observations</h3>
					<div style="margin:10px;">
                        Data management for observation projects is handled in a similar manner to what is described in the Collections paragraph above.
                        One difference is the General Observation project. This project serves two central purposes:
                        1) Allows registered users to submit a image voucherd field observation.
                        2) Allows collectors to enter their own collection data for label printing and to make the data available
                        to the collections obtaining the physical specimens through donations or exchange. Visit the
                        <a href="http://symbiota.org/docs/specimen-data-management/" target="_blank">Symbiota Documentation</a> for more information on specimen processing capabilities.
                        Note that observation projects are not activated on all Symbiota data portals.
                    </div>
					<div style="margin:10px;">
						<?php
						$obsList = $smManager->getObsArr();
						$genObsList = $smManager->getGenObsArr();
						$obsManagementStr = '';
						?>
						<div style="font-weight:bold;">
                            Observation Image Voucher Submission
						</div>
						<ul>
							<?php
							if($obsList){
								foreach($genObsList as $k => $oArr){
									?>
									<li>
										<a href="collections/editor/observationsubmit.php?collid=<?php echo $k; ?>">
											<?php echo $oArr['name']; ?>
										</a>
									</li>
									<?php
									if($oArr['isadmin']) {
                                        $obsManagementStr .= '<li><a href="collections/misc/collprofiles.php?collid=' . $k . '&emode=1">' . $oArr['name'] . "</a></li>\n";
                                    }
								}
								foreach($obsList as $k => $oArr){
									?>
									<li>
										<a href="collections/editor/observationsubmit.php?collid=<?php echo $k; ?>">
											<?php echo $oArr['name']; ?>
										</a>
									</li>
									<?php
									if($oArr['isadmin']) {
                                        $obsManagementStr .= '<li><a href="collections/misc/collprofiles.php?collid=' . $k . '&emode=1">' . $oArr['name'] . "</a></li>\n";
                                    }
								}
							}
							else{
								echo '<li>There are no Observation Projects to which you have permissions</li>';
							}
							?>
						</ul>
						<?php
						if($genObsList){
							?>
							<div style="font-weight:bold;">
                                Personal Specimen Management and Label Printing Features
							</div>
							<ul>
								<?php
								foreach($genObsList as $k => $oArr){
									?>
									<li>
										<a href="collections/misc/collprofiles.php?collid=<?php echo $k; ?>&emode=1">
											<?php echo $oArr['name']; ?>
										</a>
									</li>
									<?php
								}
								?>
							</ul>
							<?php
						}
						if($obsManagementStr){
							?>
							<div style="font-weight:bold;">
                                Observation Project Management
							</div>
							<ul>
								<?php echo $obsManagementStr; ?>
							</ul>
						<?php
						}
					?>
					</div>
					<?php
				}
				else{
					echo 'Please <a href="'.$GLOBALS['CLIENT_ROOT'].'/profile/index.php?refurl=../sitemap.php">login</a> to access editing tools.<br/>Contact a portal administrator for obtaining editing permissions.';
				}
			?>
			</fieldset>

			<h2>About Symbiota</h2>
			<ul>
				<li>
                    Schema Version <?php echo $smManager->getSchemaVersion(); ?>
				</li>
			</ul>
		</div>
	</div>
	<?php
		include(__DIR__ . '/footer.php');
	?>
</body>
</html>
