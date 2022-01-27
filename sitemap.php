<?php
include_once(__DIR__ . '/config/symbbase.php');
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
	<script type="text/javascript" src="js/symb/shared.js?ver=20211227"></script>
</head>
<body>
	<?php
	include(__DIR__ . '/header.php');
	?>
	<div id="innertext">
		<h2>Site Map</h2>
		<div class="pmargin">
			<h3>Collections</h3>
			<ul>
				<li><a href="collections/index.php">Search Engine</a> - search collections</li>
				<li><a href="collections/misc/collprofiles.php">Collections</a> - list of collection participating in project</li>
				<li><a href="collections/misc/collstats.php">Collection Statistics</a></li>
				<li><a href="collections/exsiccati/index.php">Exsiccati Index</a></li>
                <?php
                if(isset($GLOBALS['ACTIVATE_EXSICCATI']) && $GLOBALS['ACTIVATE_EXSICCATI']){
                    echo '<li><a href="collections/exsiccati/index.php">Exsiccati Index</a></li>';
                }
                ?>
				<li>Data Publishing</li>
                <li style="margin-left:15px"><a href="collections/datasets/rsshandler.php" target="_blank">RSS Feed for Natural History Collections and Observation Projects</a></li>
                <li style="margin-left:15px"><a href="collections/datasets/datapublisher.php">Darwin Core Archives (DwC-A)</a> - published datasets of selected collections</li>
                <?php
                if(file_exists('webservices/dwc/rss.xml')){
                    echo '<li style="margin-left:15px"><a href="webservices/dwc/rss.xml" target="_blank">DwC-A RSS Feed</a></li>';
                }
                ?>
                <li><a href="collections/misc/protectedspecies.php">Protected Species</a> - list of taxa where locality and/or taxonomic information is protected due to rare/threatened/endangered status</li>

			</ul>

			<h3>Image Library</h3>
			<ul>
				<li><a href="imagelib/index.php">Image Library</a></li>
				<li><a href="imagelib/search.php">Image Search</a></li>
				<li><a href="imagelib/contributors.php">Image Contributors</a></li>
				<li><a href="misc/usagepolicy.php">Usage Policy and Copyright Information</a></li>
			</ul>

            <h3>Additional Resources</h3>
			<ul>
                <?php
                if($smManager->hasGlossary()){
                    ?>
                    <li><a href="glossary/index.php">Glossary</a></li>
                    <?php
                }
                ?>
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
				echo '<h3>Biotic Inventory Projects</h3><ul>';
				foreach($projList as $pid => $pArr){
					echo "<li><a href='projects/index.php?pid=".$pid."'>".$pArr['name']."</a></li>\n";
					echo '<ul><li>Manager: ' .$pArr['managers']."</li></ul>\n";
				}
				echo '</ul>';
			}
			?>

            <h3>Dynamic Species Lists</h3>
			<ul>
				<li>
					<a href="checklists/dynamicmap.php?interface=checklist">
                        Checklist
					</a>
                    - dynamically build a checklist using georeferenced occurrence records
				</li>
				<li>
					<a href="checklists/dynamicmap.php?interface=key">
                        Dynamic Key
					</a>
                    - dynamically build a key using georeferenced occurrence records
				</li>
			</ul>
            <?php
            if($GLOBALS['SYMB_UID']){
                ?>
                <fieldset class="sitemapDataManagementContainer">
                    <legend><b>Management Tools</b></legend>
                    <?php
                    if($GLOBALS['IS_ADMIN']){
                        ?>
                        <h3>Administrative Tools</h3>
                        <ul>
                            <li>
                                <a href="profile/usermanagement.php">User Permissions</a>
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
                                    UUID/GUID Generator
                                </a>
                            </li>
                        </ul>
                        <?php
                    }

                    if($GLOBALS['KEY_MOD_IS_ACTIVE'] || array_key_exists('KeyAdmin',$GLOBALS['USER_RIGHTS'])){
                        ?>
                        <h3>Identification Keys</h3>
                        <ul>
                            <li>
                                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/ident/admin/index.php">Characters and Character States Editor</a>
                            </li>
                        </ul>
                        <?php
                    }
                    ?>

                    <h3>Images</h3>
                    <div class="pmargin">
                        See the Symbiota documentation on
                        <a href="http://symbiota.org/docs/image-submission-2/">Image Submission</a>
                        for an overview of how images are managed within a Symbiota data portal. Field images without
                        detailed locality information can be uploaded using the Taxon Species Profile page.
                        Occurrence images are loaded through the Occurrence Editing page or through a batch upload process
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

                    <h3>Glossary</h3>
                    <ul>
                        <li><a href="glossary/index.php">Manage Glossary</a></li>
                    </ul>

                    <h3>References</h3>
                    <ul>
                        <li><a href="references/index.php">Manage References</a></li>
                    </ul>

                    <h3>Datasets</h3>
                    <ul>
                        <li><a href="collections/datasets/index.php">Manage Datasets</a></li>
                    </ul>

                    <h3>Taxonomy</h3>
                    <ul>
                        <?php
                        if($GLOBALS['IS_ADMIN']){
                            ?>
                            <li><a href="profile/usertaxonomymanager.php">Taxonomic Interest User Permissions</a></li>
                            <?php
                        }
                        if($GLOBALS['IS_ADMIN'] || array_key_exists('Taxonomy',$GLOBALS['USER_RIGHTS'])){
                            ?>
                            <li><a href="taxa/admin/taxonomydisplay.php">Edit Taxonomic Placement</a></li>
                            <li><a href="taxa/admin/taxonomyloader.php">Add New Taxonomic Name</a></li>
                            <li><a href="taxa/admin/batchloader.php">Batch Upload a Taxonomic Data File</a></li>
                            <li><a href="taxa/admin/eolmapper.php">Encyclopedia of Life Linkage Manager</a></li>
                            <?php
                        }
                        if($GLOBALS['IS_ADMIN'] || array_key_exists('TaxonProfile',$GLOBALS['USER_RIGHTS'])){
                            ?>
                            <li>To edit the synonyms, common names, description, or images for a taxon, click on the editing link located in the upper right of each
                                <a href="taxa/admin/tpeditor.php?taxon=">Taxon Profile page</a>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>

                    <h3>Biotic Inventory Projects</h3>
                    To view the biotic inventory projects that you currently have permission to edit, visit your
                    <a href="profile/viewprofile.php">My Profile page</a>.
                    <?php
                    if($GLOBALS['IS_ADMIN']){
                        echo '<ul>';
                        echo '<li><a href="projects/index.php?newproj=1">Add a New Project</a></li>';
                        echo '</ul>';
                    }
                    ?>

                    <h3>Checklists</h3>
                    <div class="pmargin">
                        Tools for managing Checklists are available from each checklist display page.
                        Editing symbols located in the upper right of the page will display
                        editing options for that checklist. To view the checklists that you currently have
                        permission to edit, visit your <a href="profile/viewprofile.php">My Profile page</a>.
                    </div>

                    <?php
                    if(isset($GLOBALS['ACTIVATE_EXSICCATI']) && $GLOBALS['ACTIVATE_EXSICCATI']){
                        ?>
                        <h3>Exsiccati</h3>
                        <div class="pmargin">
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
                    <div class="pmargin">
                        Tools for managing data specific to a particular collection are available through the collection&#39;s profile page.
                        Clicking on a collection name in the list below will take you to this page for that given collection.
                        An additional method to reach this page is by clicking on the collection name within the occurrence search engine.
                        The editing symbol located in the upper right of Collection Profile page will open
                        the editing pane and display a list of editing options. To view the collections that you currently have
                        permission to edit, visit your <a href="profile/viewprofile.php?tabindex=1">My Profile page</a>.
                    </div>

                    <h3>Observations</h3>
                    <div class="pmargin">
                        Data management for observation projects is handled in a similar manner to what is described in the Collections paragraph above.
                        One difference is the General Observation project. This project serves two central purposes:
                        1) Allows registered users to submit a image voucherd field observation.
                        2) Allows collectors to enter their own collection data for label printing and to make the data available
                        to the collections obtaining the physical specimens through donations or exchange. Visit the
                        <a href="http://symbiota.org/docs/specimen-data-management/" target="_blank">Symbiota Documentation</a> for more information on occurrence processing capabilities.
                        Note that observation projects are not activated on all Symbiota data portals. To view the observation projects that you currently have
                        permission to edit, visit your <a href="profile/viewprofile.php?tabindex=1">My Profile page</a>.
                    </div>
                </fieldset>
                <?php
            }
            ?>
        </div>
	</div>
	<?php
		include(__DIR__ . '/footer.php');
	?>
</body>
</html>
