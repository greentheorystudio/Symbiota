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
	<script type="text/javascript" src="js/shared.js?ver=20220809"></script>
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
				<li><a href="collections/index.php">Search Collections</a></li>
                <li><a href="spatial/index.php">Spatial Module</a></li>
				<li><a href="collections/misc/collprofiles.php">Collections</a></li>
				<li><a href="collections/misc/collstats.php">Collection Statistics</a></li>
				<?php
                if(isset($GLOBALS['ACTIVATE_EXSICCATI']) && $GLOBALS['ACTIVATE_EXSICCATI']){
                    echo '<li><a href="collections/exsiccati/index.php">Exsiccati Index</a></li>';
                }
                ?>
				<li>Data Publishing</li>
                <li style="margin-left:15px"><a href="collections/datasets/rsshandler.php" target="_blank">RSS Feed for Natural History Collections and Observation Projects</a></li>
                <li style="margin-left:15px"><a href="collections/datasets/datapublisher.php">Darwin Core Archives (DwC-A)</a></li>
                <?php
                if(file_exists('webservices/dwc/rss.xml')){
                    echo '<li style="margin-left:15px"><a href="webservices/dwc/rss.xml" target="_blank">DwC-A RSS Feed</a></li>';
                }
                ?>
                <li><a href="collections/misc/protectedspecies.php">Protected Species</a></li>

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
                <li><a href="checklists/index.php">Checklists</a></li>
                <li><a href="checklists/dynamicmap.php?interface=checklist">Dynamic Checklist</a></li>
                <?php
                if(isset($GLOBALS['KEY_MOD_IS_ACTIVE']) && $GLOBALS['KEY_MOD_IS_ACTIVE']){
                    echo '<li><a href="checklists/dynamicmap.php?interface=key">Dynamic Key</a></li>';
                }
                ?>
			</ul>

			<?php
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
                                <a href="admin/portalConfigurationManager.php">Portal Configurations</a>
                            </li>
                            <li>
                                <a href="admin/mappingConfigurationManager.php">Mapping Configurations</a>
                            </li>
                            <li>
                                <a href="profile/usermanagement.php">User Management</a>
                            </li>
                            <li>
                                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/misc/collmetadata.php">
                                    Create a New Collection or Observation Profile
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/management/thumbnailbuilder.php">
                                    Build Image Thumbnails
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/management/guidmapper.php">
                                    Generate GUIDs/UUIDs
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
                            <li><a href="taxa/admin/batchimageloader.php">Batch Upload Taxa Images</a></li>
                            <li>To edit the synonyms, common names, description, or images for a taxon, click on the editing link located in the upper right of each
                                <a href="taxa/admin/tpeditor.php?taxon=">Taxon Profile page</a>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>

                    <?php
                    if(isset($GLOBALS['ACTIVATE_EXSICCATI']) && $GLOBALS['ACTIVATE_EXSICCATI']){
                        ?>
                        <h3>Exsiccati</h3>
                        <ul>
                            <li><a href="collections/exsiccati/index.php">Exsiccati Index</a></li>
                        </ul>
                        <?php
                    }
                    ?>
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
